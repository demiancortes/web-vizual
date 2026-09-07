<h5 class="mb-2">🔗 Enlaces</h5>


<div class="position-relative">

	<!-- OVERLAY (nuevo, no rompe nada) -->
	<div id="overlay-enlace"
	class="position-absolute top-0 start-0 w-100 h-100 d-none"
	style="background: rgba(255,255,255,.85); z-index: 10;">
	<div class="d-flex justify-content-center align-items-center h-100">
		<div class="text-center">
			<div class="spinner-border text-secondary mb-2"></div>
			<div class="fw-semibold" id="overlay-text-enlace">
				Cargando enlace…
			</div>
		</div>
	</div>
</div>

<div class="row g-2 mb-3 align-items-center">

    <!-- BUSCADOR -->
    <div class="col-12 col-md">
        <div class="input-group w-100">
            <input
                type="search"
                id="buscadorEnlace"
                class="form-control"
                placeholder="🔎 Buscar slug, enlace, descripción"
                oninput="filtrarEnlaces()">
        </div>
    </div>

    <!-- AGREGAR -->
    <div class="col-12 col-md-auto">
        <button
            class="btn btn-success w-100"
            onclick="abrirModalEnlace()">
            ➕ Agregar
        </button>
    </div>

</div>

<div id="enlaceResultado"></div>
</div>
<?php include __DIR__ . '/modals/modal_enlace.php'; ?>