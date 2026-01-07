<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$usuario = '';
	$password = '';

	if (isset($_POST['usuario'])) {
		$usuario = trim($_POST['usuario']);
	}

	if (isset($_POST['password'])) {
		$password = trim($_POST['password']);
	}

	if ($usuario === '' || $password === '') {
		$error = 'Captura usuario y contraseña';
	}
	else if ($usuario === 'admin' && $password === '1234') {

		$_SESSION['usuario'] = $usuario;
		header('Location: index.php');
		exit;
	}
	else {
		$error = 'Usuario o contraseña incorrectos';
	}
}
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Acceso | Control</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/app.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh">

	<div class="card shadow border-0" style="width:360px; max-width:90vw;">
		<div class="card-body px-4 py-4">

			<h5 class="text-center fw-bold mb-0">Control</h5>
			<small class="text-muted d-block text-center mb-4">
				Persianas Vizual
			</small>

			<form method="post" autocomplete="off" id="formLogin">

				<div class="mb-3">
					<input
						type="text"
						name="usuario"
						class="form-control py-2"
						placeholder="Usuario"
						autofocus>
				</div>

				<div class="mb-4">
					<input
						type="password"
						name="password"
						class="form-control py-2"
						placeholder="Contraseña">
				</div>

				<button class="btn btn-success w-100 py-2">
					🔐 Entrar
				</button>

			</form>

		</div>
	</div>

	<!-- Toast global (reutiliza el mismo que ya usas en la app) -->
	<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1100;">
		<div id="toastGlobal" class="toast align-items-center border-0 shadow" role="alert">
			<div class="d-flex">
				<div class="toast-body fw-semibold text-dark" id="toastGlobalTexto"></div>
				<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

	<script>
		// Función toast (misma lógica que tu app)
		function mostrarToast(mensaje, tipo) {

			var toastEl = document.getElementById('toastGlobal');
			var toastTexto = document.getElementById('toastGlobalTexto');
			if (!toastEl || !toastTexto) return;

			toastEl.className = 'toast align-items-center border-0 shadow';

			if (tipo === 'success') toastEl.classList.add('toast-success');
			if (tipo === 'warning') toastEl.classList.add('toast-warning');
			if (tipo === 'danger') toastEl.classList.add('toast-danger');

			toastTexto.textContent = mensaje;

			var toast = bootstrap.Toast.getOrCreateInstance(toastEl, {
				delay: 3000
			});
			toast.show();
		}

		<?php if ($error != ''): ?>
			// Mostrar error desde PHP
			mostrarToast('<?php echo $error; ?>', 'warning');
		<?php endif; ?>
	</script>

</body>
</html>
