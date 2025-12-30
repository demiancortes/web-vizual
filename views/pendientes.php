<h5 class="mb-2">🛠️ Pendientes de instalar</h5>


<div class="position-relative">

	<!-- OVERLAY (nuevo, no rompe nada) -->
	<div id="overlay-pendientes"
	class="position-absolute top-0 start-0 w-100 h-100 d-none"
	style="background: rgba(255,255,255,.85); z-index: 10;">
	<div class="d-flex justify-content-center align-items-center h-100">
		<div class="text-center">
			<div class="spinner-border text-secondary mb-2"></div>
			<div class="fw-semibold" id="overlay-text-pendientes">
				Cargando pendientes…
			</div>
		</div>
	</div>
</div>

<div id="pendientesHeader"class="row g-2 mb-3 align-items-center">
	<!-- BUSCADOR -->
	<div class="col-12 col-md-7">
		<div class="input-group w-100">
			<input
			type="search"
			id="buscadorPendientes"
			class="form-control"
			placeholder="🔍 Buscar cliente o fraccionamiento">
		</div>
	</div>
	<div class="col-12 col-md-5 text-md-end">
		<div id="badgeTotalPendientes"></div>
	</div>
</div>

<div id="pendientesResultado"></div>
</div>