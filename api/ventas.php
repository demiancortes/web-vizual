<?php
require_once 'auth.php';
require __DIR__ . '/../db.php';

$desde = !empty($_GET['desde'])
  ? $_GET['desde']
  : date('Y-m-01');

$hasta = !empty($_GET['hasta'])
  ? $_GET['hasta']
  : date('Y-m-d');


$sql = "
SELECT
  v.id,
  v.fecha_cotizacion,
  v.modelo,
  v.medida_real,
  v.largo,
  v.alto,
  v.total,
  v.costo,
  v.ganancia,
  c.nombre,
  c.telefono,
  c.fraccionamiento,
  c.domicilio,
  c.ubicacion, 
  v.ctrl, 
  v.cliente_id
FROM ventas v
JOIN clientes c ON c.id = v.cliente_id
WHERE v.fecha_cotizacion BETWEEN :desde AND :hasta
ORDER BY v.fecha_cotizacion ASC, v.id ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
  ':desde' => $desde,
  ':hasta' => $hasta
]);

echo json_encode($stmt->fetchAll());
