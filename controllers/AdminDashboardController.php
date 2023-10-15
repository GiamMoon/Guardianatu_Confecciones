<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Clientes;
use Model\Imagenes;
use Model\Presupuesto;


date_default_timezone_set('America/Lima');

class AdminDashboardController{

    private $ingresosEgresos;
    private $totalPorSemana;

    public static function index(Router $router)
    {
        isAuth();

        if ($_SESSION['rol'] !== '1') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }

        $fecha = $_GET["fecha"] ?? date("Y-m-d");
        $fechas = explode("-", $fecha);

        if (!checkdate($fechas[1], $fechas[2], $fechas[0])) {
            header("Location: /404");
        }

        $superviciones = Clientes::consultaSupervisorFinal($fecha);

        foreach ($superviciones as $supervicion) {
            $clienteID = $supervicion->id;

            // Obtener las imágenes asociadas al cliente desde la tabla imagenescliente
            $imagenesCliente = Imagenes::whereImagen("clienteImagen_id", $clienteID);

            // Asignar las imágenes al cliente
            $supervicion->imagenes = $imagenesCliente;
        }

        $router->render("admin_dashboard/index", [
            "titulo" => "Ventas",
            "superviciones" => $superviciones,
            "fecha" => $fecha
        ]);
    }




    public function ingresoEgreso(Router $router) {
        isAuth();
        if ($_SESSION['rol'] !== '1') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }

        $router->render("admin_dashboard/ingreso_egreso", [
            "titulo" => "Ingreso / Egreso",
        ]);
    }
    public function resultadoNeto(Router $router) {
        isAuth();
        if ($_SESSION['rol'] !== '1') {
            // Si no tiene el rol necesario, redirigir o mostrar un mensaje de error
            header("Location: /sin_permisos");
            exit;
        }
                // Recibe los parámetros del formulario
                $mes = $_GET["mes"] ?? date("m");
                $ano = $_GET["ano"] ?? date("Y");
        
                // Filtrar los datos según el mes y año
                $ingresosEgresos = Presupuesto::filtrarPorMesAno($mes, $ano);
        
        
                // Calcula el total de ingresos y egresos por semana
                $totalPorSemana = Presupuesto::calcularTotalPorSemana($ingresosEgresos);
        
                // Guarda los resultados en las propiedades de la clase
                $this->ingresosEgresos = $ingresosEgresos;
                $this->totalPorSemana = $totalPorSemana;
        
        // Accede a las propiedades de la clase en lugar de variables locales
        $router->render("admin_dashboard/resultado_neto", [
            "titulo" => "Resultado Neto",
            "ingresosEgresos" => $ingresosEgresos,
            "totalPorSemana" => $totalPorSemana,
            "mes" => $mes,
            "ano" => $ano,
        ]);
    }
    public static function guardarGasto(Router $router) {
        isAuth();
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'];
            $cantidad = $_POST['cantidad'];
            $concepto = $_POST['concepto'];
            $fecha = $_POST['fecha'];
            // Validación de datos (puedes agregar más validaciones según tus necesidades)
           
            // Crear instancia de Presupuesto
            $gasto = new Presupuesto([
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'concepto' => $concepto,
                'fecha' => $fecha,
            ]);
            
            // Guardar el gasto en la base de datos
            $resultado = $gasto->crearPresu();

            
            
            if ($resultado['resultado']) {
                // Redireccionar o mostrar un mensaje de éxito
                header("Location: /ingreso_egreso");
                exit;
            } else {
                // Mostrar un mensaje de error
                echo "Error al guardar el gasto.";
            }
        }
    }


}