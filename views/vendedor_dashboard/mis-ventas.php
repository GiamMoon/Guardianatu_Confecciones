<?php include_once __DIR__ . "/header-dashboard.php"; ?>

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
                ?>

                <li>
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
    

            <h3>Precios:</h3>
            <?php
                 $idVenta = $venta->id;
                }//Fin if ?> 
                <p>Precio Total: <span>S/.<?php echo $venta->precioTotal; ?></span></p>
                <p>Adelanto: <span>S/.<?php echo $venta->adelanto; ?></span></p>
                <p>Restante a Pagar: <span>S/.<?php echo $venta->restantePagar; ?></span></p>

                <h3>Imágenes:</h3>
                    <div class="imagenes-container">
                        <?php foreach ($venta->imagenes as $imagen) : ?>
                            <div class="container-img-1">
        <img src="imagenes/<?php echo $imagen->imagen_path; ?>" alt="">
        </div>
                        <?php endforeach; ?>
                    </div>
                </li>

            <?php
                $idVenta = $venta->id;
            }
         ?>
    </ul>
</div>

<?php include_once __DIR__ . "/footer-dashboard.php";

$script = "<script src='build/js/buscador.js'></script>";

?>