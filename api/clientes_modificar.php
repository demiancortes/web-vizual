<?php
require __DIR__ . '/../db.php';

$data = json_decode(file_get_contents('php://input'), true);

// 🔹 ID
$id = isset($data['id']) ? intval($data['id']) : 0;
if ($id <= 0) {
	echo json_encode(['ok' => false, 'error' => 'ID inválido']);
	exit;
}

// 🔹 Nombre
$nombre = isset($data['nombre']) ? trim($data['nombre']) : '';
if ($nombre === '') {
	echo json_encode(['ok' => false, 'error' => 'El nombre es obligatorio']);
	exit;
}

// 🔹 Teléfono
$telefono = isset($data['telefono']) ? trim($data['telefono']) : '';
if ($telefono === '' || !preg_match('/^\d{7,}$/', $telefono)) {
	echo json_encode(['ok' => false, 'error' => 'Teléfono inválido']);
	exit;
}

// 🔹 Domicilio
$domicilio = isset($data['domicilio']) ? trim($data['domicilio']) : '';
if ($domicilio === '') {
	echo json_encode(['ok' => false, 'error' => 'El domicilio es obligatorio']);
	exit;
}

// 🔹 Fraccionamiento
$fraccionamiento = isset($data['fraccionamiento']) ? trim($data['fraccionamiento']) : '';
if ($fraccionamiento === '') {
	echo json_encode(['ok' => false, 'error' => 'El fraccionamiento es obligatorio']);
	exit;
}

// 🔹 Ubicación
$ubicacion = isset($data['ubicacion']) ? trim($data['ubicacion']) : '';
if ($ubicacion === '') {
	echo json_encode(['ok' => false, 'error' => 'La ubicación es obligatoria']);
	exit;
}

// 🔹 Importes
$total = isset($data['total']) ? floatval($data['total']) : -1;
$anticipo = isset($data['anticipo']) ? floatval($data['anticipo']) : -1;

if ($total <= 0) {
	echo json_encode(['ok' => false, 'error' => 'Total inválido']);
	exit;
}

if ($anticipo < 0) {
	echo json_encode(['ok' => false, 'error' => 'Anticipo inválido']);
	exit;
}

$pendiente = $total - $anticipo;
if ($pendiente < 0) {
	echo json_encode(['ok' => false, 'error' => 'El anticipo no puede ser mayor al total']);
	exit;
}

// 🔹 Update
$sql = "
	UPDATE clientes SET
		nombre = :nombre,
		telefono = :telefono,
		domicilio = :domicilio,
		fraccionamiento = :fraccionamiento,
		ubicacion = :ubicacion,
		total = :total,
		anticipo = :anticipo,
		pendiente = :pendiente
	WHERE id = :id
";

$stmt = $pdo->prepare($sql);

$ok = $stmt->execute([
	':nombre' => $nombre,
	':telefono' => $telefono,
	':domicilio' => $domicilio,
	':fraccionamiento' => $fraccionamiento,
	':ubicacion' => $ubicacion,
	':total' => $total,
	':anticipo' => $anticipo,
	':pendiente' => $pendiente,
	':id' => $id
]);

if (!$ok) {
	echo json_encode(['ok' => false, 'error' => 'No se pudo actualizar el cliente']);
	exit;
}

echo json_encode(['ok' => true]);
