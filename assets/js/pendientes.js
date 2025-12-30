/* =========================
   Estado en memoria
   ========================= */
let pendientesData = [];
let pendientesFiltrados = [];

/* =========================
   Init de la vista
   ========================= */
function initPendientes() {
	initBuscadorPendientes();
	cargarPendientes();
}

/* =========================
   Fetch
   ========================= */
function cargarPendientes() {
	mostrarOverlay('pendientes', 'Cargando pendientes…');
	fetch('api/ventas_pendientes.php')
		.then(r => r.json())
		.then(data => {
			pendientesData = data;
			pendientesFiltrados = data;
			renderPendientes();
		})
		.catch(err => {
			console.error(err);
			const cont = document.getElementById('pendientesResultado');
			if (cont) {
				cont.innerHTML = `
					<div class="alert alert-secondary">
						Error al cargar pendientes
					</div>`;
			}
		})
		.finally(() => {
			ocultarOverlay('pendientes');
		});
}

/* =========================
   Buscador (cliente + fraccionamiento)
   ========================= */
function initBuscadorPendientes() {
	const input = document.getElementById('buscadorPendientes');
	if (!input) return;

	// Evitar listeners duplicados
	input.replaceWith(input.cloneNode(true));
	const newInput = document.getElementById('buscadorPendientes');

	newInput.addEventListener('input', e => {
		const q = e.target.value.toLowerCase().trim();

		pendientesFiltrados = pendientesData.filter(v =>
			(v.nombre || '').toLowerCase().includes(q) ||
			(v.fraccionamiento || '').toLowerCase().includes(q)
		);

		renderPendientes();
	});
}

/* =========================
   Render principal
   ========================= */
function renderPendientes() {
	const cont = document.getElementById('pendientesResultado');

	if (!pendientesFiltrados.length) {
		cont.innerHTML = `
			<div class="alert alert-secondary">
				No hay pendientes de instalar en este mes
			</div>`;
		actualizarBadgeTotal(0);
		return;
	}

	/* =========================
	   Total general por recibir
	   (una sola vez por cliente)
	   ========================= */
	const clientesUnicos = {};
	pendientesFiltrados.forEach(v => {
		if (!clientesUnicos[v.nombre]) {
			clientesUnicos[v.nombre] = Number(v.pendiente || 0);
		}
	});

	const totalGeneral = Object.values(clientesUnicos)
		.reduce((a, b) => a + b, 0);

	actualizarBadgeTotal(totalGeneral);

	let html = '';
	let clienteActual = null;

	pendientesFiltrados.forEach(v => {

		/* =========================
		   Cambio de cliente
		   ========================= */
		if (clienteActual !== v.nombre) {

			if (clienteActual !== null) {
				html += `
							</tbody>
						</table>
					</div>
				</div>
			</div>`;
			}

			clienteActual = v.nombre;

			html += `
			<div class="card mb-3 shadow-sm">
				<div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
					<div>
						<div class="fw-bold">
							${v.nombre} | 📞 (${formatoTelefono(v.telefono)})
						</div>
						<small>📍 ${v.fraccionamiento}</small>
					</div>
					<span class="badge bg-success">
						$${Number(v.pendiente || 0).toLocaleString()}
					</span>
				</div>

				<div class="card-body p-0">
					<div class="table-scroll table-scroll-pendientes">
						<table class="table table-sm mb-0">

							<colgroup>
								<col style="width:14%">
								<col style="width:8%">
								<col style="width:26%">
								<col style="width:16%">
								<col style="width:10%">
								<col style="width:16%">
							</colgroup>

							<thead class="table-light">
								<tr>
									<th>Fecha</th>
									<th>Tipo</th>
									<th>Modelo</th>
									<th>Medida</th>
									<th class="text-center">Días</th>
									<th title="🟢 A tiempo&#10;🟠 En proceso&#10;🔴 Retrasado">
										Estado
									</th>
								</tr>
							</thead>
				<tbody>`;
		}

		/* =========================
		   Badge tipo
		   ========================= */
		let badgeTipo = 'secondary';
		if (v.tipo === 'P') badgeTipo = 'primary';
		if (v.tipo === 'C') badgeTipo = 'dark';
		if (v.tipo === 'T') badgeTipo = 'success';

		/* =========================
		   Badge estado
		   ========================= */
		let badgeEstado = 'secondary';
		let textoEstado = '—';

		if (v.estado === 'verde') {
			badgeEstado = 'success';
			textoEstado = 'A tiempo';
		}
		if (v.estado === 'amarillo') {
			badgeEstado = 'warning text-dark';
			textoEstado = 'En proceso';
		}
		if (v.estado === 'rojo') {
			badgeEstado = 'danger';
			textoEstado = 'Retrasado';
		}

		/* =========================
		   Fila
		   ========================= */
		html += `
			<tr>
				<td>${formatoFecha(v.fecha_cotizacion)}</td>
				<td><span class="badge bg-${badgeTipo}">${v.tipo}</span></td>
				<td>${v.modelo || '—'}</td>
				<td>${v.medida_real || (v.largo && v.alto ? `${v.largo} x ${v.alto}` : '—')}</td>
				<td class="text-center">${v.dias_habiles}</td>
				<td>
					<span class="badge bg-${badgeEstado}">
						${textoEstado}
					</span>
				</td>
			</tr>`;
	});

	/* =========================
	   Cierre final
	   ========================= */
	html += `
				</tbody>
			</table>
		</div>
	</div>
	</div>`;

	cont.innerHTML = html;
}

/* =========================
   Badge total (header)
   ========================= */
function actualizarBadgeTotal(monto) {
	const cont = document.getElementById('badgeTotalPendientes');
	if (!cont) return;

	if (!monto) {
		cont.innerHTML = '';
		return;
	}

	cont.innerHTML = `
		<span class="badge bg-success">
			Pendiente: $${monto.toLocaleString()}
		</span>
	`;
}

/* =========================
   Registro de la vista
   ========================= */
window.initViews = window.initViews || {};
window.initViews.pendientes = initPendientes;
