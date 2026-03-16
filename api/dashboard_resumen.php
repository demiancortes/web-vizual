<?php
require_once 'auth.php';
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
   mejor día del mes
   =============================== */
   $sqlMejor = "
   SELECT fecha_cotizacion AS dia, SUM(total) total_dia
   FROM ventas v
   WHERE v.fecha_cotizacion BETWEEN :desde AND :hasta
   GROUP BY fecha_cotizacion ORDER BY total_dia DESC LIMIT 1;";

   $stmt = $pdo->prepare($sqlMejor);
   $stmt->execute([
      ':desde' => $inicioMes,
      ':hasta' => $hoy
   ]);

   $dataMejor = $stmt->fetch();

   $fecha = $dataMejor['dia']; // YYYY-MM-DD

   $meses = [
      1 => 'ene', 2 => 'feb', 3 => 'mar', 4 => 'abr',
      5 => 'may', 6 => 'jun', 7 => 'jul', 8 => 'ago',
      9 => 'sep', 10 => 'oct', 11 => 'nov', 12 => 'dic'
   ];

   $timestamp = strtotime($fecha);
   $dia = date('j', $timestamp);
   $mes = $meses[intval(date('n', $timestamp))];

   $fecha_formateada = $dia . ' ' . $mes;

/* ===============================
   RESPUESTA
   =============================== */
   $response = [
   	'ventas'     => (int)$data['ventas'],
   	'persianas'  => (int)$data['persianas'],
   	'total'      => (float)$data['total'],
   	'ganancia'   => (float)$data['ganancia'],
   	'ticket'     => $data['ventas'] > 0 ? $data['total'] / $data['ventas'] : 0,
   	'pendientes' => (int)$pendientes,
      'dia'  => $fecha_formateada, 
      'importe_dia'=> $dataMejor['total_dia']
   ];

   echo json_encode($response);
