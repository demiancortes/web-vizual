<h5 class="mb-2">📢 Gastos de Publicidad</h5>

<div class="position-relative">

	<!-- Overlay -->
	<div id="overlay-publicidad"
	     class="position-absolute top-0 start-0 w-100 h-100 d-none"
	     style="background: rgba(255,255,255,.85); z-index: 10;">
		<div class="d-flex justify-content-center align-items-center h-100">
			<div class="text-center">
				<div class="spinner-border text-secondary mb-2"></div>
				<div class="fw-semibold" id="overlay-text-publicidad">
					Cargando publicidad…
				</div>
			</div>
		</div>
	</div>

	<!-- Filtros -->
	<div class="row g-2 mb-2 align-items-end">
		<div class="col-6 col-md-3">
			<label class="form-label small mb-1">Desde</label>
			<input type="date" id="publicidadDesde" class="form-control form-control-sm">
		</div>
		<div class="col-6 col-md-2">
			<label class="form-label small mb-1">Hasta</label>
			<input type="date" id="publicidadHasta" class="form-control form-control-sm">
		</div>
		<div class="col-6 col-md-2">
			<button class="btn btn-dark w-100" onclick="cargarPublicidad()">
				Consultar
			</button>
		</div>
		<div class="col-6 col-md-2">
			<button class="btn btn-success w-100" onclick="abrirModalPublicidad()">
				➕ Agregar
			</button>
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-12">
			<span id="badgeTotalPublicidad"></span>
		</div>
	</div>

	<div id="publicidadResultado"></div>
</div>
<?php include __DIR__ . '/modals/modal_publicidad.php'; ?>