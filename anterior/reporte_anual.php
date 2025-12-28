<?php
require 'db.php';

$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

$meses = array(
  1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
  5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
  9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
);

function money($n){
  return '$'.number_format($n, 0);
}

/* =========================
   VENTAS
   ========================= */
$sql = "
SELECT
  MONTH(fecha_cotizacion) AS mes,
  SUM(total) AS total,
  SUM(costo) AS costo,
  SUM(ganancia) AS ganancia
FROM ventas
WHERE YEAR(fecha_cotizacion) = ?
GROUP BY mes
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array($anio));

$ventas = array();
while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
  $ventas[$r['mes']] = $r;
}

/* =========================
   GASOLINA
   ========================= */
$sql = "
SELECT
  MONTH(fecha) AS mes,
  SUM(monto) AS total
FROM gastos_gasolina
WHERE YEAR(fecha) = ?
GROUP BY mes
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array($anio));

$gasolina = array();
while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
  $gasolina[$r['mes']] = $r['total'];
}

/* =========================
   PUBLICIDAD
   ========================= */
$sql = "
SELECT
  MONTH(fecha) AS mes,
  SUM(monto) AS total
FROM gastos_publicidad
WHERE YEAR(fecha) = ?
GROUP BY mes
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array($anio));

$publicidad = array();
while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
  $publicidad[$r['mes']] = $r['total'];
}

/* Totales */
$Tventa = 0;
$Tcosto = 0;
$Tgan   = 0;
$Tgas  = 0;
$Tpub  = 0;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <strong>Reporte anual</strong>
  <select class="form-select w-auto"
          onchange="cargarReporteAnual(this.value)">
    <?php
    for($y = date('Y'); $y >= date('Y') - 5; $y--){
      echo '<option value="'.$y.'"'.($y==$anio?' selected':'').'>'.$y.'</option>';
    }
    ?>
  </select>
</div>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
  <thead class="table-dark">
    <tr>
      <th>Mes</th>
      <th class="text-end">Ventas</th>
      <th class="text-end">Costo</th>
      <th class="text-end">Ganancia</th>
      <th class="text-end">Gasolina</th>
      <th class="text-end">Publicidad</th>
      <th class="text-end">Ganancia neta</th>
    </tr>
  </thead>
  <tbody>

<?php
foreach($meses as $m => $nombre){

  $v  = isset($ventas[$m]['total'])    ? $ventas[$m]['total']    : 0;
  $c  = isset($ventas[$m]['costo'])    ? $ventas[$m]['costo']    : 0;
  $g  = isset($ventas[$m]['ganancia']) ? $ventas[$m]['ganancia'] : 0;
  $ga = isset($gasolina[$m])           ? $gasolina[$m]           : 0;
  $pu = isset($publicidad[$m])         ? $publicidad[$m]         : 0;

  $neta = $g - $ga - $pu;

  $Tventa += $v;
  $Tcosto += $c;
  $Tgan   += $g;
  $Tgas   += $ga;
  $Tpub   += $pu;

  echo '<tr>';
  echo '<td class="fw-semibold">'.$nombre.'</td>';
  echo '<td class="text-end">'.money($v).'</td>';
  echo '<td class="text-end">'.money($c).'</td>';
  echo '<td class="text-end text-success">'.money($g).'</td>';
  echo '<td class="text-end text-danger">'.money($ga).'</td>';
  echo '<td class="text-end text-danger">'.money($pu).'</td>';
  echo '<td class="text-end fw-bold '.($neta>=0?'text-success':'text-danger').'">'.money($neta).'</td>';
  echo '</tr>';
}
?>

<tr class="table-secondary fw-bold">
  <td>TOTAL</td>
  <td class="text-end"><?= money($Tventa) ?></td>
  <td class="text-end"><?= money($Tcosto) ?></td>
  <td class="text-end text-success"><?= money($Tgan) ?></td>
  <td class="text-end text-danger"><?= money($Tgas) ?></td>
  <td class="text-end text-danger"><?= money($Tpub) ?></td>
  <td class="text-end"><?= money($Tgan - $Tgas - $Tpub) ?></td>
</tr>

  </tbody>
</table>
</div>
