<?php
include_once __DIR__ . "/header-dashboard.php";

?>

<style>

    .ventas_vendedor ul {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        list-style: none;
        padding: 0;
        width: 100%; /* Asegura que la lista ocupe todo el ancho disponible */
    }

    .ventas_vendedor li {
        border: 1px solid black;
        padding: 10px;
        width: calc(33.33% - 20px); /* Calcula el ancho para que haya tres elementos en una fila con espacio entre ellos */
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
            if ($idSupervicion != $supervicion->id) {
                $estadoTexto = '';
                $estadoClase = '';

                if ($supervicion->confirmado == 1 && $supervicion->aprobar_envio == 1) {
                    $estadoTexto = 'Aprobado';
                    $estadoClase = 'aprobado';
                } elseif ($supervicion->confirmado == 2 || $supervicion->aprobar_envio == 2) {
                    $estadoTexto = 'Rechazado';
                    $estadoClase = 'rechazado';
                } else {
                    $estadoTexto = 'En espera';
                    $estadoClase = 'en-espera';
                }
                ?>

        <li>
            <p>Fecha de Creacion: <span><?php echo $supervicion->fecha_creacion; ?></span></p>
            <p>N° Boleta: <span><?php echo $supervicion->boleta; ?></span></p>
            <p>Nombres: <span><?php echo $supervicion->nombres; ?></span></p>
            <p>Apellidos: <span><?php echo $supervicion->apellidos; ?></span></p>
            <p>DNI: <span><?php echo $supervicion->dni; ?></span></p>
            <p>Telefono: <span><?php echo $supervicion->telefono; ?></span></p>
            <p>Provincia: <span><?php echo $supervicion->provincia; ?></span></p>
            <p>Distrito: <span><?php echo $supervicion->distrito; ?></span></p>
            <p>Direccion: <span><?php echo $supervicion->direccion; ?></span></p>
            <p>Fecha de envio: <span><?php echo $supervicion->fechaEnvio; ?></span></p>
            <p>Mensaje Vendedor: <span><?php echo $supervicion->mensaje; ?></span></p>
            <p>Estado de supervisión: <span class="<?= $estadoClase ?>"><?php echo $estadoTexto; ?></span></p>
                    <?php if ($supervicion->confirmar_envio == 1) : ?>
                        <p>Estado de Envío: <span style="color: green; font-weight: bold;">Enviado</span></p>
                    <?php elseif ($supervicion->confirmar_envio == 0) : ?>
                        <p>Estado de Envío: <span style="color: orange; font-weight: bold;">En Espera</span></p>
                    <?php endif; ?>
            <!-- Nuevo párrafo para mostrar el estado de pago -->
<?php if ($supervicion->pago_confirmado == 1) : ?>
    <p>Pago Confirmado: <span style="color: green; font-weight: bold;">Confirmado</span></p>
<?php else : ?>
    <p>Pago Confirmado: <span style="color: orange; font-weight: bold;">En espera</span></p>
<?php endif; ?>        

            <p>Nombre del Vendedor: <span><?php echo isset($supervicion->nombre_usuario) ? $supervicion->nombre_usuario : '' ?> <?php echo isset($supervicion->apellido_usuario) ? $supervicion->apellido_usuario : ''; ?></span></p>
            <p>Nombre del Supervisor: <span><?php echo isset($supervicion->nombre_supervisor) ? $supervicion->nombre_supervisor : '' ?> <?php echo isset($supervicion->apellido_supervisor) ? $supervicion->apellido_supervisor : ''; ?></span></p>
            <p>Nombre del Despacho: <span><?php echo $supervicion->nombre_despacho; ?> <?php echo $supervicion->apellido_despacho; ?></span></p>
            <p>Mensaje de Despacho: <span><?php echo $supervicion->mensaje_vendedor; ?></span></p>

    

            <h3>Precios:</h3>
            <?php
                 $idVenta = $supervicion->id;
                }//Fin if ?> 
                <p>Precio Total: <span>S/.<?php echo $supervicion->precioTotal; ?></span></p>
                <p>Adelanto: <span>S/.<?php echo $supervicion->adelanto; ?></span></p>
                <p>Restante a Pagar: <span>S/.<?php echo $supervicion->restantePagar; ?></span></p>

                <h3>Imágenes:</h3>
                    <div class="imagenes-container">
                        <?php foreach ($supervicion->imagenes as $imagen) : ?>
                            <div class="container-img-1">
                                <img src="imagenes/<?php echo $imagen->imagen_path; ?>" alt="">
                            </div>
                        <?php endforeach; ?>
                    </div>
                
        </li>





         <?php } //Fin foreach?> 
    </ul>
</div>


<?php include_once __DIR__ . "/footer-dashboard.php"; 

    $script = "<script src='build/js/buscador.js'></script>";
    
?>