<?php include_once __DIR__ . "/header-dashboard.php"; ?>

<div style="margin: 20px;">

    <h2 style="color: black;">Nuevo Ingreso / Egreso</h2>

    <form action="/guardar-gasto" method="post" style="max-width: 400px; margin: auto;">

        <div style="margin-bottom: 15px;">
            <label for="tipo" style="color: black; display: block; margin-bottom: 5px;">Tipo de Gasto:</label>
            <select name="tipo" id="tipo" style="width: 100%; padding: 8px;">
                <option value="egreso">Egreso</option>
                <option value="ingreso">Ingreso</option>
            </select>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="cantidad" style="color: black; display: block; margin-bottom: 5px;">Cantidad:</label>
            <input type="number" name="cantidad" id="cantidad" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="concepto" style="color: black; display: block; margin-bottom: 5px;">Concepto:</label>
            <input type="text" name="concepto" id="concepto" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label for="fecha" style="color: black; display: block; margin-bottom: 5px;">Fecha:</label>
            <input type="date" name="fecha" id="fecha" required style="width: 100%; padding: 8px; box-sizing: border-box;">
        </div>

        <input type="hidden" name="admin_id" value="<?= $_SESSION['id'] ?? '' ?>">


        <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Guardar Gasto</button>

    </form>

</div>

<?php include_once __DIR__ . "/footer-dashboard.php"; ?>
