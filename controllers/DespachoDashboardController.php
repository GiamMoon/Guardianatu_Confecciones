<?php
namespace Controllers;

use Model\Clientes;
use Model\Imagenes;
use Model\Tareas;
use MVC\Router;
use Intervention\Image\ImageManagerStatic as Image;

date_default_timezone_set('America/Lima');

class DespachoDashboardController{

    public static function index(Router $router) {
        isAuth();
    
        if ($_SESSION['rol'] !== '6') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }
        $fecha = $_GET["fecha"] ?? date("Y-m-d");
        $fechas = explode("-", $fecha);
        if (!checkdate($fechas[1], $fechas[2], $fechas[0])) {
            header("Location: /404");
        }
    
        // Obtener clientes según la fecha de creación
        $clientes = Clientes::whereAllFechasTareas("fecha_creacion", $fecha);

        foreach ($clientes as $supervicion) {
            $clienteID = $supervicion->id;

            // Obtener las imágenes asociadas al cliente desde la tabla imagenescliente
            $imagenesCliente = Imagenes::whereImagen("clienteImagen_id", $clienteID);

            // Asignar las imágenes al cliente
            $supervicion->imagenes = $imagenesCliente;
        }
    
        $router->render("despacho_dashboard/index", [
            "titulo" => "Despachos Pendientes",
            "clientes" => $clientes,
            "fecha" => $fecha,
        ]);
    }

    public static function despachoHecho(Router $router){
        isAuth();
        if ($_SESSION['rol'] !== '6') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }

        $fecha = $_GET["fecha"] ?? date("Y-m-d");
        $fechas = explode("-", $fecha);
        if (!checkdate($fechas[1], $fechas[2], $fechas[0])) {
            header("Location: /404");
        }
    
        // Obtener clientes según la fecha de creación
        $clientes = Clientes::whereAllFechasTareas("fecha_creacion", $fecha);
    
        foreach ($clientes as $supervicion) {
            $clienteID = $supervicion->id;

            // Obtener las imágenes asociadas al cliente desde la tabla imagenescliente
            $imagenesCliente = Imagenes::whereImagen("clienteImagen_id", $clienteID);

            // Asignar las imágenes al cliente
            $supervicion->imagenes = $imagenesCliente;
        }

        $router->render("despacho_dashboard/despacho_hecho",[
            "titulo" => "Despachos Realizados",
            "clientes" => $clientes,
            "fecha" => $fecha
        ]);

    }

    public static function confirmarEnvio(Router $router) {
        // Obtener el ID del cliente y la fecha y hora desde la solicitud POST
        $clienteID = $_POST['cliente_id'] ?? null;
        $fechaHora = $_POST['fecha_hora'] ?? null;
    
        if ($clienteID && $fechaHora) {
            // Obtener el ID del usuario de la sesión actual
            $usuarioID = $_SESSION['id'] ?? null;
    
            if (!$usuarioID) {
                echo json_encode(['error' => 'Usuario no autenticado']);
                exit;
            }
    
            // Actualizar el estado, la fecha/hora y el ID del despacho en la base de datos
            Clientes::actualizarEstadoEnvio(1, $clienteID, $fechaHora, $usuarioID);
    
            // Puedes enviar una respuesta JSON si es necesario
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Datos incompletos']);
        }
    }
    
    
    public function obtenerEstadoTarea($clienteID, $nombreTarea) {
        $tarea = Tareas::where('nombre',  $nombreTarea);

        // Verificar si se encontró la tarea
        if ($tarea) {
            // Devolver el estado de la tarea
            return $tarea->estado;
        } else {
            // La tarea no fue encontrada, puedes manejarlo de la manera que prefieras
            return 'No encontrada';
        }
    }
    


}