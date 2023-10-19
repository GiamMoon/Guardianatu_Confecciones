<?php 

require_once __DIR__ . '/../includes/app.php';

use Controllers\AdminDashboardController;
use Controllers\DespachoDashboardController;
use MVC\Router;
use Controllers\LoginController;
use Controllers\SupervisorDashboardController;
use Controllers\TareaDashboardController;
use Controllers\VendedorDashboardController;

$router = new Router();
$controller = new AdminDashboardController();

$router->get("/", [LoginController::class, "login"]);
$router->post("/", [LoginController::class, "login"]);
$router->get("/logout", [LoginController::class, "logout"]);

$router->get("/create-new-cuenta-only-admin", [LoginController::class, "crear"]);
$router->post("/create-new-cuenta-only-admin", [LoginController::class, "crear"]);

//Zona de vendedor
$router->get("/vendedor_dashboard",[VendedorDashboardController::class, "index"]);
$router->post("/vendedor_dashboard",[VendedorDashboardController::class, "index"]);
$router->get("/misventas",[VendedorDashboardController::class, "mis_ventas"]);
$router->post("/misventas",[VendedorDashboardController::class, "mis_ventas"]);
$router->post("/confirmarPagoCompleto", [VendedorDashboardController::class, "confirmarPagoCompleto"]);
$router->post("/eliminarVenta", [VendedorDashboardController::class, "eliminarVenta"]);



//Zona Supervisor
$router->get("/supervisor_dashboard",[SupervisorDashboardController::class, "index"]);
$router->post("/supervisor_dashboard",[SupervisorDashboardController::class, "index"]);
$router->get("/aprobar_ventas",[SupervisorDashboardController::class, "aprobarVentas"]);
$router->post("/aprobar_ventas",[SupervisorDashboardController::class, "aprobarVentas"]);
$router->get("/ventas_aprobadas",[SupervisorDashboardController::class, "ventasAprobadas"]);
$router->post("/ventas_aprobadas",[SupervisorDashboardController::class, "ventasAprobadas"]);
$router->get("/ventas_rechazadas",[SupervisorDashboardController::class, "ventasRechazadas"]);
$router->post("/ventas_rechazadas",[SupervisorDashboardController::class, "ventasRechazadas"]);
$router->get("/cortes",[SupervisorDashboardController::class, "cortes"]);
$router->post("/cortes",[SupervisorDashboardController::class, "cortes"]);
$router->get("/costura",[SupervisorDashboardController::class, "costura"]);
$router->post("/costura",[SupervisorDashboardController::class, "costura"]);

//Zona Tareas
$router->get("/tareas_dashboard",[TareaDashboardController::class, "index"]);
$router->post("/tareas_dashboard",[TareaDashboardController::class, "index"]);
$router->get("/tareas_creadas",[TareaDashboardController::class, "tareasCreadas"]);
$router->post("/tareas_creadas",[TareaDashboardController::class, "tareasCreadas"]);
$router->get("/obtener-clientes", [TareaDashboardController::class, "obtenerClientes"]);
$router->post("/crear-tarea", [TareaDashboardController::class, "crearTarea"]);

//Zona Tareas Hacer
$router->get("/tareas_pendientes",[TareaDashboardController::class, "tareasPendientes"]);
$router->post("/tareas_pendientes",[TareaDashboardController::class, "tareasPendientes"]);
$router->get("/tareas_hechas",[TareaDashboardController::class, "tareasHechas"]);
$router->post("/tareas_hechas",[TareaDashboardController::class, "tareasHechas"]);
$router->post("/actualizar-estado-tarea", [TareaDashboardController::class, "actualizarEstadoTarea"]);

//Zona costura
$router->get("/costuras_pendientes",[TareaDashboardController::class, "costurasPendientes"]);
$router->post("/costuras_pendientes",[TareaDashboardController::class, "costurasPendientes"]);
$router->get("/costuras_hechas",[TareaDashboardController::class, "costurasHechas"]);
$router->post("/costuras_hechas",[TareaDashboardController::class, "costurasHechas"]);
$router->post("/actualizar-estado-costura", [TareaDashboardController::class, "actualizarEstadoCostura"]);

//Zona despacho
$router->get("/despacho_pendiente",[DespachoDashboardController::class, "index"]);
$router->post("/despacho_pendiente",[DespachoDashboardController::class, "index"]);
$router->get("/despacho_hecho",[DespachoDashboardController::class, "despachoHecho"]);
$router->post("/despacho_hecho",[DespachoDashboardController::class, "despachoHecho"]);
$router->post("/confirmar-envio", [DespachoDashboardController::class, "confirmarEnvio"]);

//Zona admin
$router->get("/ventas",[AdminDashboardController::class, "index"]);
$router->post("/ventas",[AdminDashboardController::class, "index"]);
$router->get("/ingreso_egreso",[AdminDashboardController::class, "ingresoEgreso"]);
$router->get("/ingreso_egreso", [$controller, "ingresoEgreso"]);
$router->get("/resultado_neto", [$controller, "resultadoNeto"]);
$router->post("/resultado_neto",[AdminDashboardController::class, "resultadoNeto"]);
$router->post("/guardar-gasto", [AdminDashboardController::class, "guardarGasto"]);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();