<?php
require __DIR__ . '/../db.php';

$inicioMes = date('Y-m-01');
$hoy       = date('Y-m-d');

/* ===============================
   RESUMEN DEL MES ACTUAL
   =============================== */
   $sql = "
   SELECT
   COUNT(DISTINCT CONCAT(v.cliente_id,'-',v.fecha_cotizacion)) AS ventas,
   COUNT(*) AS persianas,
   SUM(v.total) AS total,
   SUM(v.ganancia) AS ganancia
   FROM ventas v
   WHERE v.fecha_cotizacion BETWEEN :desde AND :hasta
   ";

   $stmt = $pdo->prepare($sql);
   $stmt->execute([
   	':desde' => $inicioMes,
   	':hasta' => $hoy
   ]);

   $data = $stmt->fetch();

/* ===============================
   POR INSTALAR
   =============================== */
   $sqlPendientes = "
   SELECT COUNT(*)
   FROM ventas
   WHERE fecha_instalacion IS NULL
   AND fecha_cotizacion >= DATE_FORMAT(
      DATE_SUB(CURDATE(), INTERVAL 1 MONTH),
      '%Y-%m-01'
   )
   ";

   $stmt = $pdo->prepare($sqlPendientes);
   $stmt->execute();

   $pendientes = $stmt->fetchColumn();

/* ===============================
   RESPUESTA
   =============================== */
   $response = [
   	'ventas'     => (int)$data['ventas'],
   	'persianas'  => (int)$data['persianas'],
   	'total'      => (float)$data['total'],
   	'ganancia'   => (float)$data['ganancia'],
   	'ticket'     => $data['ventas'] > 0
   	? $data['total'] / $data['ventas']
   	: 0,
   	'pendientes' => (int)$pendientes
   ];

   echo json_encode($response);
