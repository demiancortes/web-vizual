<?php
require_once 'auth.php';
require __DIR__ . '/../db.php';

$input = json_decode(file_get_contents('php://input'), true);

$clienteId = isset($input['cliente_id']) ? (int)$input['cliente_id'] : 0;
$fecha     = isset($input['fecha_instalacion']) ? $input['fecha_instalacion'] : '';

if (!$clienteId || !$fecha) {
	echo json_encode(['ok' => false]);
	exit;
}

$sql = "
	UPDATE ventas
	SET fecha_instalacion = :fecha
	WHERE cliente_id = :cliente
	  AND fecha_instalacion IS NULL
";

$stmt = $pdo->prepare($sql);
$ok = $stmt->execute([
	':fecha'   => $fecha,
	':cliente' => $clienteId
]);

echo json_encode(['ok' => $ok]);
