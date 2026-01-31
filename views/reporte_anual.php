<h5 class="mb-2">📊 Reporte anual</h5>

<div class="position-relative">

	<!-- OVERLAY -->
	<div id="overlay-reporte-anual"
	     class="position-absolute top-0 start-0 w-100 h-100 d-none"
	     style="background: rgba(255,255,255,.85); z-index: 10;">
		<div class="d-flex justify-content-center align-items-center h-100">
			<div class="text-center">
				<div class="spinner-border text-secondary mb-2"></div>
				<div class="fw-semibold" id="overlay-text-reporte-anual">
					Cargando reporte…
				</div>
			</div>
		</div>
	</div>

	<!-- CONTROLES -->
	<div class="d-flex justify-content-between align-items-center mb-3">

		<select id="reporteAnio"
		        class="form-select w-auto"
		        onchange="cargarReporteAnual(this.value)">
		</select>
	</div>

	<div id="reporteAnualResultado"></div>
</div>
