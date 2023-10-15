<?php

namespace Controllers;

use Model\Clientes;
use MVC\Router;
use Intervention\Image\ImageManagerStatic as Image;
use Model\Imagenes;

date_default_timezone_set('America/Lima');

class VendedorDashboardController
{
    public static function index(Router $router)
    {
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

        $fecha = $_GET["fecha"]  ?? date("Y-m-d");
        $fechas = explode("-", $fecha);
        if(!checkdate($fechas[1], $fechas[2], $fechas[0])){
            header("Location: /404");
        }

        $ventas = Clientes::whereAllFechas("usuario_id", $_SESSION["id"], "fecha_creacion", $fecha);


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
}
