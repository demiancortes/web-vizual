<?php
require __DIR__ . '/../db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
	echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
	exit;
}

try {

	$pdo->beginTransaction();

	/* ===============================
	   INSERT CLIENTE
	   =============================== */
	$sqlCliente = "
		INSERT INTO clientes
		(nombre, domicilio, fraccionamiento, telefono, total, anticipo, pendiente, ubicacion)
		VALUES
		(:nombre, :domicilio, :fraccionamiento, :telefono, :total, :anticipo, :pendiente, :ubicacion)
	";

	$stmt = $pdo->prepare($sqlCliente);
	$stmt->execute([
		':nombre'          => $data['cliente']['nombre'],
		':domicilio'       => $data['cliente']['domicilio'],
		':fraccionamiento' => $data['cliente']['fraccionamiento'],
		':telefono'        => $data['cliente']['telefono'],
		':total'           => $data['cliente']['total'],
		':anticipo'        => $data['cliente']['anticipo'],
		':pendiente'       => $data['cliente']['pendiente'],
		':ubicacion'       => $data['cliente']['ubicacion']
	]);

	$clienteId = $pdo->lastInsertId();

	/* ===============================
	   INSERT PERSIANAS (VENTAS)
	   =============================== */
	$sqlVenta = "
		INSERT INTO ventas
		(tipo, cliente_id, fecha_cotizacion, modelo, ctrl, medida_real, largo, alto, tam, total, precio, costo, ganancia)
		VALUES
		(:tipo, :cliente_id, :fecha, :modelo, :ctrl, :medida_real, :largo, :alto, :tam, :total, :precio, :costo, :ganancia)
	";

	$stmtVenta = $pdo->prepare($sqlVenta);

	foreach ($data['persianas'] as $p) {

		$ancho = $p['ancho'];
		$alto  = $p['alto'];

		$tam = ($ancho < 1 ? 1 : $ancho) * ($alto < 1 ? 1 : $alto);

		$stmtVenta->execute([
			':tipo'        => 'P',
			':cliente_id'  => $clienteId,
			':fecha'       => $data['cliente']['fecha'],
			':modelo'      => $p['modelo'],
			':ctrl'        => $p['cadena'],
			':medida_real' => $p['medida_real'],
			':largo'       => $ancho,
			':alto'        => $alto,
			':tam'         => $tam,
			':total'       => $p['total'],
			':precio'      => $p['precio'],
			':costo'       => $p['costo'],
			':ganancia'    => $p['ganancia']
		]);
	}

	$pdo->commit();

	echo json_encode(['ok' => true]);

} catch (Exception $e) {

	$pdo->rollBack();

	echo json_encode([
		'ok' => false,
		'error' => 'No se pudo guardar la venta'
	]);
}