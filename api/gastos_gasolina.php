<?php
require_once 'auth.php';
require __DIR__ . '/../db.php';

/* =========================
   ELIMINAR GASTO
   ========================= */
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['eliminar'])) {
	$id = (int)$input['eliminar'];

	$stmt = $pdo->prepare("DELETE FROM gastos_gasolina WHERE id = :id");
	$ok = $stmt->execute([':id' => $id]);

	echo json_encode(['ok' => $ok]);
	exit;
}

/* =========================
   INSERTAR GASTO
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$fecha = isset($input['fecha']) ? $input['fecha'] : '';
	$monto = isset($input['monto']) ? $input['monto'] : '';
	$nota  = isset($input['nota']) && trim($input['nota']) !== ''
		? trim($input['nota'])
		: 'Carga gasolina';

	if ($fecha === '' || $monto === '') {
		echo json_encode(['ok' => false]);
		exit;
	}

	$sql = "
		INSERT INTO gastos_gasolina (fecha, monto, nota)
		VALUES (:fecha, :monto, :nota)
	";
	$stmt = $pdo->prepare($sql);
	$ok = $stmt->execute([
		':fecha' => $fecha,
		':monto' => $monto,
		':nota'  => $nota
	]);

	echo json_encode(['ok' => $ok]);
	exit;
}

/* =========================
   CONSULTA
   ========================= */
$desde = isset($_GET['desde']) ? $_GET['desde'] : '';
$hasta = isset($_GET['hasta']) ? $_GET['hasta'] : '';

if ($desde === '' || $hasta === '') {
	echo json_encode([]);
	exit;
}

$sql = "
	SELECT id, fecha, monto, nota
	FROM gastos_gasolina
	WHERE fecha BETWEEN :desde AND :hasta
	ORDER BY fecha DESC, id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
	':desde' => $desde,
	':hasta' => $hasta
]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
