<?php
require __DIR__ . '/../db.php';

/* =========================
   Rango: mes actual
   ========================= */
   $desde = date('Y-m-01');
   $hasta = date('Y-m-t');

/* =========================
   Query base
   ========================= */
   $sql = "
   SELECT
   v.id,
   v.tipo,
   v.fecha_cotizacion,
   v.fecha_instalacion,
   v.modelo,
   v.medida_real,
   v.largo,
   v.alto,
   c.nombre,
   c.telefono,
   c.fraccionamiento, 
   c.pendiente, 
   c.id
   FROM ventas v
   JOIN clientes c ON c.id = v.cliente_id
   WHERE v.fecha_instalacion IS NULL
   AND v.fecha_cotizacion BETWEEN :desde AND :hasta
   ORDER BY v.fecha_cotizacion ASC,
   c.nombre ASC,
   v.id ASC
   ";

   $stmt = $pdo->prepare($sql);
   $stmt->execute([
   	':desde' => $desde,
   	':hasta' => $hasta
   ]);

   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   Función días hábiles
   (L–V, sin festivos)
   ========================= */
   function diasHabiles($inicio, $fin){
   	$inicio = new DateTime($inicio);
   	$fin    = new DateTime($fin);
   	$dias   = 0;

   	while ($inicio < $fin) {
   		$inicio->modify('+1 day');
		$diaSemana = $inicio->format('N'); // 1 (L) a 7 (D)
		if ($diaSemana < 6) {
			$dias++;
		}
	}
	return $dias;
}

/* =========================
   Procesar estados
   ========================= */
   $hoy = date('Y-m-d');
   $data = [];

   foreach ($rows as $r) {

   	$diasHabiles = diasHabiles($r['fecha_cotizacion'], $hoy);

   	/* Límites según tipo */
   	if ($r['tipo'] === 'C') {
   		$verdeMax    = 10;
   		$amarilloMax = 15;
	} else { // P y T
		$verdeMax    = 3;
		$amarilloMax = 6;
	}

	/* Estado */
	if ($diasHabiles <= $verdeMax) {
		$estado = 'verde';
	} elseif ($diasHabiles <= $amarilloMax) {
		$estado = 'amarillo';
	} else {
		$estado = 'rojo';
	}

	$data[] = [
		'id'               => $r['id'],
		'tipo'             => $r['tipo'],
		'nombre'           => $r['nombre'],
		'telefono'         => $r['telefono'],
		'fraccionamiento'  => $r['fraccionamiento'],
		'modelo'           => $r['modelo'],
		'fecha_cotizacion' => $r['fecha_cotizacion'],
		'medida_real'      => $r['medida_real'],
		'largo'            => $r['largo'],
		'alto'             => $r['alto'],
		'pendiente' 		 => $r['pendiente'],
		'dias_habiles'     => $diasHabiles,
		'estado'           => $estado, 
		'idCliente' 		 => $r['id']
	];
}

header('Content-Type: application/json');
echo json_encode($data);
