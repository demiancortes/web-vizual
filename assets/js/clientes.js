/* =========================
   Estado en memoria
   ========================= */
let clientesData = [];
let clientesFiltrados = [];


/* =========================
   Init de la vista
   ========================= */
function initClientes() {
	initBuscadorClientes();
	cargarClientes();
}

/* =========================
   Fetch
   ========================= */
function cargarClientes() {
	mostrarOverlay('clientes', 'Cargando clientes…');
	fetch('api/clientes.php')
	.then(r => r.json())
	.then(data => {
		clientesData = data;
		clientesFiltrados = data;
		renderClientes();
	})
	.catch(err => {
		console.error(err);
		const cont = document.getElementById('clientesResultado');
		if (cont) {
			cont.innerHTML = `
					<div class="alert alert-secondary">
						Error al cargar clientes
			</div>`;
		}
	})
	.finally(() => {
			ocultarOverlay('clientes');
		});
}

/* =========================
   Buscador
   ========================= */
function initBuscadorClientes() {
	const input = document.getElementById('buscadorClientes');
	if (!input) return;

	input.replaceWith(input.cloneNode(true));
	const newInput = document.getElementById('buscadorClientes');

	newInput.addEventListener('input', e => {
		const q = e.target.value.toLowerCase().trim();

		clientesFiltrados = clientesData.filter(c =>
			(c.nombre || '').toLowerCase().includes(q) ||
			(c.fraccionamiento || '').toLowerCase().includes(q) ||
			(c.telefono || '').toLowerCase().includes(q)
		);

		renderClientes();
	});
}

/* =========================
   Render
   ========================= */
function renderClientes() {
	const cont = document.getElementById('clientesResultado');

	if (!clientesFiltrados.length) {
		cont.innerHTML = `
			<div class="alert alert-secondary">
				No hay clientes registrados
		</div>`;
		return;
	}

	let html = `
	<div class="card shadow-sm">
		<div class="card-body p-0">
			<div class="table-scroll table-scroll-clientes table-striped">
				<table class="table table-sm mb-0 ">
					<colgroup>
						<col style="width:110px">  <!-- Fecha -->
						<col style="width:220px">  <!-- Nombre -->
						<col style="width:160px">  <!-- Teléfono -->
						<col style="width:440px">  <!-- Domicilio -->
						<col style="width:140px">  <!-- Total -->
						<col style="width:90px">   <!-- Cant. -->
						<col style="width:120px">  <!-- Ubicación -->
						<col style="width:60px">   <!-- Copiar -->
					</colgroup>

					<thead class="table-dark">
						<tr>
							<th>Fecha</th>
							<th>Nombre</th>
							<th>Teléfono</th>
							<th>Domicilio</th>
							<th class="text-end">Total</th>
							<th class="text-center">Cant.</th>
							<th>Ubicación</th>
							<th class="text-center">📋</th>
						</tr>
					</thead>
					<tbody>
	`;

	clientesFiltrados.forEach(c => {
		html += `
			<tr>
				<td>${formatoFecha(c.fecha)}</td>
				<td>${c.nombre}</td>
				<td>${formatoTelefono(c.telefono)}</td>
				<td>${[c.domicilio, c.fraccionamiento].filter(Boolean).join(' · ') || '—'}</td>
				<td class="text-end">
					$${Number(c.total).toLocaleString()}
				</td>
				<td class="text-center">
					<span class="badge bg-secondary">
						${c.cantidad}
					</span>
				</td>
				<td>
					${badgeUbicacion(c.ubicacion)}
				</td>
				<td class="text-center">
					<button
						class="btn btn-sm btn-outline-secondary"
						title="Copiar cliente"
						onclick="copiarCliente('[VIZUAL] ${c.nombre} / ${c.domicilio} ${c.fraccionamiento} - ${c.telefono}')"
						>
						📋
					</button>
				</td>
			</tr>`;
	});

	html += `
					</tbody>
				</table>
			</div>
		</div>
	</div>
	`;

	cont.innerHTML = html;
}

/* =========================
   Registro de la vista
   ========================= */
window.initViews = window.initViews || {};
window.initViews.clientes = initClientes;
