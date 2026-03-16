<h5 class="mb-2">📅 Calendario Estratégico</h5>

<div class="position-relative">

	<!-- Overlay -->
	<div id="overlay-calendario"
	     class="position-absolute top-0 start-0 w-100 h-100 d-none"
	     style="background: rgba(255,255,255,.85); z-index: 10;">
		<div class="d-flex justify-content-center align-items-center h-100">
			<div class="text-center">
				<div class="spinner-border text-secondary mb-2"></div>
				<div class="fw-semibold" id="overlay-text-calendario">
					Cargando calendario…
				</div>
			</div>
		</div>
	</div>

	<!-- Filtros -->
	<div class="row g-2 mb-3 align-items-end">
		<div class="col-6 col-md-2">
			<label class="form-label small mb-1">Año</label>
			<select id="calAnio" class="form-select form-select-sm"></select>
		</div>
		<div class="col-6 col-md-2">
			<label class="form-label small mb-1">Mes</label>
			<select id="calMes" class="form-select form-select-sm"></select>
		</div>
		<div class="col-6 col-md-2">
			<button class="btn btn-dark w-100" onclick="cargarCalendario()">
				Consultar
			</button>
		</div>
	</div>

	<div id="calendarioResultado"></div>

</div>