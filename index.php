<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Vizual | Dashboard</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

	<!-- Bootstrap -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/app.css">
</head>

<body>
	<!-- 🔝 HEADER -->
	<div class="app-header">
		<strong></strong>
		<button class="btn btn-outline-primary btn-sm" onclick="cargarVista('dashboard')">
			<i class="bi bi-house"></i>
		</button>
	</div>

	<div id="appContent">
		<!-- Aquí se cargan las vistas -->
	</div>

	<!-- Bootstrap JS (SIN defer, SIN async) -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="assets/js/utils.js"></script>
	<script src="assets/js/helpers.js"></script>
	
	<script src="assets/js/dashboard.js"></script>
	<script src="assets/js/ventas.js"></script>
	<script src="assets/js/pendientes.js"></script>
	<script src="assets/js/clientes.js"></script>
	<script src="assets/js/gasolina.js"></script>
	<script src="assets/js/router.js"></script>
	<script src="assets/js/app.js"></script>
</body>
</html>