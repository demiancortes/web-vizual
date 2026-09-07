<h5 class="mb-2">🧮 Cotizador </h5>


<div class="position-relative">

	<!-- OVERLAY (nuevo, no rompe nada) -->
	<div id="overlay-cotizador"
	class="position-absolute top-0 start-0 w-100 h-100 d-none"
	style="background: rgba(255,255,255,.85); z-index: 10;">
	<div class="d-flex justify-content-center align-items-center h-100">
		<div class="text-center">
			<div class="spinner-border text-secondary mb-2"></div>
			<div class="fw-semibold" id="overlay-text-cotizador">
				Cargando cotizador…
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
			id="buscadorCortina"
			class="form-control"
			placeholder="🔍 Cotizar cortina (Tela 100X100)">
		</div>
	</div>
</div>

<div id="cortinaResultado"></div>
</div>