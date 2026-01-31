<div class="modal fade" id="modalPedidoTemporal" tabindex="-1">
	<div class="modal-dialog modal-md modal-dialog-centered">
		<div class="modal-content">

			<!-- Header -->
			<div class="modal-header py-2">
				<h6 class="modal-title">🛒 Pedido temporal</h6>
				<button
					type="button"
					class="btn-close"
					data-bs-dismiss="modal"
					onclick="this.blur()">
				</button>
			</div>

			<!-- Body -->
			<div class="modal-body">

				<!-- Clientes (solo control visual) -->
				<div id="pedidoClientes" class="small text-muted mb-2"></div>

				<!-- Items del pedido -->
				<div id="pedidoItems"></div>

				<!-- Total -->
				<div class="border-top pt-2 mt-2 d-flex justify-content-between fw-semibold">
					<span>Total</span>
					<span id="pedidoTotal">$0.00</span>
				</div>

			</div>

			<!-- Footer -->
			<div class="modal-footer py-2">
				<button
					class="btn btn-secondary btn-sm"
					data-bs-dismiss="modal"
					onclick="this.blur()">
					Cerrar
				</button>
				<button
					class="btn btn-outline-danger btn-sm"
					onclick="limpiarPedido()">
					Limpiar
				</button>
				<button
					class="btn btn-primary btn-sm"
					onclick="copiarPedido()">
					Copiar
				</button>
			</div>

		</div>
	</div>
</div>
