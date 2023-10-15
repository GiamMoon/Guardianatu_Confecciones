<?php
use Model\Imagenes;
include_once __DIR__ . "/header-dashboard.php";

// Ordenar tareas por fecha de creación
usort($tareas, function ($a, $b) {
    return strtotime($a->fecha_creacion) - strtotime($b->fecha_creacion);
});

// Agrupar tareas por ID de cliente y estado 0
$tareasAgrupadas = [];
foreach ($tareas as $tarea) {
    $clienteId = $tarea->cliente->id;
    $imagenesCliente = Imagenes::whereImagen('clienteImagen_id', $clienteId);

    // Solo considerar tareas con estado 0
    if ($tarea->estado == 0) {
        if (!isset($tareasAgrupadas[$clienteId])) {
            $tareasAgrupadas[$clienteId] = [
                'nombreCliente' => $tarea->cliente->nombres . ' ' . $tarea->cliente->apellidos,
                'tareas' => [],
            ];
        }
        $tareasAgrupadas[$clienteId]['tareas'][] = $tarea;
    }
}

?>

<div class="busqueda">
    <h2>Buscar Tareas</h2>
    <form class="formulario" id="form-busqueda">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha ?>">
        </div>
    </form>
</div>

<?php foreach ($tareasAgrupadas as $clienteId => $infoCliente) : ?>
    <div class="cliente">
        <h3 style="color: black; text-transform: uppercase;"><?php echo $infoCliente['nombreCliente']; ?></h3>
        <?php foreach ($infoCliente['tareas'] as $tarea) : ?>
            <div class="tarea">
                <p>Tarea: <?php echo $tarea->nombre; ?></p>
                <p>Estado: 
                    <span style="color: #8f6200; font-weight: bold;">Pendiente</span>
                </p>
                <!-- Enlaces Pendiente y Realizado -->
                <?php
                $pendienteClass = $tarea->estado == 0 ? 'pendiente' : '';
                $realizadoClass = $tarea->estado == 1 ? 'realizado' : '';
                ?>
                <a href="#" class="btn-estado <?php echo $pendienteClass; ?>" data-tarea-id="<?php echo $tarea->id; ?>" data-estado-actual="0">
                    Pendiente
                </a>
                <a href="#" class="btn-estado <?php echo $realizadoClass; ?>" data-tarea-id="<?php echo $tarea->id; ?>" data-estado-actual="1">
                    Realizado
                </a>

                <h3>Imagenes:</h3>
                <div class="imagenes-container">
            <?php foreach ($imagenesCliente as $imagen) : ?>
                <div class="container-img-1">
                <img src="imagenes/<?php echo $imagen->imagen_path; ?>" alt="Imagen del Cliente">
                </div>
            <?php endforeach; ?>
        </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php 
include_once __DIR__ . "/footer-dashboard.php"; 
?>

<style>
    .btn-estado {
        display: inline-block;
        padding: 10px;
        margin: 5px;
        text-decoration: none;
        color: white;
        cursor: pointer;
        border: none;
        border-radius: 5px;
        color: black;
    }

    .pendiente {
        color: white;
        background-color: #ff4d4d;
    }

    .realizado {
        color: white;
        background-color: #4da6ff;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        iniciarApp();
    });

    function iniciarApp(){
        buscarPorFecha();
        manejarBotonesEstado();
    }

    function buscarPorFecha(){
        const fechaInput = document.querySelector("#fecha");
        fechaInput.addEventListener("input", function(e){
            const fechaSeleccionada = e.target.value;
            window.location = `?fecha=${fechaSeleccionada}`;
        });
    }

    function manejarBotonesEstado() {
    const botonesEstado = document.querySelectorAll(".btn-estado");

    botonesEstado.forEach((boton) => {
        boton.addEventListener("click", async function() {
            const tareaId = boton.dataset.tareaId;
            const nuevoEstado = boton.dataset.estadoActual;

            // Actualiza el estado de la tarea en el servidor
            await actualizarEstadoEnServidor(tareaId, nuevoEstado);

            // Actualiza el color de fondo y texto del botón
            botonesEstado.forEach((b) => {
                b.classList.remove("pendiente", "realizado");
                b.style.color = "black"; // Restablece el color de texto a negro por defecto

                if (nuevoEstado === "0" && b === boton) {
                    b.classList.add("pendiente");
                } else if (nuevoEstado === "1" && b === boton) {
                    b.classList.add("realizado");
                }
            });

            // Recargar la página después de actualizar el estado (puedes optar por no recargar y actualizar el DOM directamente)
            location.reload();
        });
    });
}
async function actualizarEstadoEnServidor(tareaId, nuevoEstado) {
    try {
        // Obtener el ID del usuario de sesión
        const usuarioId = <?php echo json_encode($_SESSION['id'] ?? null); ?>;

        const response = await fetch('/actualizar-estado-tarea', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                tareaId: tareaId,
                nuevoEstado: nuevoEstado,
                usuarioId: usuarioId,
            }),
        });

        const result = await response.json();

        if (!result.success) {
            console.error('Error al actualizar el estado de la tarea:', result.error);
        }
    } catch (error) {
        console.error('Error al enviar la solicitud al servidor:', error);
    }
}
</script>