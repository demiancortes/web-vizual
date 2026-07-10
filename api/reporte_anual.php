<?php
require_once 'auth.php';
require __DIR__ . '/../db.php';

$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');

$meses = array(
	1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
	5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
	9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
);

/* =========================
	VENTAS
	========================= */
	$sql = "
	SELECT
	MONTH(fecha_cotizacion) AS mes,
	SUM(total) AS ventas,
	SUM(costo) AS costo,
	SUM(ganancia) AS ganancia,
	COUNT(DISTINCT cliente_id) AS clientes
	FROM ventas
	WHERE YEAR(fecha_cotizacion) = ?
	GROUP BY mes
	";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($anio));

	$ventas = array();
	while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$ventas[$r['mes']] = $r;
	}

	/* =========================
	DESGLOSE POR UBICACION
	========================= */
	$sql = "
	SELECT
	MONTH(v.fecha_cotizacion) AS mes,
	COALESCE(c.ubicacion, 'Otros') AS ubicacion,
	SUM(v.total) AS ventas,
	SUM(v.costo) AS costo,
	SUM(v.ganancia) AS ganancia
	FROM ventas v
	LEFT JOIN clientes c ON v.cliente_id = c.id
	WHERE YEAR(v.fecha_cotizacion) = ?
	GROUP BY mes, ubicacion
	";

	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($anio));

	$desglose = array();

	while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

		$mes = (int)$r['mes'];
		$ubi = trim($r['ubicacion']);

		if (!isset($desglose[$mes])) {
			$desglose[$mes] = array(
				'publicidad' => array('ventas'=>0,'costo'=>0,'ganancia'=>0),
				'recomendacion' => array('ventas'=>0,'costo'=>0,'ganancia'=>0),
				'otros' => array('ventas'=>0,'costo'=>0,'ganancia'=>0)
			);
		}

		$ventasU = round((float)$r['ventas']);
		$costoU = round((float)$r['costo']);
		$gananciaU = round((float)$r['ganancia']);

		if (strcasecmp($ubi, 'PUBLICIDAD') === 0) {

			$desglose[$mes]['publicidad']['ventas'] += $ventasU;
			$desglose[$mes]['publicidad']['costo'] += $costoU;
			$desglose[$mes]['publicidad']['ganancia'] += $gananciaU;

		} elseif (strcasecmp($ubi, 'RECOMENDACIÓN') === 0) {

			$desglose[$mes]['recomendacion']['ventas'] += $ventasU;
			$desglose[$mes]['recomendacion']['costo'] += $costoU;
			$desglose[$mes]['recomendacion']['ganancia'] += $gananciaU;

		} else {

			$desglose[$mes]['otros']['ventas'] += $ventasU;
			$desglose[$mes]['otros']['costo'] += $costoU;
			$desglose[$mes]['otros']['ganancia'] += $gananciaU;
		}
	}

/* =========================
	GASOLINA
	========================= */
	$sql = "
	SELECT
	MONTH(fecha) AS mes,
	SUM(monto) AS total
	FROM gastos_gasolina
	WHERE YEAR(fecha) = ?
	GROUP BY mes
	";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($anio));

	$gasolina = array();
	while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$gasolina[$r['mes']] = $r['total'];
	}

/* =========================
	PUBLICIDAD
	========================= */
	$sql = "
	SELECT
	MONTH(fecha) AS mes,
	SUM(monto) AS total,
	SUM(mensajes) AS mensajes,
	SUM(citas) AS citas
	FROM gastos_publicidad
	WHERE YEAR(fecha)=?
	GROUP BY mes
	";
	$stmt = $pdo->prepare($sql);
	$stmt->execute(array($anio));

	$publicidad = array();

	while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {

		$publicidad[$r['mes']] = array(
			'total' => (float)$r['total'],
			'mensajes' => (int)$r['mensajes'],
			'citas' => (int)$r['citas']
		);

	}

/* =========================
	ARMADO FINAL
	========================= */
	$data = array();

	$Tventa = 0;
	$Tcosto = 0;
	$Tgan   = 0;
	$Tgas   = 0;
	$Tpub   = 0;

	foreach ($meses as $m => $nombre) {

		$v  = isset($ventas[$m]['ventas'])   ? round((float)$ventas[$m]['ventas'])   : 0;
		$c  = isset($ventas[$m]['costo'])    ? round((float)$ventas[$m]['costo'])    : 0;
		$g  = isset($ventas[$m]['ganancia']) ? round((float)$ventas[$m]['ganancia']) : 0;
		
		$ga = isset($gasolina[$m])
		? round((float)$gasolina[$m])
		: 0;

		$pu = isset($publicidad[$m]['total'])
		? round((float)$publicidad[$m]['total'])
		: 0;

		$mensajes = isset($publicidad[$m]['mensajes'])
		? (int)$publicidad[$m]['mensajes']
		: 0;

		$citas = isset($publicidad[$m]['citas'])
		? (int)$publicidad[$m]['citas']
		: 0;

		$clientes = isset($ventas[$m]['clientes'])
		? (int)$ventas[$m]['clientes']
		: 0;

		$neta = $g - $ga - $pu;

		$Tventa += $v;
		$Tcosto += $c;
		$Tgan   += $g;
		$Tgas   += $ga;
		$Tpub   += $pu;

		$embudo = array(

			'mensajes' => $mensajes,
			'citas' => $citas,
			'clientes' => $clientes,

			'costoMensaje' => $mensajes > 0
			? round($pu / $mensajes)
			: 0,

			'costoCita' => $citas > 0
			? round($pu / $citas)
			: 0,

			'costoCliente' => $clientes > 0
			? round($pu / $clientes)
			: 0,

			'convMensajeCita' => $mensajes > 0
			? round(($citas / $mensajes) * 100, 1)
			: 0,

			'convCitaCliente' => $citas > 0
			? round(($clientes / $citas) * 100, 1)
			: 0,

			'convMensajeCliente' => $mensajes > 0
			? round(($clientes / $mensajes) * 100, 1)
			: 0

		);

		$data[] = array(
			'mes' => $nombre,
			'ventas' => $v,
			'costo' => $c,
			'ganancia' => $g,
			'gasolina' => $ga,
			'publicidad' => $pu,
			'neta' => $neta,
			'desglose' => isset($desglose[$m]) ? $desglose[$m] : array(
				'publicidad'=>array('ventas'=>0,'costo'=>0,'ganancia'=>0),
				'recomendacion'=>array('ventas'=>0,'costo'=>0,'ganancia'=>0),
				'otros'=>array('ventas'=>0,'costo'=>0,'ganancia'=>0)
			),
			'embudo' => $embudo
		);
	}

	/* TOTAL */
	$data[] = array(
		'mes'        => 'TOTAL',
		'ventas'     => $Tventa,
		'costo'      => $Tcosto,
		'ganancia'   => $Tgan,
		'gasolina'   => $Tgas,
		'publicidad' => $Tpub,
		'neta'       => ($Tgan - $Tgas - $Tpub)
	);

	echo json_encode($data);
