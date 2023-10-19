<?php
include_once __DIR__ . "/header-dashboard.php";
?>
<style>
    
    .ventas {
            /* ... (estilos existentes) */
            list-style: none;
            padding: 0;
            display: flex; /* Añadido: Mostrar elementos en línea */
            flex-wrap: wrap; /* Añadido: Permitir el ajuste de elementos en varias líneas */
        }
    
        .cliente-item {
            /* Añadido: Estilo específico para cada elemento cliente */
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px;
            width: calc(50% - 20px); /* Ajuste para que cada elemento ocupe el 50% del contenedor con un margen de 20px */
            box-sizing: border-box;
            position: relative; /* Añadido: Posición relativa para el contenedor */
        }
    
        .contenedor-botones {
            
            margin: 0 auto;/* Añadido: Apilar los botones verticalmente */
        }
    
        .boton_supervisor,
        .boton_supervisor-verde {
            /* Añadido: Estilos para los botones */
            padding: 8px 16px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }
    
        .boton_supervisor {
            background-color: red;
            color: white;
        }
    
        .boton_supervisor-verde {
            background-color: green;
            color: white;
        }
    </style>

<div class="busqueda">
    <h2>Buscar Venta</h2>
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha ?>">
        </div>
    </form>
</div>
</div>

<?php
if (count($superviciones) === 0) {
    echo "<h2 class='nh' >No hay ventas en esta fecha</h2>";
}
?>

<div class="ventas_vendedor">
    <ul class="ventas">
        <?php
        $idSupervicion = 0;
        foreach ($superviciones as $supervicion) {
            // Verificar si aprobar_envio es igual a 0
            if ($supervicion->aprobar_envio == 0) {
                $estadoTexto = '';
                $estadoClase = '';
                switch ($supervicion->confirmado) {
                    case 1:
                        $estadoTexto = 'Aprobado';
                        $estadoClase = 'aprobado';
                        break;
                    case 2:
                        $estadoTexto = 'Rechazado';
                        $estadoClase = 'rechazado';
                        break;
                    case 0:
                        $estadoTexto = 'En espera';
                        $estadoClase = 'en-espera';
                        break;
                    default:
                        $estadoTexto = 'Estado desconocido';
                        $estadoClase = 'estado-desconocido';
                }

                
                echo "<li class='cliente-item'>";
                ?>
        

                    <p>Fecha de Creacion: <span><?php echo $supervicion->fecha_creacion; ?></span></p>
                    <p>Nombre Vendedor: <span><?php echo isset($supervicion->nombre_usuario) ? $supervicion->nombre_usuario : '' ?> <?php echo isset($supervicion->apellido_usuario) ? $supervicion->apellido_usuario : ''; ?></span></p>
                    <p>N° Boleta: <span><?php echo $supervicion->boleta; ?></span></p>
                    <p>Nombres: <span><?php echo $supervicion->nombres; ?></span></p>
                    <p>Apellidos: <span><?php echo $supervicion->apellidos; ?></span></p>
                    <p>DNI: <span><?php echo $supervicion->dni; ?></span></p>
                    <p>Telefono: <span><?php echo $supervicion->telefono; ?></span></p>
                    <p>Provincia: <span><?php echo $supervicion->provincia; ?></span></p>
                    <p>Distrito: <span><?php echo $supervicion->distrito; ?></span></p>
                    <p>Direccion: <span><?php echo $supervicion->direccion; ?></span></p>
                    <p>Fecha de envio: <span><?php echo $supervicion->fechaEnvio; ?></span></p>
                    <p>Mensaje: <span><?php echo $supervicion->mensaje; ?></span></p>
                    <p>Estado: <span class="<?= $estadoClase ?>"><?php echo $estadoTexto; ?></span></p>

                    <?php
                    // Aquí evaluamos el estado de las tareas y asignamos el color correspondiente
                    $color = ($supervicion->mensaje_tareas === 'Completado') ? 'green' : 'orange';
                    ?>
                    <p>Estado de Cortes: <span style="font-weight: bold; color: <?php echo $color; ?>"><?php echo $supervicion->mensaje_tareas; ?></span></p>
                    <?php $colorCostura = ($supervicion->mensaje_costura === 'Completado') ? 'green' : 'orange';
                    ?>
                    <p>Estado de Costura: <span style="font-weight: bold; color: <?php echo $colorCostura; ?>"><?php echo $supervicion->mensaje_costura; ?></span></p>
                    
                    <h3>Imágenes:</h3>
                    <div class="imagenes-container">
                        <?php foreach ($supervicion->imagenes as $imagen) : ?>
                            <div class="container-img-1">
                                <img src="imagenes/<?php echo $imagen->imagen_path; ?>" alt="">
                            </div>
                        <?php endforeach; ?>
                    </div>

                <form class="contenedor-botones" method="POST" action="/supervisor_dashboard">
                    <input type="hidden" name="id" value="<?php echo $supervicion->id; ?>">
                    <input type="submit" class="boton_supervisor-verde" value="Aprobar" name="aprobar_venta">
                </form>

                <?php
                        echo "</li>"; 
                 $idVenta = $supervicion->id;
            } // Fin if
        } // Fin foreach
        ?>
    </ul>
</div>

<?php include_once __DIR__ . "/footer-dashboard.php"; 

$script = "<script src='build/js/buscador.js'></script>";
?>
