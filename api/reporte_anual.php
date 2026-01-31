<?php
require __DIR__ . '/../db.php';

$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');

$meses = array(
	1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
	5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
	9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
);

/* =========================
   VENTAS
   ========================= */
$sql = "
SELECT
	MONTH(fecha_cotizacion) AS mes,
	SUM(total) AS ventas,
	SUM(costo) AS costo,
	SUM(ganancia) AS ganancia
FROM ventas
WHERE YEAR(fecha_cotizacion) = ?
GROUP BY mes
";
$stmt = $pdo->prepare($sql);
$stmt->execute(array($anio));

$ventas = array();
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$publicidad[$r['mes']] = $r['total'];
}

/* =========================
   ARMADO FINAL
   ========================= */
$data = array();

$Tventa = 0;
$Tcosto = 0;
$Tgan   = 0;
$Tgas   = 0;
$Tpub   = 0;

foreach ($meses as $m => $nombre) {

	$v  = isset($ventas[$m]['ventas'])   ? (float)$ventas[$m]['ventas']   : 0;
	$c  = isset($ventas[$m]['costo'])    ? (float)$ventas[$m]['costo']    : 0;
	$g  = isset($ventas[$m]['ganancia']) ? (float)$ventas[$m]['ganancia'] : 0;
	$ga = isset($gasolina[$m])            ? (float)$gasolina[$m]           : 0;
	$pu = isset($publicidad[$m])          ? (float)$publicidad[$m]         : 0;

	$neta = $g - $ga - $pu;

	$Tventa += $v;
	$Tcosto += $c;
	$Tgan   += $g;
	$Tgas   += $ga;
	$Tpub   += $pu;

	$data[] = array(
		'mes'        => $nombre,
		'ventas'     => $v,
		'costo'      => $c,
		'ganancia'   => $g,
		'gasolina'   => $ga,
		'publicidad' => $pu,
		'neta'       => $neta
	);
}

/* TOTAL */
$data[] = array(
	'mes'        => 'TOTAL',
	'ventas'     => $Tventa,
	'costo'      => $Tcosto,
	'ganancia'   => $Tgan,
	'gasolina'   => $Tgas,
	'publicidad' => $Tpub,
	'neta'       => ($Tgan - $Tgas - $Tpub)
);

echo json_encode($data);
