<div class="modal fade" id="modalPendientes" tabindex="-1">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">

			<div class="modal-header py-2">
				<h6 class="modal-title">📅 Confirmar instalación</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<div class="modal-body">
				<div class="mb-2">
					<label class="form-label small mb-1">Fecha de instalación</label>
					<input
						type="date"
						id="instFecha"
						class="form-control form-control-sm">
				</div>

				<div class="small text-muted">
					Esta acción marcará todas las persianas del cliente como instaladas.
				</div>
			</div>

			<div class="modal-footer py-2">
				<button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
					Cancelar
				</button>
				<button class="btn btn-success btn-sm" onclick="confirmarInstalacion()">
					Confirmar
				</button>
			</div>

		</div>
	</div>
</div>
