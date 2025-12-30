<div class="modal fade" id="modalPublicidad" tabindex="-1">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">

			<div class="modal-header py-2">
				<h6 class="modal-title">➕ Agregar gasto de publicidad</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<div class="modal-body">
				<div class="mb-2">
					<label class="form-label small mb-1">Fecha</label>
					<input type="date" id="pubFecha" class="form-control form-control-sm">
				</div>

				<div class="mb-2">
					<label class="form-label small mb-1">Monto</label>
					<input type="number" id="pubMonto" class="form-control form-control-sm" step="0.01" placeholder="0.00">
				</div>

				<div>
					<label class="form-label small mb-1">Nota</label>
					<input type="text" id="pubNota" class="form-control form-control-sm" placeholder="Publicidad mes">
				</div>
			</div>

			<div class="modal-footer py-2">
				<button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
					Cancelar
				</button>
				<button class="btn btn-success btn-sm" onclick="guardarPublicidad()">
					Guardar
				</button>
			</div>

		</div>
	</div>
</div>