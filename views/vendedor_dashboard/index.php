<?php
include_once __DIR__ . "/header-dashboard.php";
include_once __DIR__ . "/../templates/alertas.php";

?>


<form class="formulario_vendedor" action="/vendedor_dashboard" method="POST" enctype="multipart/form-data">

    <div class="campo_form">
        <label for="boleta">N° boleta</label>
        <input type="text" id="boleta" placeholder="Numero de boleta" value="<?php echo s($cliente->boleta); ?>" name="boleta" minlength="6" maxlength="6" pattern="\d{6}">
    </div>

    <div class="campo_form">
        <label for="nombres">Nombres</label>
        <input type="text" id="nombres" placeholder="Nombres Cliente" value="<?php echo s($cliente->nombres); ?>" name="nombres" >
    </div>
    <div class="campo_form">
        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" placeholder="Apellidos Cliente" value="<?php echo s($cliente->apellidos); ?>" name="apellidos" >
    </div>
    <div class="campo_form">
        <label for="dni">DNI</label>
        <input type="text" id="dni" placeholder="DNI Cliente" name="dni" value="<?php echo s($cliente->dni);?>" minlength="8" maxlength="8" pattern="\d{8}" >
    </div>
    <div class="campo_form">
        <label for="telefono">Telefono</label>
        <input type="text" id="telefono" placeholder="Telefono Cliente" name="telefono" value="<?php echo s($cliente->telefono); ?>" minlength="9" maxlength="9" pattern="\d{9}">
    </div>
    <div class="campo_form">
        <label for="provincia">Provincia</label>
        <input type="text" id="provincia" placeholder="Provincia Cliente" name="provincia" value="<?php echo s($cliente->provincia); ?>" >
    </div>
    <div class="campo_form">
        <label for="distrito">Distrito</label>
        <input type="text" id="distrito" placeholder="Distrito Cliente" name="distrito" value="<?php echo s($cliente->distrito); ?>" >
    </div>
    <div class="campo_form">
        <label for="direccion">Direccion</label>
        <input type="text" id="direccion" placeholder="Direccion Cliente" name="direccion" value="<?php echo s($cliente->direccion); ?>">
    </div>
    <div class="campo_form">
        <label for="fechaEnvio">Fecha de Envio</label>
        <input type="datetime-local"  id="fechaEnvio" placeholder="Fecha de Envio" name="fechaEnvio" value="" >
    </div>
    <div class="campo_form">
        <label for="precioTotal">Precio Total</label>
        <input type="text" id="precioTotal" placeholder="Precio Total" name="precioTotal" value="<?php echo s($cliente->precioTotal); ?>">
    </div>
    <div class="campo_form">
        <label for="adelanto">Adelanto</label>
        <input type="text" id="adelanto" placeholder="Adelanto" name="adelanto" value="<?php echo s($cliente->adelanto); ?>" >
    </div>
    <div class="campo_form">
        <label for="restantePagar">Restante a Pagar</label>
        <input type="text" id="restantePagar" placeholder="restante a Pagar" name="restantePagar" value="<?php echo s($cliente->restantePagar); ?>">
    </div>
    <div class="campo_form">
        <label for="imagen">Imagen</label>
        <input type="file" id="imagen" name="imagen[]" accept="image/jpeg, image/png" multiple>
    </div>
    <div class="campo_form">
        <label for="mensaje">Mensaje</label>
        <textarea id="mensaje" name="mensaje"><?php echo s($cliente->mensaje); ?></textarea>
    </div>

    <input type="hidden" name="usuario_id" value="<?php echo $_SESSION["id"]; ?>">

    <input type="submit" class="boton" value="Registrar Venta">

</form>




<?php include_once __DIR__ . "/footer-dashboard.php"; ?>

