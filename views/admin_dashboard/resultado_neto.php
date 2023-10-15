<?php include_once __DIR__ . "/header-dashboard.php"; ?>

<div style="margin: 20px; text-align: center;">

    <h2 style="color: black; text-transform: uppercase; font-weight: bold;">Filtrar por Mes y Año</h2>

    <form action="" method="get" style="display: flex; flex-wrap: wrap; justify-content: center; align-items: center;">

        <div style="margin: 0 10px 10px 0;">
            <label for="mes" style="color: black; display: block; margin-bottom: 5px; text-transform: uppercase; font-weight: bold;">Selecciona el mes:</label>
            <select name="mes" id="mes" style="width: 150px; padding: 8px; box-sizing: border-box;">
                <?php
                $meses = [
                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                ];
                ?>
                <?php foreach ($meses as $numero => $nombre) : ?>
                    <option value="<?= $numero ?>" <?= isset($_GET['mes']) && $_GET['mes'] == $numero ? 'selected' : '' ?>>
                        <?= $nombre ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="margin: 0 10px 10px 0;">
            <label for="ano" style="color: black; display: block; margin-bottom: 5px; text-transform: uppercase; font-weight: bold;">Selecciona el año:</label>
            <select name="ano" id="ano" style="width: 150px; padding: 8px; box-sizing: border-box;">
                <?php for ($i = 2023; $i <= 2030; $i++) : ?>
                    <option value="<?= $i ?>" <?= isset($_GET['ano']) && $_GET['ano'] == $i ? 'selected' : '' ?>><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <button type="submit" style="background-color: #0da6f3; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; margin-top: 15px; text-transform: uppercase; font-weight: bold;">Filtrar</button>

    </form>

</div>

<div style="color: black; text-align: center;">

    <h2 style="color: black; margin-top:20px; text-transform: uppercase; font-weight: bold;">Resultados</h2>

    <?php if (isset($ingresosEgresos) && is_array($ingresosEgresos)) : ?>
        <ul style="list-style: none; font-size:20px; font-weight:bold">
            <?php foreach ($ingresosEgresos as $registro) : ?>
                <?php
                // Formatear la fecha
                $fechaFormateada = date('d-m-Y', strtotime($registro->fecha));
                ?>
                <li><?= strtoupper($registro->tipo) ?>: <span style="color: #0da6f3; font-weight: bold;"><?= $registro->cantidad ?></span> en <?= $fechaFormateada ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No hay datos de ingresos y egresos.</p>
    <?php endif; ?>

    <h2 style="color: black; margin-top:20px; text-transform: uppercase; font-weight: bold;">Total por Semana</h2>

    <?php if (isset($totalPorSemana) && is_array($totalPorSemana)) : ?>
        <ul>
            <?php foreach ($totalPorSemana as $semana => $totales) : ?>
                <li  style="list-style: none; font-size:20px; text-transform: uppercase; font-weight: bold;">
                    Semana <?= $semana ?> - Ingresos: <span style="color: #0da6f3; font-weight: bold;"><?= $totales['ingreso'] ?></span>,
                    Egresos: <span style="color: #0da6f3; font-weight: bold;"><?= $totales['egreso'] ?></span> -
                    Total: <span style="color: #0da6f3; font-weight: bold;"><?= $totales['ingreso'] - $totales['egreso'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No hay datos de total por semana.</p>
    <?php endif; ?>


<?php include_once __DIR__ . "/footer-dashboard.php"; ?>
