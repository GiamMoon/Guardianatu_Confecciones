<?php
namespace Controllers;

use MVC\Router;
use Model\Tareas;
use Model\Clientes;
use Model\Imagenes;
use Intervention\Image\ImageManagerStatic as Image;

date_default_timezone_set('America/Lima');

class SupervisorDashboardController{

    public static function index(Router $router){

        isAuth();
        if ($_SESSION['rol'] !== '3') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }
        $idSupervisor = $_SESSION['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['aprobar_venta'])) {
                $idVentaAprobar = $_POST['id'] ?? null;


        
                if ($idVentaAprobar !== null && is_numeric($idVentaAprobar)) {
                    // Actualiza el estado de la venta y guarda el ID del supervisor
                    Clientes::actualizarEstadoSuperEnvio(1, $idVentaAprobar, $idSupervisor);
                    header("Location: /ventas_aprobadas");
                }
            } elseif (isset($_POST['rechazar_venta'])) {
                $idVentaRechazar = $_POST['id'] ?? null;
        
                if ($idVentaRechazar !== null && is_numeric($idVentaRechazar)) {
                    // Actualiza el estado de la venta y guarda el ID del supervisor
                    Clientes::actualizarEstadoSuperEnvio(2, $idVentaRechazar, $idSupervisor);
                    header("Location: /ventas_rechazadas");
                }
            }
        }

        $fecha = $_GET["fecha"]  ?? date("Y-m-d");
        $fechas = explode("-", $fecha);
        if(!checkdate($fechas[1], $fechas[2], $fechas[0])){
            header("Location: /404");
        }

        $superviciones = Clientes::consultaSupervisorEnvio(0,$fecha);

        foreach ($superviciones as $supervicion) {
            $clienteID = $supervicion->id;

            // Obtener las imágenes asociadas al cliente desde la tabla imagenescliente
            $imagenesCliente = Imagenes::whereImagen("clienteImagen_id", $clienteID);

            // Asignar las imágenes al cliente
            $supervicion->imagenes = $imagenesCliente;

            $estadosTareas = Tareas::obtenerEstadoTareasCliente($clienteID);

            // Asignar los mensajes al cliente
            $supervicion->mensaje_tareas = $estadosTareas['mensaje_tareas'];
            $supervicion->mensaje_costura = $estadosTareas['mensaje_costura'];

           
        }

        $router->render("supervisor_dashboard/index",[
            "titulo" => "Aprobar Envio",
            "superviciones" => $superviciones,
            "fecha" => $fecha
        ]);

    }

    public static function aprobarVentas(Router $router){

        isAuth();
        if ($_SESSION['rol'] !== '3') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }
        $idSupervisor = $_SESSION['id'] ?? null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['aprobar_venta'])) {
                $idVentaAprobar = $_POST['id'] ?? null;


        
                if ($idVentaAprobar !== null && is_numeric($idVentaAprobar)) {
                    // Actualiza el estado de la venta y guarda el ID del supervisor
                    Clientes::actualizarEstado(1, $idVentaAprobar, $idSupervisor);
                    header("Location: /supervisor_dashboard");
                }
            } elseif (isset($_POST['rechazar_venta'])) {
                $idVentaRechazar = $_POST['id'] ?? null;
        
                if ($idVentaRechazar !== null && is_numeric($idVentaRechazar)) {
                    // Actualiza el estado de la venta y guarda el ID del supervisor
                    Clientes::actualizarEstado(2, $idVentaRechazar, $idSupervisor);
                    header("Location: /ventas_rechazadas");
                }
            }
        }

        $fecha = $_GET["fecha"]  ?? date("Y-m-d");
        $fechas = explode("-", $fecha);
        if(!checkdate($fechas[1], $fechas[2], $fechas[0])){
            header("Location: /404");
        }

        $superviciones = Clientes::consultaSupervisor(0,$fecha);

        foreach ($superviciones as $supervicion) {
            $clienteID = $supervicion->id;

            // Obtener las imágenes asociadas al cliente desde la tabla imagenescliente
            $imagenesCliente = Imagenes::whereImagen("clienteImagen_id", $clienteID);

            // Asignar las imágenes al cliente
            $supervicion->imagenes = $imagenesCliente;

            $estadosTareas = Tareas::obtenerEstadoTareasCliente($clienteID);

            // Asignar los mensajes al cliente
            $supervicion->mensaje_tareas = $estadosTareas['mensaje_tareas'];
            $supervicion->mensaje_costura = $estadosTareas['mensaje_costura'];

           
        }

        $router->render("supervisor_dashboard/aprobar_ventas",[
            "titulo" => "Aprobar Ventas",
            "superviciones" => $superviciones,
            "fecha" => $fecha
        ]);

    }

    
    public static function ventasAprobadas(Router $router){

        isAuth();
        if ($_SESSION['rol'] !== '3') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }

        $fecha = $_GET["fecha"]  ?? date("Y-m-d");
        $fechas = explode("-", $fecha);
        if(!checkdate($fechas[1], $fechas[2], $fechas[0])){
            header("Location: /404");
        }

        $superviciones = Clientes::consultaSupervisorAprobado($fecha);

        foreach ($superviciones as $supervicion) {
            $clienteID = $supervicion->id;

            // Obtener las imágenes asociadas al cliente desde la tabla imagenescliente
            $imagenesCliente = Imagenes::whereImagen("clienteImagen_id", $clienteID);

            // Asignar las imágenes al cliente
            $supervicion->imagenes = $imagenesCliente;

            $estadosTareas = Tareas::obtenerEstadoTareasCliente($clienteID);

            // Asignar los mensajes al cliente
            $supervicion->mensaje_tareas = $estadosTareas['mensaje_tareas'];
            $supervicion->mensaje_costura = $estadosTareas['mensaje_costura'];
        }

        $router->render("supervisor_dashboard/ventas-aprobadas",[
            "titulo" => "Ventas Aprobadas",
            "superviciones" => $superviciones,
            "fecha" => $fecha
        ]);

    }
    public static function ventasRechazadas(Router $router){

        isAuth();
        if ($_SESSION['rol'] !== '3') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }
        $fecha = $_GET["fecha"]  ?? date("Y-m-d");
        $fechas = explode("-", $fecha);
        if(!checkdate($fechas[1], $fechas[2], $fechas[0])){
            header("Location: /404");
        }

        $superviciones = Clientes::consultaSupervisorRechazado($fecha);

        foreach ($superviciones as $supervicion) {
            $clienteID = $supervicion->id;

            // Obtener las imágenes asociadas al cliente desde la tabla imagenescliente
            $imagenesCliente = Imagenes::whereImagen("clienteImagen_id", $clienteID);

            // Asignar las imágenes al cliente
            $supervicion->imagenes = $imagenesCliente;

            $estadosTareas = Tareas::obtenerEstadoTareasCliente($clienteID);

            // Asignar los mensajes al cliente
            $supervicion->mensaje_tareas = $estadosTareas['mensaje_tareas'];
            $supervicion->mensaje_costura = $estadosTareas['mensaje_costura'];
        }


        



        $router->render("supervisor_dashboard/ventas-rechazadas",[
            "titulo" => "Ventas Rechazadas",
            "superviciones" => $superviciones,
            "fecha" => $fecha
        ]);

    }

    public static function cortes(Router $router){

        isAuth();
        if ($_SESSION['rol'] !== '3') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }
        $fecha = $_GET["fecha"]  ?? date("Y-m-d");
        $fechas = explode("-", $fecha);
        if(!checkdate($fechas[1], $fechas[2], $fechas[0])){
            header("Location: /404");
        }

        

        $router->render("supervisor_dashboard/cortes",[
            "titulo" => "Cortes",
            "fecha" => $fecha
        ]);

    }

    public static function costura(Router $router){

        isAuth();
        if ($_SESSION['rol'] !== '3') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }
        $fecha = $_GET["fecha"]  ?? date("Y-m-d");
        $fechas = explode("-", $fecha);
        if(!checkdate($fechas[1], $fechas[2], $fechas[0])){
            header("Location: /404");
        }

        

        $router->render("supervisor_dashboard/costura",[
            "titulo" => "Costura",
            "fecha" => $fecha
        ]);

    }


}