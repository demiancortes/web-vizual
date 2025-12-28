<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<title>Vizual | Dashboard</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

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
	<script src="assets/js/utils.js"></script>
	<script src="assets/js/dashboard.js"></script>
	<script src="assets/js/ventas.js"></script>
	<script src="assets/js/router.js"></script>
	<script src="assets/js/app.js"></script>
</body>
</html>