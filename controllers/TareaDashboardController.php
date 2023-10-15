<?php
namespace Controllers;

use Model\Clientes;
use MVC\Router;
use Model\Tareas;
use Intervention\Image\ImageManagerStatic as Image;

date_default_timezone_set('America/Lima');

class TareaDashboardController {
    public static function index(Router $router) {
        isAuth();
        if ($_SESSION['rol'] !== '4') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }

        $router->render("tareas_dashboard/index", [
            "titulo" => "Crear tareas"
        ]);
    }

    
    
    public static function obtenerClientes() {
        $clientes = Clientes::all();
        
        // Ordenar clientes alfabéticamente por nombres
        usort($clientes, function ($a, $b) {
            return strcasecmp($a->nombres, $b->nombres);
        });
    
        echo json_encode($clientes);
    }

    public static function tareasCreadas(Router $router) {
        isAuth();
        if ($_SESSION['rol'] !== '4') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }

        $fecha = $_GET['fecha'] ?? date("Y-m-d");
    
        // Obtener tareas creadas desde la base de datos
        $tareas = Tareas::whereAllFechas2('fecha_creacion', $fecha);
    
        // Obtener información del cliente asociado
        foreach ($tareas as $tarea) {
            // Verifica que haya un cliente_id antes de buscar el cliente
            if ($tarea->cliente_id) {
                $cliente = Clientes::find($tarea->cliente_id);
                $tarea->cliente = $cliente;
            } else {
                // Si no hay cliente_id, asigna un objeto de cliente vacío
                $tarea->cliente = new Clientes();
            }
        }
    
        // Renderizar la vista con las tareas
        $router->render("tareas_dashboard/tareas_creadas", [
            "titulo" => "Tareas Creadas",
            "fecha" => $fecha,
            "tareas" => $tareas,
        ]);
    }

    public static function crearTarea() {
        // Obtiene el contenido del cuerpo de la solicitud
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
    
        $clienteId = $data['cliente_id'] ?? null;
        $nombreTarea = $data['tarea'] ?? '';
        
        // Obtener el ID del usuario de sesión (puedes ajustar esto según tu lógica de autenticación)
        $usuarioId = $_SESSION['id'] ?? null;
    
        if (!$clienteId) {
            echo json_encode(['error' => 'ID de cliente no proporcionado']);
            return;
        }
    
        // Asegurarte de que tengas la lógica para validar la autenticación del usuario y obtener su ID
    
        $tarea = new Tareas(['cliente_id' => $clienteId, 'nombre' => $nombreTarea, 'creadorTareas_id' => $usuarioId]);
        $resultado = $tarea->guardar();
    
        if ($resultado) {
            // Envia solo la información necesaria en lugar del objeto completo
            echo json_encode([
                'success' => true,
                'mensaje' => 'Tarea agregada correctamente',
                'tarea' => ['id' => $tarea->id, 'cliente_id' => $tarea->cliente_id, 'nombre' => $tarea->nombre, 'fecha_creacion' => $tarea->fecha_creacion]
            ]);
        } else {
            echo json_encode(['error' => 'Error al agregar la tarea']);
        }
    }

    public static function tareasPendientes(Router $router) {
        isAuth();
        if ($_SESSION['rol'] !== '5') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }



        $fecha = $_GET['fecha'] ?? date("Y-m-d");
    
        // Obtener tareas creadas desde la base de datos
        $tareas = Tareas::whereAllFechas2('fecha_creacion', $fecha);
    
        // Obtener información del cliente asociado
        foreach ($tareas as $tarea) {
            // Verifica que haya un cliente_id antes de buscar el cliente
            if ($tarea->cliente_id) {
                $cliente = Clientes::find($tarea->cliente_id);
                $tarea->cliente = $cliente;
            } else {
                // Si no hay cliente_id, asigna un objeto de cliente vacío
                $tarea->cliente = new Clientes();
            }
        }

        

        $router->render("tareas_to_do/tareas_pendientes", [
            "titulo" => "Tareas Pendientes",
            "fecha" => $fecha,
            "tareas" => $tareas
        ]);
    }
    
    public static function tareasHechas(Router $router) {
        isAuth();
        if ($_SESSION['rol'] !== '5') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }


        $fecha = $_GET['fecha'] ?? date("Y-m-d");
    
        // Obtener tareas creadas desde la base de datos
        $tareas = Tareas::whereAllFechas2('fecha_creacion', $fecha);
    
        // Obtener información del cliente asociado
        foreach ($tareas as $tarea) {
            // Verifica que haya un cliente_id antes de buscar el cliente
            if ($tarea->cliente_id) {
                $cliente = Clientes::find($tarea->cliente_id);
                $tarea->cliente = $cliente;
            } else {
                // Si no hay cliente_id, asigna un objeto de cliente vacío
                $tarea->cliente = new Clientes();
            }
        }

        $router->render("tareas_to_do/tareas_hechas", [
            "titulo" => "Tareas Hechas",
            "fecha" => $fecha,
            "tareas" => $tareas
        ]);
    }

    public static function actualizarEstadoTarea(Router $router) {
        // Obtener datos de la solicitud
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validar los datos
        if (!isset($data['tareaId']) || !isset($data['nuevoEstado']) || !isset($data['usuarioId'])) {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
            return;
        }
        
        // Actualizar el estado de la tarea en la base de datos y guardar el ID del usuario que la completó
        $tarea = Tareas::find($data['tareaId']);
        if ($tarea) {
            $tarea->estado = $data['nuevoEstado'];
            $tarea->completadorTareas_id = $data['usuarioId']; // Nueva columna para el ID del usuario que completó la tarea
            $tarea->guardar();
            echo json_encode(['success' => true, 'mensaje' => 'Estado de tarea actualizado']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Tarea no encontrada']);
        }
    }
    



}



