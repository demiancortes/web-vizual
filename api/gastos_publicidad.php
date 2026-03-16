<?php
require_once 'auth.php';
require __DIR__ . '/../db.php';

$input = json_decode(file_get_contents('php://input'), true);

/* =========================
   ELIMINAR
   ========================= */
   if (isset($input['eliminar'])) {
   	$id = (int)$input['eliminar'];

   	$stmt = $pdo->prepare("DELETE FROM gastos_publicidad WHERE id = :id");
   	$ok = $stmt->execute([':id' => $id]);

   	echo json_encode(['ok' => $ok]);
   	exit;
   }

/* =========================
   INSERTAR
   ========================= */
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   	$fecha = isset($input['fecha']) ? $input['fecha'] : '';
$monto = isset($input['monto']) ? $input['monto'] : '';
$mensajes = isset($input['mensajes']) ? (int)$input['mensajes'] : 0;
$citas = isset($input['citas']) ? (int)$input['citas'] : 0;
$nota = (isset($input['nota']) && trim($input['nota']) !== '')
	? trim($input['nota'])
	: 'Gasto publicidad';
	

   	if ($fecha === '' || $monto === '') {
   		echo json_encode(['ok' => false]);
   		exit;
   	}

   	$stmt = $pdo->prepare("
   		INSERT INTO gastos_publicidad
   		(fecha, monto, mensajes, citas, nota)
   		VALUES (:fecha, :monto, :mensajes, :citas, :nota)
   		");

   	$ok = $stmt->execute([
   		':fecha'    => $fecha,
   		':monto'    => $monto,
   		':mensajes' => $mensajes,
   		':citas'    => $citas,
   		':nota'     => $nota
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

   $stmt = $pdo->prepare("
   	SELECT id, fecha, monto, mensajes, citas, nota
   	FROM gastos_publicidad
   	WHERE fecha BETWEEN :desde AND :hasta
   	ORDER BY fecha DESC, id DESC
   	");

   $stmt->execute([
   	':desde' => $desde,
   	':hasta' => $hasta
   ]);

   echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
