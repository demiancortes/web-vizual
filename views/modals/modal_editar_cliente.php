<div class="modal fade" id="modalEditarCliente" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
		<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

		<div class="modal-content">

			<div class="modal-header py-2">
				<h6 class="modal-title">✏️ Editar cliente</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="this.blur()"></button>
			</div>
			<div id="alertEditarCliente" class="alert d-none mb-2 small" role="alert"></div>

			<div class="modal-body">
				<input type="hidden" id="editClienteId">

				<!-- Cliente / Teléfono -->
				<div class="row g-2 mb-2">
					<div class="col-6">
						<label for="editClienteNombre" class="form-label small mb-1">Nombre</label>
						<input type="text" id="editClienteNombre" class="form-control form-control-sm">
					</div>
					<div class="col-6">
						<label for="editClienteTelefono" class="form-label small mb-1">Teléfono</label>
						<input type="text" id="editClienteTelefono" class="form-control form-control-sm">
					</div>
				</div>

				<!-- Dirección / Fraccionamiento -->
				<div class="row g-2 mb-2">
					<div class="col-6">
						<label for="editClienteDomicilio" class="form-label small mb-1">Domicilio</label>
						<input type="text" id="editClienteDomicilio" class="form-control form-control-sm">
					</div>
					<div class="col-6">
						<label for="editClienteFraccionamiento" class="form-label small mb-1">Fraccionamiento</label>
						<input type="text" id="editClienteFraccionamiento" class="form-control form-control-sm">
					</div>
				</div>

				<!-- Importes -->
				<div class="row g-2 mb-2">
					<div class="col-4">
						<label for="editClienteTotal" class="form-label small mb-1">Total</label>
						<div class="input-group input-group-sm">
							<span class="input-group-text">$</span>
							<input type="text" oninput="soloNumerosDecimal(this)" id="editClienteTotal" class="form-control">
						</div>
					</div>

					<div class="col-4">
						<label for="editClienteAnticipo" class="form-label small mb-1">Anticipo</label>
						<div class="input-group input-group-sm">
							<span class="input-group-text">$</span>
							<input type="text" oninput="soloNumerosDecimal(this)" id="editClienteAnticipo" class="form-control">
						</div>
					</div>

					<div class="col-4">
						<label for="editClientePendiente" class="form-label small mb-1">Pendiente</label>
						<div class="input-group input-group-sm">
							<span class="input-group-text">$</span>
							<input type="text" oninput="soloNumerosDecimal(this)" id="editClientePendiente" class="form-control" readonly>
						</div>
					</div>
				</div>


				<!-- Ubicación -->
				<div class="mb-2">
					<label for="editClienteUbicacion" class="form-label small mb-1">Ubicación</label>
					<select id="editClienteUbicacion" class="form-select form-select-sm">
						<option value="">Seleccionar</option>
					</select>
				</div>

			</div>

			<div class="modal-footer py-2">
				<button class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="this.blur()">
					Cancelar
				</button>
				<button class="btn btn-success btn-sm" onclick="guardarEdicionCliente()">
					Guardar
				</button>
			</div>
		</div>
	</div>
</div>