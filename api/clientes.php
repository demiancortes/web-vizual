<?php
require __DIR__ . '/../db.php';

$sql = "
SELECT
	c.id,
	c.nombre,
	c.telefono,
	c.domicilio,
	c.fraccionamiento,
	c.ubicacion,
	COUNT(v.id) AS cantidad,
	SUM(v.total) AS total,
	MAX(v.fecha_cotizacion) AS ultima_fecha
FROM clientes c
JOIN ventas v ON v.cliente_id = c.id
GROUP BY
	c.id
ORDER BY ultima_fecha DESC, c.id DESC 
";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$data = [];

while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$data[] = [
		'nombre'          => $r['nombre'],
		'telefono'        => $r['telefono'],
		'domicilio'       => $r['domicilio'],
		'fraccionamiento' => $r['fraccionamiento'],
		'ubicacion'       => $r['ubicacion'],
		'cantidad'        => (int)$r['cantidad'],
		'total'           => (float)$r['total'],
		'fecha'           => $r['ultima_fecha']
	];
}

header('Content-Type: application/json');
echo json_encode($data);
