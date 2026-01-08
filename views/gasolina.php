<h5 class="mb-2">⛽ Gastos de Gasolina</h5>

<div class="position-relative">

	<!-- Overlay -->
	<div id="overlay-gasolina"
	     class="position-absolute top-0 start-0 w-100 h-100 d-none"
	     style="background: rgba(255,255,255,.85); z-index: 10;">
		<div class="d-flex justify-content-center align-items-center h-100">
			<div class="text-center">
				<div class="spinner-border text-secondary mb-2"></div>
				<div class="fw-semibold" id="overlay-text-gasolina">
					Cargando gasolina…
				</div>
			</div>
		</div>
	</div>

	<!-- Fila 1: controles -->
	<div class="row g-2 mb-2 align-items-end">

		<div class="col-6 col-md-2">
			<label for="gasolinaDesde" class="form-label small mb-1">Desde</label>
			<input type="date" id="gasolinaDesde" class="form-control form-control-sm">
		</div>

		<div class="col-6 col-md-2">
			<label for="gasolinaHasta" class="form-label small mb-1">Hasta</label>
			<input type="date" id="gasolinaHasta" class="form-control form-control-sm">
		</div>

		<div class="col-6 col-md-2">
			<button class="btn btn-dark w-100" onclick="cargarGasolina()">
				Consultar
			</button>
		</div>

		<div class="col-6 col-md-2">
			<button class="btn btn-success w-100" onclick="abrirModalGasolina()">
				➕ Agregar
			</button>
		</div>

	</div>

	<!-- Fila 2: badge -->
	<div class="row mb-3">
		<div class="col-12">
			<span id="badgeTotalGasolina"></span>
		</div>
	</div>

	<!-- Tabla -->
	<div id="gasolinaResultado"></div>
</div>
<?php include __DIR__ . '/modals/modal_gasolina.php'; ?>