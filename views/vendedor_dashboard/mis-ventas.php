<?php include_once __DIR__ . "/header-dashboard.php"; ?>

<style>

.eliminar-btn {
        background-color: red; /* Puedes personalizar el color según tus preferencias */
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
    }

.estado-pago-text .pendiente {
    color: orange;
    font-weight: bold;
    text-transform: uppercase;
}

.estado-pago-text .completo{
    color: green;
    font-weight: bold;
    text-transform: uppercase;
}

.ventas {
    /* ... (estilos existentes) */
    list-style: none;
    padding: 0;
     /* Añadido: Permitir el ajuste de elementos en varias líneas */
}

.cliente-item {
    display: grid; /* Añadido: Mostrar elementos en línea */
    grid-template-columns: repeat(2,1fr);
    /* Añadido: Estilo específico para cada elemento cliente */
    border: 1px solid black;
    padding: 10px;
    margin: 10px; /* Añadido: Ancho del elemento (ajustar según sea necesario) */
    box-sizing: border-box;
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

<?php
if (count($ventas) === 0) {
    echo "<h2 class='nh' >No hay ventas en esta fecha</h2>";
}
?>

<div class="ventas_vendedor">
    <ul class="ventas">
        <?php
        $idVenta = 0;
        foreach ($ventas as $venta) {
            if ($idVenta != $venta->id) {
                $estadoTexto = '';
                $estadoClase = '';

                switch ($venta->confirmado) {
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

                
                    <p>Fecha de Creacion: <span><?php echo $venta->fecha_creacion; ?></span></p>
                    <p>N° Boleta: <span><?php echo $venta->boleta; ?></span></p>
                    <p>Nombres: <span><?php echo $venta->nombres; ?></span></p>
                    <p>Apellidos: <span><?php echo $venta->apellidos; ?></span></p>
                    <p>DNI: <span><?php echo $venta->dni; ?></span></p>
                    <p>Telefono: <span><?php echo $venta->telefono; ?></span></p>
                    <p>Provincia: <span><?php echo $venta->provincia; ?></span></p>
                    <p>Distrito: <span><?php echo $venta->distrito; ?></span></p>
                    <p>Direccion: <span><?php echo $venta->direccion; ?></span></p>
                    <p>Fecha de envio: <span><?php echo $venta->fechaEnvio; ?></span></p>
                    <p>Mensaje: <span><?php echo $venta->mensaje; ?></span></p>
                    <p>Estado: <span class="<?= $estadoClase ?>"><?php echo $estadoTexto; ?></span></p>
    
                    
            <?php
                 $idVenta = $venta->id;
                }//Fin if ?> 
                <p>Precio Total: <span>S/.<?php echo $venta->precioTotal; ?></span></p>
                <p>Adelanto: <span>S/.<?php echo $venta->adelanto; ?></span></p>
                <p>Restante a Pagar: <span>S/.<?php echo $venta->restantePagar; ?></span></p>


                <?php
            // Mostrar párrafo "Pago Completo" y el valor de la columna pago_confirmado
            echo "<p class='estado-pago-text'>Pago Completado: ";
            if ($venta->pago_confirmado == 1) {
                echo "<span class='completo'>Completo</span>";
            } else {
                echo "<span class='pendiente'>Pendiente</span>";
            }
            echo "</p>";
            ?>

                    <div class="imagenes-container">
                        <?php foreach ($venta->imagenes as $imagen) : ?>
                            <div class="container-img-1">
        <img src="imagenes/<?php echo $imagen->imagen_path; ?>" alt="">
        </div>
                        <?php endforeach; ?>
                    </div>

                    <form method="post" action="/confirmarPagoCompleto">
    <input type="hidden" name="cliente_id" value="<?php echo $venta->id; ?>">
    <?php if ($venta->pago_confirmado != 1): ?>
        <button style="background-color: #4CAF50;color: white; padding: 10px 20px;
            border: none;border-radius: 5px;text-align: center; text-decoration: none;display: inline-block;
            font-size: 16px; margin: 4px 2px;cursor: pointer;"
            type="submit" name="confirmar_pago">
            Confirmar Pago Completo
        </button>
    <?php endif; ?>
</form>

<?php if ($venta->confirmado == 2 || $venta->aprobar_envio == 2) : ?>
    <form method="post" action="/eliminarVenta">
        <input type="hidden" name="cliente_id" value="<?php echo $venta->id; ?>">
        <button class="eliminar-btn" type="submit" name="eliminar_venta">
            Eliminar
        </button>
    </form>
<?php endif; ?>
                

            <?php
            echo "</li>"; 
                $idVenta = $venta->id;
            }
         ?>
    </ul>
</div>

<?php include_once __DIR__ . "/footer-dashboard.php";

$script = "<script src='build/js/buscador.js'></script>";

?>