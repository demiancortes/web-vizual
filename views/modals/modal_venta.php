<div class="modal fade" id="modalVenta" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">

			<!-- ================= HEADER ================= -->
			<div class="modal-header py-2">
				<h6 class="modal-title">➕ Registrar venta</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="this.blur()"></button>
			</div>

			<!-- Alertas -->
			<div id="alertVenta" class="alert d-none mb-2 small" role="alert"></div>


			<!-- ================= BODY ================= -->
			<div class="modal-body">

				<!-- =================================================
				     CABECERA DE VENTA
				     ================================================= -->
				     <div class="text-muted small fw-semibold mb-2">Datos del cliente</div>

				     <div class="d-flex align-items-center mb-2">
				     	<label for="venFecha" class="form-label small mb-0 me-2">Fecha:</label>
				     	<input type="date" id="venFecha" class="form-control form-control-sm">
				     </div>

				     <div class="row g-2 mb-2">
				     	<div class="col-6">
				     		<label for="venCliente" class="form-label small mb-1">Cliente</label>
				     		<input type="text" id="venCliente" class="form-control form-control-sm">
				     	</div>
				     	<div class="col-6">
				     		<label for="venTelefono" class="form-label small mb-1">Teléfono</label>
				     		<input type="text" id="venTelefono" class="form-control form-control-sm">
				     	</div>
				     </div>

				     <div class="row g-2 mb-3">
				     	<div class="col-6">
				     		<label for="venDomicilio" class="form-label small mb-1">Dirección</label>
				     		<input type="text" id="venDomicilio" class="form-control form-control-sm">
				     	</div>
				     	<div class="col-6">
				     		<label for="venFraccionamiento" class="form-label small mb-1">Fraccionamiento</label>
				     		<input type="text" id="venFraccionamiento" class="form-control form-control-sm">
				     	</div>
				     </div>

				<!-- =================================================
				     IMPORTES
				     ================================================= -->
				     <div class="text-muted small fw-semibold mb-2">Importes</div>

				     <div class="row g-2 mb-3">
				     	<div class="col-4">
				     		<label for="venTotal" class="form-label small">Total</label>
				     		<div class="input-group input-group-sm">
				     			<span class="input-group-text">$</span>
				     			<input type="text" id="venTotal" class="form-control" inputmode="decimal">
				     		</div>
				     	</div>

				     	<div class="col-4">
				     		<label for="venAnticipo" class="form-label small">Anticipo</label>
				     		<div class="input-group input-group-sm">
				     			<span class="input-group-text">$</span>
				     			<input type="text" id="venAnticipo" class="form-control" inputmode="decimal">
				     		</div>
				     	</div>

				     	<div class="col-4">
				     		<label for="venPendiente" class="form-label small">Pendiente</label>
				     		<div class="input-group input-group-sm">
				     			<span class="input-group-text">$</span>
				     			<input type="text" id="venPendiente" class="form-control" readonly>
				     		</div>
				     	</div>
				     </div>

				<!-- =================================================
				     ORIGEN DE VENTA
				     ================================================= -->
				     <div class="text-muted small fw-semibold mb-2">Origen de la venta</div>
				     <div class="row g-2 mb-3">
				     	<div class="col-6">
				     		<select id="venOrigen" class="form-select form-select-sm">
				     			<option value="">Seleccionar</option>
				     		</select>
				     	</div>
				     </div>

				     <hr>

				     <!-- Estado de la venta -->
				     <div
				     id="estadoVenta"
				     class="alert alert-info py-1 px-2 mb-2 d-none small">
				     ℹ️ Venta en progreso
				 </div>


				<!-- =================================================
				     CAPTURA DE PERSIANA
				     ================================================= -->
				     <div class="text-muted small fw-semibold mb-2">Captura de persiana</div>

				     <!-- ========== BLOQUE A: MODELO / CADENA ========== -->
				     <div class="row g-2 mb-2">
				     	<div class="col-8">
				     		<!-- <label class="form-label small">Modelo</label>
				     		<select id="perModelo" class="form-select form-select-sm"></select> -->

				     		<!-- 🔍 Buscador de modelo -->
				     		<input
				     		type="text"
				     		id="perModeloBuscar"
				     		class="form-control form-control-sm mb-1"
				     		placeholder="Buscar modelo...">

				     		<select id="perModelo" class="form-select form-select-sm"></select>

				     	</div>

				     	<div class="col-4">
				     		<label for="perCadena" class="form-label small">Cadena</label>
				     		<select id="perCadena" class="form-select form-select-sm">
				     			<option value="IZQ">IZQ</option>
				     			<option value="DER">DER</option>
				     		</select>
				     	</div>
				     </div>

				     <!-- ========== BLOQUE B: MEDIDAS ========== -->
				     <div class="row g-2 mb-2">
				     	<div class="col-3">
				     		<label for="perAncho" class="form-label small">Ancho</label>
				     		<input type="text" id="perAncho" class="form-control form-control-sm" inputmode="decimal"
				     		placeholder="0.00">
				     	</div>

				     	<div class="col-3">
				     		<label for="perAlto" class="form-label small">Alto</label>
				     		<input type="text" id="perAlto" class="form-control form-control-sm" inputmode="decimal"
				     		placeholder="0.00">
				     	</div>

				     	<div class="col-6">
				     		<label for="perMedidaReal" class="form-label small">Medida real</label>
				     		<input type="text" id="perMedidaReal" class="form-control form-control-sm" readonly>
				     	</div>
				     </div>

				     <!-- ========== BLOQUE C: IMPORTES DE LA PERSIANA ========== -->
				     <div class="row g-2 mb-3 align-items-end">
				     	<div class="col-4">
				     		<label for="perTotal" class="form-label small">Total</label>
				     		<div class="input-group input-group-sm">
				     			<span class="input-group-text">$</span>
				     			<input type="text" id="perTotal" class="form-control" inputmode="decimal">
				     		</div>
				     	</div>

				     	<div class="col-4">
				     		<label for="perPrecio" class="form-label small">Precio</label>
				     		<div class="input-group input-group-sm">
				     			<span class="input-group-text">$</span>
				     			<input type="text" id="perPrecio" class="form-control" inputmode="decimal">
				     		</div>
				     	</div>

				     	<div class="col-4 d-flex align-items-end">
				     		<button class="btn btn-primary btn-sm" onclick="agregarPersiana()">
				     			➕ Agregar
				     		</button>
				     	</div>
				     </div>

				     <hr>

				<!-- =================================================
				     TABLA DE PERSIANAS
				     ================================================= -->
				     <div class="table-responsive table-striped">
				     	<table class="table table-sm table-bordered align-middle small mb-0 w-100 text-nowrap">

				     		<thead class="table-dark">
				     			<tr>
				     				<th>Modelo</th>
				     				<th>Cadena</th>
				     				<th>Medida</th>
				     				<th>Precio</th>
				     				<th>Costo</th>
				     				<th>Total</th>
				     				<th>Ganancia</th>
				     				<th width="90"></th>
				     			</tr>
				     		</thead>
				     		<tbody id="tablaPersianas"></tbody>
				     	</table>
				     </div>

				 </div>

				 <!-- ================= FOOTER ================= -->
				 <div class="modal-footer py-2">
				 	<button class="btn btn-secondary btn-sm" data-bs-dismiss="modal" onclick="this.blur()">Cancelar</button>
				 	<button
				 	id="btnGuardarVenta"
				 	type="button"
				 	class="btn btn-success btn-sm"
				 	onclick="guardarVenta()">
				 	Guardar
				 </button>

				</div>

			</div>
		</div>
	</div>

<!-- Modal Confirmación Genérica -->
<div class="modal fade" id="modalConfirmar" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
	<div class="modal-dialog modal-sm modal-dialog-centered">
		<div class="modal-content">

			<div class="modal-header py-2">
				<h6 class="modal-title" id="confirmarTitulo">Confirmar acción</h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" onclick="this.blur()"></button>
			</div>

			<div class="modal-body small" id="confirmarContenido"></div>

			<div class="modal-footer py-2">
				<button
					type="button"
					class="btn btn-secondary btn-sm"
					data-bs-dismiss="modal" onclick="this.blur()">
					Cancelar
				</button>

				<button
					type="button"
					class="btn btn-danger btn-sm"
					id="btnConfirmarAccion">
					Confirmar
				</button>
			</div>

		</div>
	</div>
</div>
