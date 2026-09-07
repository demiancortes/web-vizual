<?php
require_once 'auth.php';
require __DIR__ . '/../db.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];

function responder($data, $status = 200) {
	http_response_code($status);
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

function validarSlug($slug) {
	return preg_match('/^[a-zA-Z0-9_-]+$/', $slug);
}

function rutaImagenes() {
	return '/var/www/persianasvizual/go/img/';
}

function extensionImagen($mime) {
	switch ($mime) {
		case 'image/jpeg': return 'jpg';
		case 'image/png': return 'png';
		case 'image/webp': return 'webp';
		default: return false;
	}
}

/* =========================
   GET
========================= */
if ($method === 'GET') {

	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

	if ($id > 0) {
		$stmt = $pdo->prepare("SELECT id, slug, titulo, descripcion, imagen, destino, clicks, ultimo_click, activo FROM links WHERE id = :id LIMIT 1");
		$stmt->execute([':id' => $id]);
		$enlace = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$enlace) {
			responder(['ok' => false, 'mensaje' => 'Enlace no encontrado'], 404);
		}

		responder($enlace);
	}

	$stmt = $pdo->query("SELECT id, slug, titulo, descripcion, imagen, destino, clicks, ultimo_click, activo FROM links ORDER BY id DESC");
	responder($stmt->fetchAll(PDO::FETCH_ASSOC));
}

/* =========================
   POST
========================= */
if ($method !== 'POST') {
	responder(['ok' => false, 'mensaje' => 'Método no permitido'], 405);
}

/*
 * El módulo usa FormData porque permite subir imágenes.
 * Por eso los datos llegan por $_POST, no por php://input.
 */
$accion = isset($_POST['accion']) ? trim($_POST['accion']) : '';


/* =========================
   TOGGLE
========================= */
if ($accion === 'toggle') {

	$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

	if ($id <= 0) {
		responder(['ok' => false, 'mensaje' => 'ID inválido']);
	}

	$stmt = $pdo->prepare("UPDATE links SET activo = IF(activo = 1, 0, 1) WHERE id = :id");
	$ok = $stmt->execute([':id' => $id]);

responder([
    'ok' => $ok,
    'mensaje' => $ok ? 'Enlace creado' : 'No se pudo crear',
    'errorInfo' => $stmt->errorInfo()
]);
}

