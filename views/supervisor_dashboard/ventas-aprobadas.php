<?php
include_once __DIR__ . "/header-dashboard.php";

?>

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
                ?>

        <li>
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
                 $idVenta = $supervicion->id;
                }//Fin if ?> 
           
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
