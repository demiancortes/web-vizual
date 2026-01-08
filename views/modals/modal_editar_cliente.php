<div class="modal fade" id="modalEditarCliente" tabindex="-1">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">

			<div class="modal-header py-2">
				<h6 class="modal-title">✏️ Editar cliente</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="this.blur()"></button>
			</div>

			<div class="modal-body">

				<input type="hidden" id="editClienteId">

				<div class="mb-2">
					<label for="editClienteNombre" class="form-label small mb-1">Nombre</label>
					<input type="text" id="editClienteNombre"
						class="form-control form-control-sm">
				</div>

				<div class="mb-2">
					<label for="editClienteTelefono" class="form-label small mb-1">Teléfono</label>
					<input type="text" id="editClienteTelefono"
						class="form-control form-control-sm">
				</div>

				<div class="mb-2">
					<label for="editClienteDomicilio" class="form-label small mb-1">Domicilio</label>
					<input type="text" id="editClienteDomicilio"
						class="form-control form-control-sm">
				</div>

				<div class="mb-2">
					<label for="editClienteFraccionamiento" class="form-label small mb-1">
						Fraccionamiento
					</label>
					<input type="text" id="editClienteFraccionamiento"
						class="form-control form-control-sm">
				</div>

				<div class="mb-2">
					<label for="editClienteUbicacion" class="form-label small mb-1">
						Ubicación
					</label>
					<select id="editClienteUbicacion"
						class="form-select form-select-sm">
						<option value="">Seleccionar</option>
					</select>
				</div>

				<div class="mb-2">
					<label for="editClienteTotal" class="form-label small mb-1">Total</label>
					<input type="number" step="0.01"
						id="editClienteTotal"
						class="form-control form-control-sm">
				</div>

				<div class="mb-2">
					<label for="editClienteAnticipo" class="form-label small mb-1">Anticipo</label>
					<input type="number" step="0.01"
						id="editClienteAnticipo"
						class="form-control form-control-sm">
				</div>

				<div>
					<label for="editClientePendiente" class="form-label small mb-1">Pendiente</label>
					<input type="number" step="0.01"
						id="editClientePendiente"
						class="form-control form-control-sm">
				</div>

			</div>

			<div class="modal-footer py-2">
				<button class="btn btn-secondary btn-sm"
					data-bs-dismiss="modal" onclick="this.blur()">
					Cancelar
				</button>
				<button class="btn btn-success btn-sm"
					onclick="guardarEdicionCliente()">
					Guardar
				</button>
			</div>

		</div>
	</div>
</div>