/* =========================
   GUARDAR
========================= */
if ($accion === 'guardar') {

	$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
	$slug = isset($_POST['slug']) ? trim($_POST['slug']) : '';
	$titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
	$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
	$destino = isset($_POST['destino']) ? trim($_POST['destino']) : '';
	$activo = isset($_POST['activo']) ? (int)$_POST['activo'] : 1;
	$imagenActual = isset($_POST['imagenActual']) ? basename(trim($_POST['imagenActual'])) : '';

	if ($slug === '' || $titulo === '' || $destino === '') {
		responder(['ok' => false, 'mensaje' => 'Slug, título y destino son obligatorios']);
	}

	if (!validarSlug($slug)) {
		responder(['ok' => false, 'mensaje' => 'El slug solo puede contener letras, números, guiones y guion bajo']);
	}

	if (!preg_match('/^https?:\/\//i', $destino)) {
		responder(['ok' => false, 'mensaje' => 'El destino debe comenzar con http:// o https://']);
	}

	$activo = $activo === 0 ? 0 : 1;

	/* =========================
	   REGISTRO ACTUAL
	========================= */
	$registroActual = null;

	if ($id > 0) {
		$stmt = $pdo->prepare("SELECT id, slug, imagen FROM links WHERE id = :id LIMIT 1");
		$stmt->execute([':id' => $id]);
		$registroActual = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$registroActual) {
			responder(['ok' => false, 'mensaje' => 'Enlace no encontrado']);
		}
	}

	$archivoSubido = isset($_FILES['imagenArchivo']) && $_FILES['imagenArchivo']['error'] !== UPLOAD_ERR_NO_FILE;
	$nuevaImagen = null;
	$archivoTemporal = null;
	$extensionNueva = null;

	/* =========================
	   VALIDAR IMAGEN
	========================= */
	if ($archivoSubido) {

		$archivo = $_FILES['imagenArchivo'];

		if ($archivo['error'] !== UPLOAD_ERR_OK) {
			responder(['ok' => false, 'mensaje' => 'No se pudo recibir la imagen']);
		}

		if ($archivo['size'] > 5 * 1024 * 1024) {
			responder(['ok' => false, 'mensaje' => 'La imagen no puede superar 5 MB']);
		}

		$info = @getimagesize($archivo['tmp_name']);

		if ($info === false) {
			responder(['ok' => false, 'mensaje' => 'El archivo no es una imagen válida']);
		}

		if ((int)$info[0] !== 1200 || (int)$info[1] !== 630) {
			responder(['ok' => false, 'mensaje' => 'La imagen debe medir exactamente 1200 × 630 px']);
		}

		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mime = $finfo->file($archivo['tmp_name']);
		$extensionNueva = extensionImagen($mime);

		if ($extensionNueva === false) {
			responder(['ok' => false, 'mensaje' => 'La imagen debe ser JPG, PNG o WebP']);
		}

		$archivoTemporal = $archivo['tmp_name'];
		$nuevaImagen = $slug . '.' . $extensionNueva;
	}

	$directorio = rutaImagenes();

	if (!is_dir($directorio) && !mkdir($directorio, 0755, true)) {
		responder(['ok' => false, 'mensaje' => 'No se pudo acceder al directorio de imágenes']);
	}

	$imagenFinal = $imagenActual !== '' ? $imagenActual : null;

	try {

		/* =========================
		   CREAR
		========================= */
		if ($id <= 0) {

			$imagenFinal = $nuevaImagen;

			if ($imagenFinal && file_exists($directorio . $imagenFinal)) {
				responder(['ok' => false, 'mensaje' => 'Ya existe una imagen con ese slug']);
			}

			if ($archivoSubido) {
				if (!move_uploaded_file($archivoTemporal, $directorio . $imagenFinal)) {
					responder(['ok' => false, 'mensaje' => 'No se pudo guardar la imagen']);
				}
			} else {
				$imagenFinal = null;
			}

			$stmt = $pdo->prepare("INSERT INTO links (slug, titulo, descripcion, imagen, destino, activo) VALUES (:slug, :titulo, :descripcion, :imagen, :destino, :activo)");

			$ok = $stmt->execute([
				':slug' => $slug,
				':titulo' => $titulo,
				':descripcion' => $descripcion,
				':imagen' => $imagenFinal,
				':destino' => $destino,
				':activo' => $activo
			]);

			if (!$ok && $imagenFinal && file_exists($directorio . $imagenFinal)) {
				@unlink($directorio . $imagenFinal);
			}

			responder([
				'ok' => $ok,
				'mensaje' => $ok ? 'Enlace creado' : 'No se pudo crear'
			]);
		}

		/* =========================
		   ACTUALIZAR
		========================= */
		$slugAnterior = $registroActual['slug'];
		$imagenAnterior = $registroActual['imagen'];

		if ($archivoSubido) {

			$imagenFinal = $nuevaImagen;
			$rutaNueva = $directorio . $imagenFinal;

			if ($imagenAnterior && $imagenAnterior !== $imagenFinal && file_exists($directorio . $imagenAnterior)) {
				@unlink($directorio . $imagenAnterior);
			}

			if (file_exists($rutaNueva) && $imagenAnterior !== $imagenFinal) {
				responder(['ok' => false, 'mensaje' => 'Ya existe una imagen con ese slug']);
			}

			if (!move_uploaded_file($archivoTemporal, $rutaNueva)) {
				responder(['ok' => false, 'mensaje' => 'No se pudo guardar la imagen']);
			}

		} elseif ($imagenAnterior) {

			$extensionAnterior = strtolower(pathinfo($imagenAnterior, PATHINFO_EXTENSION));
			$imagenFinal = $slug . ($extensionAnterior ? '.' . $extensionAnterior : '');

			if ($imagenAnterior !== $imagenFinal && file_exists($directorio . $imagenAnterior)) {

				if (file_exists($directorio . $imagenFinal)) {
					responder(['ok' => false, 'mensaje' => 'Ya existe una imagen con el nuevo slug']);
				}

				if (!rename($directorio . $imagenAnterior, $directorio . $imagenFinal)) {
					responder(['ok' => false, 'mensaje' => 'No se pudo renombrar la imagen']);
				}
			}

		} else {
			$imagenFinal = null;
		}

		$stmt = $pdo->prepare("UPDATE links SET slug = :slug, titulo = :titulo, descripcion = :descripcion, imagen = :imagen, destino = :destino, activo = :activo WHERE id = :id");

		$ok = $stmt->execute([
			':slug' => $slug,
			':titulo' => $titulo,
			':descripcion' => $descripcion,
			':imagen' => $imagenFinal,
			':destino' => $destino,
			':activo' => $activo,
			':id' => $id
		]);

		if (!$ok && $imagenFinal && $imagenFinal !== $imagenAnterior && file_exists($directorio . $imagenFinal)) {
			@unlink($directorio . $imagenFinal);
		}

		responder([
			'ok' => $ok,
			'mensaje' => $ok ? 'Enlace actualizado' : 'No se pudo actualizar'
		]);

	} catch (PDOException $e) {

		if (isset($e->errorInfo[1]) && (int)$e->errorInfo[1] === 1062) {
			responder(['ok' => false, 'mensaje' => 'Ese slug ya existe']);
		}

		responder(['ok' => false, 'mensaje' => 'Error de base de datos']);
	}
}

responder(['ok' => false, 'mensaje' => 'Acción no válida']);
