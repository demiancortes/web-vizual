<div class="modal fade" id="modalEnlace" tabindex="-1">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">

			<div class="modal-header py-2">
				<h6 class="modal-title" id="modalEnlaceTitulo">➕ Agregar enlace</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="this.blur()"></button>
			</div>

			<div class="modal-body">
				<input type="hidden" id="enlaceId">
				<input type="hidden" id="enlaceImagenActual">

				<div class="row g-2">

					<div class="col-md-4">
						<label for="enlaceSlug" class="form-label small mb-1">Slug</label>
						<input type="text" id="enlaceSlug" class="form-control form-control-sm" placeholder="informes" autocomplete="off">
						<div class="form-text">Se usará en /go/slug</div>
					</div>

					<div class="col-md-8">
						<label for="enlaceTitulo" class="form-label small mb-1">Título</label>
						<input type="text" id="enlaceTitulo" class="form-control form-control-sm" placeholder="Persianas Vizual Mazatlán">
					</div>

					<div class="col-12">
						<label for="enlaceDescripcion" class="form-label small mb-1">Descripción</label>
						<input type="text" id="enlaceDescripcion" class="form-control form-control-sm" placeholder="¿Buscas persianas para tu hogar? Cotiza sin costo">
					</div>

					<div class="col-md-6">
						<label for="enlaceImagenArchivo" class="form-label small mb-1">Imagen</label>
						<input type="file" id="enlaceImagenArchivo" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp" onchange="actualizarPreviewEnlace()">
						<div class="form-text">JPG, PNG o WebP · 1200 × 630 · máximo 5 MB</div>
					</div>

					<div class="col-md-6">
						<label for="enlaceDestino" class="form-label small mb-1">Destino</label>
						<input type="url" id="enlaceDestino" class="form-control form-control-sm" placeholder="https://wa.me/...">
					</div>

					<div class="col-md-6">
						<div class="form-check form-switch mt-3">
							<input class="form-check-input" type="checkbox" id="enlaceActivo" checked>
							<label class="form-check-label" for="enlaceActivo">Enlace activo</label>
						</div>
					</div>

					<div class="col-12 mt-2">
						<label class="form-label small mb-1">Vista previa</label>
						<div class="enlace-modal-preview">
							<img id="enlaceImagenPreview" src="" alt="Vista previa" style="display:none;">
							<div id="enlaceImagenSinPreview" class="text-muted small">Selecciona una imagen para verla aquí.</div>
						</div>
					</div>

				</div>
			</div>

			<div class="modal-footer py-2">
				<button class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="this.blur()">Cancelar</button>
				<button class="btn btn-success btn-sm" onclick="guardarEnlace()">Guardar</button>
			</div>

		</div>
	</div>
</div>
