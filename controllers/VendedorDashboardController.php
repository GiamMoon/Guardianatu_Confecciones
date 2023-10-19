<?php

namespace Controllers;

use MVC\Router;
use Model\Clientes;
use Model\Imagenes;
use Model\Presupuesto;
use Intervention\Image\ImageManagerStatic as Image;

date_default_timezone_set('America/Lima');

class VendedorDashboardController
{
    public static function index(Router $router)
    {
        isAuth();
        if ($_SESSION['rol'] !== '2') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }

        $cliente = new Clientes;
        $imagenes = new Imagenes;
        $alertas = [];
        $image = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $cliente->sincronizar($_POST);
            $carpetaImagenes = $_SERVER["DOCUMENT_ROOT"] . "/imagenes/";
    
            if (!is_dir($carpetaImagenes)) {
                mkdir($carpetaImagenes);
            }
    
            $alertas = array_merge($alertas, $cliente->validarNuevoCliente());
    
            if (empty($alertas)) {
                // Guardar información del cliente
                $resultadoCliente = $cliente->guardar();
        
                // Obtener el ID del cliente recién insertado
                $clienteID = $resultadoCliente['id'];
        
                // Validar y guardar imágenes asociadas con el cliente (si se proporcionan)
                if (!empty($_FILES["imagen"]["tmp_name"])) {
                    foreach ($_FILES["imagen"]["tmp_name"] as $index => $tmpName) {
                        // Obtener el nombre de la imagen y su tipo
                        $nombreImagen = md5(uniqid(rand(), true)) . ".jpg";
                        $tipoImagen = $_FILES["imagen"]["type"][$index];
                
                        // Crear objeto Imagenes
                        $imagenes = new Imagenes(['clienteImagen_id' => $clienteID, 'imagen_path' => $nombreImagen]);
                
                        // Guardar la información de la imagen en la base de datos
                        $resultadoImagen = $imagenes->guardar();
                
                        // Verificar si se guardó la información correctamente
                        if ($resultadoImagen['resultado']) {
                            // Mover el archivo a la carpeta de imágenes
                            $carpetaImagenes = $_SERVER["DOCUMENT_ROOT"] . "/imagenes/";
                            move_uploaded_file($tmpName, $carpetaImagenes . $nombreImagen);
                        } else {
                            // Manejar el error si no se pudo guardar la información de la imagen
                            $alertas = array_merge($alertas, $imagenes::getAlertas());
                        }

                        $clienteID = $resultadoCliente['id'];

                        // Guardar adelanto en la tabla adelanto
                        $adelanto = $cliente->adelanto;
        
                        // Crear objeto Presupuesto y guardar ingreso
                        $presupuesto = new Presupuesto;
                        $presupuesto->guardarIngreso($adelanto, 'Adelanto', $_SESSION["id"]);
                    }
                }
            }

        }
    
        $router->render("vendedor_dashboard/index", [
            "titulo" => "Registrar Venta",
            "cliente" => $cliente,
            "alertas" => $alertas
        ]);
    }

    public static function mis_ventas(Router $router)
    {
        isAuth();
        if ($_SESSION['rol'] !== '2') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }

        $fecha = $_GET["fecha"]  ?? date("Y-m-d H:i:s");
        $fechas = explode("-", $fecha);
        if (!checkdate(intval($fechas[1]), intval($fechas[2]), intval($fechas[0]))) {
            header("Location: /404");
        }

        $fecha = $_GET["fecha"] ?? date("Y-m-d");
$ventas = Clientes::whereAllFechas("usuario_id", $_SESSION["id"], "fecha_creacion", $fecha);



        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["confirmar_pago"])) {
            $clienteID = $_POST["cliente_id"];
        
            // Obtener el cliente desde la base de datos
            $cliente = Clientes::find($clienteID);
        
            if ($cliente) {
                // Actualizar la cantidad en la tabla presupuesto
                $presupuesto = new Presupuesto;
                $presupuesto->guardarIngreso($cliente->restantePagar, 'Confirmación de Pago', $_SESSION["id"]);
        
                // Actualizar la cantidad en la tabla clientes
                $cliente->guardar(); // Esto actualizará el valor en la base de datos
        
                // Puedes redirigir o hacer cualquier otra cosa después de confirmar el pago
                header("Location: /misventas");
                exit;
            }
        }
        foreach ($ventas as $venta) {
            $clienteID = $venta->id;
        
            // Obtener las imágenes asociadas al cliente desde la tabla imagenescliente
            $imagenesCliente = Imagenes::whereImagen("clienteImagen_id", $clienteID);

            
        
            // Asignar las imágenes al cliente
            $venta->imagenes = $imagenesCliente;

        }

        $router->render("vendedor_dashboard/mis-ventas", [
            "titulo" => "Mis Ventas",
            "ventas" => $ventas,
            "fecha" => $fecha
        ]);
    }

    public static function confirmarPagoCompleto(Router $router)
{
    isAuth();
    if ($_SESSION['rol'] !== '2') {
        // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
        header("Location: /sin_permisos");
        exit;
    }
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirmar_pago'])) {
        $clienteID = $_POST['cliente_id'];

        // Obtener información del cliente por su ID
        $cliente = Clientes::find($clienteID);

        // Verificar si el cliente existe y realizar acciones necesarias
        if ($cliente) {
            // Guardar el valor de restantePagar en Presupuesto como ingreso
            $cliente->confirmarPagoCompleto();
            $presupuesto = new Presupuesto;
            $presupuesto->guardarIngreso($cliente->restantePagar, 'Pago Completo', $_SESSION["id"]);

            // Actualizar el valor de restantePagar en la tabla de clientes
            $cliente->guardar();

            // Redirigir a la página original o mostrar un mensaje de éxito
            header("Location: /misventas");
            exit;
        } else {
            // Manejar el caso en que el cliente no exista
            // ...
        }
    } else {
        // Manejar el acceso directo a este método sin enviar el formulario
        // ...
    }
}

public static function eliminarVenta()
{
    isAuth();
    if ($_SESSION['rol'] !== '2') {
        // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
        header("Location: /sin_permisos");
        exit;
    }
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar_venta"])) {
        $clienteID = $_POST["cliente_id"];

        // Obtener el cliente desde la base de datos
        $cliente = Clientes::find($clienteID);

        if ($cliente) {
            // Eliminar las imágenes asociadas
            foreach ($cliente->imagenes as $imagen) {
                $imagen->eliminar();
            }

            // Obtener la fecha de la venta para buscar el registro en la tabla presupuesto
            $fechaVenta = $cliente->fecha_creacion;
            
            // Eliminar el cliente
            $cliente->eliminar();

            // Buscar el registro en la tabla presupuesto y eliminarlo
            $presupuesto = Presupuesto::consultarSQLPre("SELECT * FROM presupuesto WHERE fecha = ?", [$fechaVenta]);
            if ($presupuesto) {
                $presupuesto = reset($presupuesto);
                $presupuesto->eliminar();
            }

            // Puedes redirigir a otra página o realizar otras acciones después de eliminar
            header("Location: /misventas");
            exit;
        }
    }

    // Redirigir a una página de error si el cliente no se encuentra o hay un error
    header("Location: /404");
}
}
