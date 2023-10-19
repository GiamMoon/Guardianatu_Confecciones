<?php include_once __DIR__ . "/header-dashboard.php"; ?>

<style>
    table {
        width: 70%;
        margin: 0 auto;
        border-collapse: collapse;
        margin-top: 20px;
        margin-bottom: 100px; /* Agrega 100px de margen inferior */
    }

    th, td {
        border: 1px solid black;
        text-align: left;
        padding: 8px;
    }

    th {
        text-align: center;
        background-color: #f2f2f2;
    }

    button {
        background-color: #0da6f3;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 15px;
        text-transform: uppercase;
        font-weight: bold;
    }

    p {
        margin-top: 20px;
    }
</style>

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

        <button type="submit">Filtrar</button>

    </form>

</div>

<div style="color: black; text-align: center;">

    <h2 style="color: black; margin-top: 20px; text-transform: uppercase; font-weight: bold;">Resultados</h2>

    <?php
// ...

// Suponiendo que ya has incluido tus clases y establecido la conexión a la base de datos

// Obtener todos los clientes
$clientes = Model\Clientes::todos();

if ($clientes) :
?>
    <table>
        <thead>
            <tr>
                <th>Boleta</th>
                <th>Nombres</th>
                <th>Apellidos</th>
                <th>Total</th>
                <th>Adelanto</th>
                <th>Restante a Pagar</th>
                <th>Pago Confirmado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente) : ?>
                <tr>
                    <td><?= $cliente->boleta ?></td>
                    <td><?= $cliente->nombres ?></td>
                    <td><?= $cliente->apellidos ?></td>
                    <td><?= $cliente->precioTotal ?></td>
                    <td><?= $cliente->adelanto ?></td>
                    <td><?= $cliente->restantePagar ?></td>
                    <td><?= $cliente->pago_confirmado == 1 ? 'Sí' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <p>No hay clientes registrados.</p>
<?php endif; ?>


    <?php if (isset($ingresosEgresos) && is_array($ingresosEgresos)) : ?>
        <table>
            <thead>
            <tr>
                    <th colspan="3">EGRESOS</th>
                </tr>
                <tr>
                    <th>Concepto</th>
                    <th>Cantidad</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Array para hacer un seguimiento de las combinaciones únicas
                $combinacionesUnicas = [];

                foreach ($ingresosEgresos as $registro) :
                    // Si es un ingreso, omitirlo y continuar con la siguiente iteración
                    if ($registro->tipo == 'ingreso') {
                        continue;
                    }

                    // Formatear la fecha
                    $fechaFormateada = date('d-m-Y', strtotime($registro->fecha));

            

                        ?>
                        <tr>
                            <td><?= $registro->concepto ?></td>
                            <td><?= $registro->cantidad ?></td>
                            <td><?= $fechaFormateada ?></td>
                        </tr>
                        <?php

                endforeach;
                ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No hay datos de egresos.</p>
    <?php endif; ?>

    <h2 style="color: black; margin-top: 20px; text-transform: uppercase; font-weight: bold;">Total por Semana</h2>

    <?php
if (isset($totalPorSemana) && is_array($totalPorSemana)) {
    ksort($totalPorSemana); // Ordenar semanas de menor a mayor
    foreach ($totalPorSemana as $semana => $totales) :
?>
        <table>
            <thead>
                <tr>
                    <th colspan="3">Semana <?= $semana ?></th>
                </tr>
                <tr>
                    <th>Ingresos</th>
                    <th>Egresos</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="color: #0da6f3; font-weight: bold;"><?= $totales['ingreso'] ?></td>
                    <td style="color: #0da6f3; font-weight: bold;"><?= $totales['egreso'] ?></td>
                    <td style="color: #0da6f3; font-weight: bold;"><?= $totales['ingreso'] - $totales['egreso'] ?></td>
                </tr>
            </tbody>
        </table>
<?php
    endforeach;
} else {
    echo "<p>No hay datos de total por semana.</p>";
}
?>



</div>

<?php include_once __DIR__ . "/footer-dashboard.php"; ?>
