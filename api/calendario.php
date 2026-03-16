<?php
require __DIR__ . '/../db.php';

$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : 0;
$mes  = isset($_GET['mes']) ? (int)$_GET['mes'] : 0;

if (!$anio || !$mes) {
	echo json_encode([]);
	exit;
}

$inicio = sprintf('%04d-%02d-01', $anio, $mes);
$fin = date("Y-m-t", strtotime($inicio));

$stmt = $pdo->prepare("
	SELECT 
	DATE(fecha_cotizacion) as fecha,
	SUM(total) as ventas_total,
	COUNT(DISTINCT cliente_id) as ventas_dia
	FROM ventas
	WHERE fecha_cotizacion BETWEEN :inicio AND :fin
	GROUP BY DATE(fecha_cotizacion)
	");

$stmt->execute([
	':inicio' => $inicio,
	':fin' => $fin
]);

$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Instalaciones */
$stmt2 = $pdo->prepare("
	SELECT 
	DATE(fecha_instalacion) as fecha,
	COUNT(id) as instaladas
	FROM ventas
	WHERE fecha_instalacion BETWEEN :inicio AND :fin
	GROUP BY DATE(fecha_instalacion)
	");

$stmt2->execute([
	':inicio' => $inicio,
	':fin' => $fin
]);

$instalaciones = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
	'ventas' => $ventas,
	'instalaciones' => $instalaciones
]);