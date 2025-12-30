<h5 class="mb-2">📋 Ventas del mes</h5>

<div class="position-relative">

	<!-- OVERLAY (nuevo, no rompe nada) -->
	<div id="overlay-ventas"
	class="position-absolute top-0 start-0 w-100 h-100 d-none"
	style="background: rgba(255,255,255,.85); z-index: 10;">
	<div class="d-flex justify-content-center align-items-center h-100">
		<div class="text-center">
			<div class="spinner-border text-secondary mb-2"></div>
			<div class="fw-semibold" id="overlay-text-ventas">
				Cargando ventas…
			</div>
		</div>
	</div>
</div>

<!-- 📅 FECHAS + BOTONES -->
<div class="row g-2 align-items-end mb-2">

	<div class="col-6 col-md-3">
		<label for="ventasDesde" class="form-label small mb-1">Desde</label>
		<input type="date" id="ventasDesde" class="form-control form-control-sm">
	</div>

	<div class="col-6 col-md-3">
		<label for="ventasHasta" class="form-label small mb-1">Hasta</label>
		<input type="date" id="ventasHasta" class="form-control form-control-sm">
	</div>

	<div class="col-md-6 d-flex gap-2">
		<button id="ventasBuscar" class="btn btn-primary btn-sm w-100">
			<i class="bi bi-search"></i> Buscar
		</button>
		<button id="ventasReset" class="btn btn-outline-secondary btn-sm w-100">
			<i class="bi bi-arrow-repeat"></i> Mes actual
		</button>
	</div>
</div>

<!-- 🔍 BUSCADOR + BADGES -->

<div class="row g-2 mb-3 align-items-center">
	<!-- BUSCADOR -->
	<div class="col-12 col-md-6">
		<input
		id="ventasBuscador"
		type="text"
		class="form-control"
		placeholder="🔍 Buscar por cliente, fraccionamiento o modelo… (Enter)">
	</div>


	<!-- BADGES (SCROLL EN MÓVIL) -->
	<div class="col-12 col-md-6">
		<div class="ventas-badges" id="ventasBadges">

			<span class="badge bg-primary">
				Ventas: <span id="bVentas">0</span>
			</span>

			<span class="badge bg-info">
				Persianas: <span id="bPersianas">0</span>
			</span>

			<span class="badge bg-dark text-white">
				Total: $<span id="bTotal">0</span>
			</span>

			<span class="badge bg-success">
				Ganancia: $<span id="bGanancia">0</span>
			</span>

		</div>
	</div>
</div>
<div id="ventasResultado"></div>
</div>