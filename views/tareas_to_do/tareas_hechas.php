<?php
use Model\Imagenes;
include_once __DIR__ . "/header-dashboard.php";

// Ordenar tareas por fecha de creación
usort($tareas, function ($a, $b) {
    return strtotime($a->fecha_creacion) - strtotime($b->fecha_creacion);
});

// Agrupar tareas por ID de cliente y estado 1 (Realizado)
$tareasAgrupadas = [];
foreach ($tareas as $tarea) {
    $clienteId = $tarea->cliente->id;
    $imagenesCliente = Imagenes::whereImagen('clienteImagen_id', $clienteId);

    // Solo considerar tareas con estado 1 (Realizado)
    if ($tarea->estado == 1) {
        if (!isset($tareasAgrupadas[$clienteId])) {
            $tareasAgrupadas[$clienteId] = [
                'nombreCliente' => $tarea->cliente->nombres . ' ' . $tarea->cliente->apellidos,
                'tareas' => [],
            ];
        }
        $tareasAgrupadas[$clienteId]['tareas'][] = $tarea;
    }
}

// Dividir clientes en grupos de 3
$clientesDivididos = array_chunk($tareasAgrupadas, 3, true);
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

<?php foreach ($clientesDivididos as $grupoClientes) : ?>
    <div class="fila-clientes">
        <?php foreach ($grupoClientes as $clienteId => $infoCliente) : ?>
            <div class="cliente">
                <h3 style="color: black; text-transform: uppercase;"><?php echo $infoCliente['nombreCliente']; ?></h3>
                <?php foreach ($infoCliente['tareas'] as $key => $tarea) : ?>
                    <div class="tarea">
                        <p class="tarea-texto">Tarea: <?php echo $tarea->nombre; ?></p>
                        <p>Estado: 
                            <span style="color: #0da6f3; font-weight: bold;">Realizado</span>
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

                        <?php if ($key === count($infoCliente['tareas']) - 1) : ?>
                            <h3 style="color:black">Imagenes:</h3>
                            <div class="imagenes-container">
                                <?php foreach ($imagenesCliente as $imagen) : ?>
                                    <div class="container-img-1">
                                        <img src="imagenes/<?php echo $imagen->imagen_path; ?>" alt="Imagen del Cliente">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<?php 
include_once __DIR__ . "/footer-dashboard.php"; 
?>

<style>
    .fila-clientes {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .cliente {
        width: 30%; /* Ajusta según tus necesidades */
        box-sizing: border-box;
        border: 1px solid #ddd;
        padding: 10px;
        margin: 10px;
    }

    .tarea {
        margin-bottom: 10px;
    }

    .btn-estado {
        display: inline-block;
        padding: 10px;
        margin: 5px;
        text-decoration: none;
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

    .tarea-texto {
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    iniciarApp();
});

function iniciarApp() {
    buscarPorFecha();
    manejarBotonesEstado();
    resaltarTareasIguales();
}

function buscarPorFecha() {
    const fechaInput = document.querySelector("#fecha");
    fechaInput.addEventListener("input", function (e) {
        const fechaSeleccionada = e.target.value;
        window.location = `?fecha=${fechaSeleccionada}`;
    });
}

function manejarBotonesEstado() {
    const botonesEstado = document.querySelectorAll(".btn-estado");

    botonesEstado.forEach((boton) => {
        boton.addEventListener("click", async function () {
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

            // Recargar la página
            location.reload();
        });
    });
}

async function actualizarEstadoEnServidor(tareaId, nuevoEstado) {
    try {
        // Obtener el ID del usuario de sesión (ajusta esto según tu lógica de autenticación)
        const usuarioId = <?php echo $_SESSION['id'] ?? null; ?>;

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

function resaltarTareasIguales() {
    const tareas = document.querySelectorAll(".tarea-texto");

    tareas.forEach((tarea, index) => {
        tarea.dataset.originalText = tarea.innerText.trim(); // Almacena el texto original
        tarea.dataset.isHighlighted = "false"; // Bandera para rastrear si la tarea está resaltada

        tarea.addEventListener("click", () => {
            const estaResaltada = tarea.dataset.isHighlighted === "true";
            const tareaTexto = tarea.innerText;
            const tareaTextoLimpio = tareaTexto.split("(")[0].trim().toLowerCase(); // Convertir a minúsculas

            let tareasIguales = [];

            tareas.forEach((otraTarea) => {
                const otraTareaTexto = otraTarea.innerText;
                const otraTareaTextoLimpio = otraTareaTexto.split("(")[0].trim().toLowerCase();

                if (tareaTextoLimpio === otraTareaTextoLimpio) {
                    tareasIguales.push(otraTarea);

                    if (estaResaltada) {
                        otraTarea.innerText = otraTarea.dataset.originalText;
                        otraTarea.style.fontWeight = "normal";
                        otraTarea.style.color = "black";
                        otraTarea.dataset.isHighlighted = "false";
                    } else {
                        otraTarea.style.fontWeight = "bold";
                        otraTarea.style.color = "red";
                        otraTarea.dataset.isHighlighted = "true";
                    }
                }
            });

            if (tareasIguales.length <= 1) {
                tarea.style.fontWeight = "normal";
                tarea.style.color = "black";
                tarea.innerText = tarea.dataset.originalText;
                tarea.dataset.isHighlighted = "false";
            } else {
                tareasIguales.forEach((t) => {
                    if (!estaResaltada) {
                        t.style.fontWeight = "bold";
                        t.style.color = "red";
                        t.innerText = t.dataset.originalText.toUpperCase();
                        t.dataset.isHighlighted = "true";
                    }
                });
            }
        });
    });
}
</script>
