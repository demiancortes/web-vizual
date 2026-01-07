<?php
require __DIR__ . '/../db.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

/* ======================================================
   ORÍGENES DE VENTA
   ====================================================== */
   if ($accion === 'origenes') {

   	$sql = "SELECT UPPER(ubicacion) AS origen FROM clientes WHERE ubicacion IS NOT NULL AND ubicacion <> '' GROUP BY UPPER(ubicacion) ORDER BY origen ASC";

   	$stmt = $pdo->prepare($sql);
   	$stmt->execute();

   	$origenes = $stmt->fetchAll(PDO::FETCH_COLUMN);

   	echo json_encode($origenes);
   	exit;
   }


/* ======================================================
   MODELOS (DESDE VENTAS)
   ====================================================== */
   if ($accion === 'modelos') {

   	$sql = "SELECT tipo, modelo AS modelo, MAX(precio) AS precio, COUNT(modelo) nVeces 
   	FROM ventas 
   	WHERE tipo IN ('P','C','T') AND modelo IS NOT NULL AND modelo <> '' AND YEAR(fecha_cotizacion) >= 2025 
   	GROUP BY tipo, modelo ORDER BY CASE tipo WHEN 'P' THEN 1 WHEN 'C' THEN 2 WHEN 'T' THEN 3 END, nVeces DESC, modelo ASC";

   	$stmt = $pdo->prepare($sql);
   	$stmt->execute();

   	$modelos = $stmt->fetchAll(PDO::FETCH_ASSOC);

   	echo json_encode($modelos);
   	exit;
   }
