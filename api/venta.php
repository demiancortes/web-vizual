<?php
require_once 'auth.php';
require __DIR__ . '/../db.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

/* ======================================================
ORÍGENES DE VENTA
====================================================== */

if ($accion === 'origenes') {

	// Orígenes por defecto
	$origenes_base = ["MARKETPLACE","PUBLICIDAD","RECOMENDACIÓN","RECOMENDACIÓN"];

	// Obtener de la BD
	$sql = "SELECT UPPER(ubicacion) AS origen 
	FROM clientes 
	WHERE ubicacion IS NOT NULL 
	AND ubicacion <> '' 
	GROUP BY UPPER(ubicacion)";

	$stmt = $pdo->prepare($sql);
	$stmt->execute();

	$origenes_db = $stmt->fetchAll(PDO::FETCH_COLUMN);

	// Unir y eliminar duplicados
	$origenes = array_unique(array_merge($origenes_base, $origenes_db));

	// Ordenar
	sort($origenes);

	echo json_encode(array_values($origenes));
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
