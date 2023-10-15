<div class="contenedor-app">
<div class="imagen"></div>
<div class="app">
<h1 class="nombre-pagina" >Login</h1>
<p class="descripcion-pagina" >Inicia sesion con tus datos</p>

<?php
include_once __DIR__ . "/../templates/alertas.php";
?>

<form action="/" class="formulario" method="POST">

    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Tu email" name="email">
    </div>
    <div class="campo">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Tu Password" name="password">
    </div>

    <input type="submit" class="boton" value="Iniciar Sesion">
</form>

</div>
</div>