<div class="modal fade" id="modalGasolina" tabindex="-1">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header py-2">
				<h6 class="modal-title">➕ Agregar gasto</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="this.blur()"></button>
			</div>

			<div class="modal-body">
				<div class="mb-2">
					<label for="gasFecha" class="form-label small mb-1">Fecha</label>
					<input type="date" id="gasFecha" class="form-control form-control-sm">
				</div>

				<div class="mb-2">
					<label for="gasMonto" class="form-label small mb-1">Monto</label>
					<input type="number"
					id="gasMonto"
					class="form-control form-control-sm"
					step="0.01"
					placeholder="0.00">
				</div>

				<div>
					<label for="gasNota" class="form-label small mb-1">Nota</label>
					<input type="text"
					id="gasNota"
					class="form-control form-control-sm"
					placeholder="Opcional">
				</div>
			</div>

			<div class="modal-footer py-2">
				<button class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="this.blur()">
					Cancelar
				</button>
				<button class="btn btn-success btn-sm" onclick="guardarGasolina()">
					Guardar
				</button>
			</div>
		</div>
	</div>
</div>