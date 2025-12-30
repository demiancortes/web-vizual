<h5 class="mb-2">👥 Clientes</h5>

<div class="position-relative">

	<!-- OVERLAY (nuevo, no rompe nada) -->
	<div id="overlay-clientes"
	class="position-absolute top-0 start-0 w-100 h-100 d-none"
	style="background: rgba(255,255,255,.85); z-index: 10;">
	<div class="d-flex justify-content-center align-items-center h-100">
		<div class="text-center">
			<div class="spinner-border text-secondary mb-2"></div>
			<div class="fw-semibold" id="overlay-text-clientes">
				Cargando clientes…
			</div>
		</div>
	</div>
</div>

<div class="row g-2 mb-3 align-items-center">
	<!-- BUSCADOR -->
	<div class="col-12">
		<div class="input-group w-100">
			<input
			type="search"
			id="buscadorClientes"
			class="form-control"
			placeholder="🔍 Buscar nombre, fraccionamiento o teléfono">
		</div>
	</div>
</div>

<div id="clientesResultado"></div>
</div>