<?php
use Model\Tareas;
include_once __DIR__ . "/header-dashboard.php";

// Ordenar tareas por fecha de creación
$tareas = Tareas::all();

if (!empty($tareas)) {
    // Ordenar tareas por fecha de creación
    usort($tareas, function ($a, $b) {
        return strtotime($a->fecha_creacion) - strtotime($b->fecha_creacion);
    });

    $tareasAgrupadas = Tareas::obtenerTareasAgrupadas($tareas);

    ?>

<style>
    .cliente {
        color: black;
        font-weight: bold;
        width: calc(33% - 20px); /* Ancho del elemento (ajustar según sea necesario) */
        margin: 10px;
        box-sizing: border-box;
        display: inline-block; /* Permite mostrar varios elementos en una fila */
        vertical-align: top; /* Alinea los elementos en la parte superior */
        border: 1px solid black;
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    li {
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
    }

    ul li div {
        font-weight: bold;
        font-size: 18px;
        margin-right: 20px;
        text-transform: uppercase;
    }

    .nombre-tarea {
        display: inline-block;
        font-size: 18px;
        color: black;
    }

    .nombre-tarea span {
        font-size: 18px;
        margin-left: 10px;
    }

    .realizado {
        color: #0da6f3;
    }

    .pendiente {
        color: #FFA500;
    }

    .completado {
        color: green;
    }
</style>

<div class="busqueda">
    <h2>Buscar Tareas</h2>
    <form class ="formulario" id="form-busqueda">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha ?>">
        </div>
    </form>
</div>
</div>

<?php if (!empty($tareasAgrupadas)) : ?>
    <?php foreach ($tareasAgrupadas as $clienteId => $infoCliente) : ?>
        <div class="cliente">
            <h3 style="color: black;"><?php echo $infoCliente['nombreCliente']; ?></h3>
            <ul>
                <?php foreach ($infoCliente['tareas'] as $tarea) : ?>
                    <li>
                        <div>
                            <p class="nombre-tarea">
                                Nombre:<span style="color: #0da6f3; text-transform: uppercase;"><?php echo $tarea->nombre; ?></span>
                            </p>
                        </div>
                        <div>
                            <p class="nombre-tarea">
                                Estado: <span class="<?php echo ($tarea->estado == 0) ? 'pendiente' : 'completado'; ?>">
                                    <?php echo ($tarea->estado == 0) ? 'Pendiente' : 'Completado'; ?>
                                </span>
                            </p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <p>No hay tareas para mostrar.</p>
<?php endif; ?>

<?php 
} else {
    echo "No hay tareas para mostrar.";
}

include_once __DIR__ . "/footer-dashboard.php"; 
$script = "<script src='build/js/buscador.js'></script>";
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
