<?php
include_once __DIR__ . "/header-dashboard.php";

// Ordenar tareas por fecha de creación
usort($tareas, function ($a, $b) {
    return strtotime($a->fecha_creacion) - strtotime($b->fecha_creacion);
});

// Agrupar tareas por ID de cliente
$tareasAgrupadas = [];
foreach ($tareas as $tarea) {
    $clienteId = $tarea->cliente->id;
    if (!isset($tareasAgrupadas[$clienteId])) {
        $tareasAgrupadas[$clienteId] = [
            'nombreCliente' => $tarea->cliente->nombres . ' ' . $tarea->cliente->apellidos,
            'tareas' => [],
        ];
    }
    $tareasAgrupadas[$clienteId]['tareas'][] = $tarea;
    $clientesDivididos = array_chunk($tareasAgrupadas, 3, true);
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
</div>

<?php foreach ($clientesDivididos as $grupoClientes) : ?>
    <div class="fila-clientes">
        <?php foreach ($grupoClientes as $clienteId => $infoCliente) : ?>
            <div class="cliente">
                <h3 style="color: black; text-transform: uppercase;"><?php echo $infoCliente['nombreCliente']; ?></h3>
                <?php foreach ($infoCliente['tareas'] as $tarea) : ?>
                    <div class="tarea">
                        <p>Tarea: <?php echo $tarea->nombre; ?></p>
                        <?php if ($tarea->estado == 0) : ?>
                            <p>Estado: <span style="color: #8f6200; font-weight: bold;">Pendiente</span></p>
                        <?php else : ?>
                            <p>Estado: <span style="color: #0da6f3; font-weight: bold;">Realizado</span></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<style>
    .fila-clientes {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .cliente {
        width: calc(33.33% - 20px);
        box-sizing: border-box;
        border: 1px solid #ddd;
        padding: 10px;
        margin: 10px;
    }

    /* Estilos adicionales según sea necesario */
</style>


<?php 
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
