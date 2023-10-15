
<div class="contenedor-app">
<div class="imagen"></div>
<div class="app">
<h1 class="nombre-pagina" >Crear Cuenta</h1>
<p class="descripcion pagina">Llena el siguiente formulario para crear una cuenta</p>

<?php
include_once __DIR__ . "/../templates/alertas.php";

?>

<form action="/create-new-cuenta-only-admin" method="POST" class="formulario">
    <div class="campo">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" placeholder="Tu Nombre" name="nombre" value="<?php echo s($usuario->nombre); ?>">
    </div>
    <div class="campo">
        <label for="apellido">Apellido</label>
        <input type="text" id="apellido" placeholder="Tu Apellido" name="apellido" value="<?php echo s($usuario->apellido); ?>">
    </div>
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Tu Email" name="email" value="<?php echo s($usuario->email); ?>">
        
    </div>
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Tu Password">
    </div>
    <div class="campo">
        <label for="rol">Tu rol</label>
        <input type="number" id="rol" placeholder="Tu Rol" name="rol" min="1" max="6" value="<?php echo s($usuario->rol); ?>">
    </div>
    <input type="submit" class="boton" value="Crear Cuenta">
</form>
</div>
</div>