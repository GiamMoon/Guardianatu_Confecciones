<?php
use Controllers\DespachoDashboardController;
include_once __DIR__ . "/header-dashboard.php";
$despachoController = new DespachoDashboardController();

?>

<div class="busqueda">
    <h2>Buscar Clientes</h2>
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha ?>">
        </div>
    </form>
</div>
</div>

<ul>
    <?php foreach ($clientes as $cliente): ?>
        <?php if ($cliente->confirmar_envio === "1"): ?>
        <li>
        
            <h2 style="color: black;" >Informacion General</h2>
            <p style="color: black;">Fecha de Creación: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->fecha_creacion; ?></span></p>
            <p style="color: black;">Boleta: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->boleta; ?></span></p>
            <p style="color: black;">Nombres: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->nombres; ?></span></p>
            <p style="color: black;">Apellidos: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->apellidos; ?></span></p>
            <p style="color: black;">DNI: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->dni; ?></span></p>
            <p style="color: black;">Teléfono: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->telefono; ?></span></p>
            <p style="color: black;">Provincia: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->provincia; ?></span></p>
            <p style="color: black;">Distrito: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->distrito; ?></span></p>
            <p style="color: black;">Dirección: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->direccion; ?></span></p>
            <p style="color: black;">Fecha de Envío: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->fechaEnvio; ?></span></p>
            <p style="color: black;">Mensaje: <span style="text-transform: uppercase; font-weight: bold; color: #0da6f3;" ><?php echo $cliente->mensaje; ?></span></p>
            <?php
            // Asegúrate de que $cliente->nombres_tareas y $cliente->estados_tareas no sean nulos
            if ($cliente->nombres_tareas !== null) {
                // Cambia el separador de comas a puntos
                $tareas = explode(',', $cliente->nombres_tareas);

                $estadoTodasTareas = 'Hecho'; // Suponemos que todas las tareas están completas inicialmente

                foreach ($tareas as $tarea) {
                    // Obtener el estado de la tarea desde la base de datos (o desde donde sea)
                    $estadoTarea = $despachoController->obtenerEstadoTarea($cliente->id, trim($tarea));

                    // Verificar el estado de la tarea y actualizar el estado general
                    if ($estadoTarea !== '1') {
                        $estadoTodasTareas = 'Pendiente';
                        break; // No es necesario continuar verificando si encontramos una tarea pendiente
                    }
                }

                // Mostrar el mensaje correspondiente según el estado de todas las tareas
                echo '<p style="color: black;">Estado de tareas: <span fo style=" font-weight: bold; color: ' . ($estadoTodasTareas === 'Hecho' ? '#0da6f3' : 'orange') . ';">' . $estadoTodasTareas . '</span></p>';
            } else {
                echo '<p style="color: black;">No hay tareas disponibles.</p>';
            }
            ?>


                        
            <?php
                $estadoConfirmacion = '';
                $color = '';

                switch ($cliente->confirmado) {
                    case 0:
                        $estadoConfirmacion = 'Pendiente';
                        $color = 'orange';
                        break;
                    case 1:
                        $estadoConfirmacion = 'Aprobado';
                        $color = "#0da6f3";
                        break;
                    case 2:
                        $estadoConfirmacion = 'Rechazado';
                        $color = 'red';
                        break;
                    default:
                        $estadoConfirmacion = 'Desconocido';
                        $color = 'black';
                        break;
                }
            ?>

            <p style="color: black; " > Estado Supervicion: <span style="text-transform: uppercase; font-weight: bold; color: <?php echo $color;?>" ><?php echo $estadoConfirmacion; ?></span></p>


            
            <?php
                $estadoEnvio = '';

                switch ($cliente->confirmar_envio) {
                    case 0:
                        $estadoEnvio = 'Pendiente';
                        break;
                    case 1:
                        $estadoEnvio = 'Aprobado';
                        break;
                    case 2:
                        $estadoEnvio = 'Rechazado';
                        break;
                    default:
                        $estadoEnvio = 'Desconocido';
                        break;
                }
            ?>

            <div class="imagenes-container">
                <?php foreach ($cliente->imagenes as $imagen) : ?>
                    <div class="container-img-1">
                        <img src="imagenes/<?php echo $imagen->imagen_path; ?>" alt="Imagen del Cliente">
                    </div>
                <?php endforeach; ?>
            </div>

        </li>

        <hr style="border-top: 1px solid black;">
        <?php endif; ?>
    <?php endforeach; ?>
</ul>


<?php include_once __DIR__ . "/footer-dashboard.php"; 
?>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        iniciarApp();
    });

    function iniciarApp(){
        buscarPorFecha();
    }

    function buscarPorFecha(){
        const fechaInput = document.querySelector("#fecha");
        fechaInput.addEventListener("input", function(e){
            const fechaSeleccionada = e.target.value;

            window.location = `?fecha=${fechaSeleccionada}`;
        });
    }
</script>
