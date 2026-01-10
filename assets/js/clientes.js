/* =========================
   Estado en memoria
   ========================= */
let clientesData = [];
let clientesFiltrados = [];
let formatosClienteBindeados = false;

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
							<th class="text-center">✏️📋</th>
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
						class="btn btn-sm btn-outline-primary me-1"
						title="Editar cliente"
						onclick="abrirModalEditarCliente(${c.id})">
						✏️
					</button>

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

function recalcularPendienteCliente() {

	formatearImporte(editClienteTotal);
	formatearImporte(editClienteAnticipo);

	const total = parseFloat(editClienteTotal.value) || 0;
	const anticipo = parseFloat(editClienteAnticipo.value) || 0;

	editClientePendiente.value = (total - anticipo).toFixed(2);
}

function bindFormatoImportesCliente() {

	if (formatosClienteBindeados) return;
	formatosClienteBindeados = true;

	editClienteTotal.addEventListener('blur', recalcularPendienteCliente);
	editClienteAnticipo.addEventListener('blur', recalcularPendienteCliente);
}

function cargarUbicacionesCliente() {

	const sel = document.getElementById('editClienteUbicacion');
	if (!sel) return;

	// 🔑 Si el select ya tiene opciones reales, no recargar
	if (sel.options.length > 1) return;

	fetch('api/venta.php?accion=origenes')
	.then(r => r.json())
	.then(data => {
		if (!Array.isArray(data)) return;

		sel.innerHTML = '<option value="">Seleccionar</option>';

		data.forEach(ubicacion => {
			const opt = document.createElement('option');
			opt.value = ubicacion;
			opt.textContent = ubicacion;
			sel.appendChild(opt);
		});
	});
}

function abrirModalEditarCliente(id) {

	const c = clientesData.find(x => x.id == id);
	if (!c) return;

	// Cargar catálogo si no existe
	cargarUbicacionesCliente();

	// Asignar valores
	editClienteId.value = c.id;
	editClienteNombre.value = c.nombre || '';
	editClienteTelefono.value = c.telefono || '';
	editClienteDomicilio.value = c.domicilio || '';
	editClienteFraccionamiento.value = c.fraccionamiento || '';
	editClienteTotal.value = c.total || 0;
	editClienteAnticipo.value = c.anticipo || 0;
	editClientePendiente.value = c.pendiente || 0;

	// Formatear valores iniciales
	formatearImporte(editClienteTotal);
	formatearImporte(editClienteAnticipo);
	formatearImporte(editClientePendiente);

	// 🔑 Bind SOLO la primera vez
	bindFormatoImportesCliente();

	const modalEl = document.getElementById('modalEditarCliente');

	const modal = new bootstrap.Modal(modalEl, {
		focus: false
	});

	// Cuando el modal ya esté visible
	modalEl.addEventListener('shown.bs.modal', () => {
		editClienteUbicacion.value = c.ubicacion || '';
		editClienteNombre.focus();
	}, { once: true });

	modal.show();
}

function guardarEdicionCliente() {

	const id = editClienteId.value;
	if (!id) return;

	// 🔹 Nombre
	const nombre = editClienteNombre.value.trim();
	if (!nombre) {
		mostrarAlerta('warning', 'Captura el nombre del cliente', 'alertEditarCliente');
		editClienteNombre.focus();
		return;
	}

	// 🔹 Teléfono
	const telefono = editClienteTelefono.value.trim();
	if (!telefono) {
		mostrarAlerta('warning', 'Captura el teléfono del cliente', 'alertEditarCliente');
		editClienteTelefono.focus();
		return;
	}
	if (!/^\d{7,}$/.test(telefono)) {
		mostrarAlerta(
			'warning',
			'El teléfono debe contener solo números y al menos 7 dígitos',
			'alertEditarCliente'
		);
		editClienteTelefono.focus();
		return;
	}

	// 🔹 Domicilio
	const domicilio = editClienteDomicilio.value.trim();
	if (!domicilio) {
		mostrarAlerta('warning', 'Captura el domicilio del cliente', 'alertEditarCliente');
		editClienteDomicilio.focus();
		return;
	}

	// 🔹 Fraccionamiento
	const fraccionamiento = editClienteFraccionamiento.value.trim();
	if (!fraccionamiento) {
		mostrarAlerta('warning', 'Captura el fraccionamiento del cliente', 'alertEditarCliente');
		editClienteFraccionamiento.focus();
		return;
	}

	// 🔹 Ubicación
	const ubicacion = editClienteUbicacion.value;
	if (!ubicacion) {
		mostrarAlerta('warning', 'Selecciona la ubicación del cliente', 'alertEditarCliente');
		editClienteUbicacion.focus();
		return;
	}

	// 🔹 Total
	const total = parseFloat(editClienteTotal.value);
	if (isNaN(total) || total <= 0) {
		mostrarAlerta('warning', 'Captura un total válido mayor a 0', 'alertEditarCliente');
		editClienteTotal.focus();
		return;
	}

	// 🔹 Anticipo
	const anticipo = parseFloat(editClienteAnticipo.value);
	if (isNaN(anticipo) || anticipo < 0) {
		mostrarAlerta('warning', 'Captura un anticipo válido', 'alertEditarCliente');
		editClienteAnticipo.focus();
		return;
	}

	// 🔹 Pendiente (regla negocio)
	const pendiente = total - anticipo;
	if (pendiente < 0) {
		mostrarAlerta(
			'warning',
			'El anticipo no puede ser mayor al total',
			'alertEditarCliente'
		);
		editClienteAnticipo.focus();
		return;
	}

	// 🔹 Payload
	const payload = {
		id,
		nombre,
		telefono,
		domicilio,
		fraccionamiento,
		ubicacion,
		total,
		anticipo,
		pendiente
	};

	// 🔹 Envío
	fetch('api/clientes_modificar.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(payload)
	})
	.then(r => r.json())
	.then(resp => {

		if (!resp.ok) {
			mostrarAlerta(
				'danger',
				resp.error || 'Error al guardar los cambios',
				'alertEditarCliente'
			);
			return;
		}

		mostrarAlerta(
			'success',
			'Cliente actualizado correctamente',
			'alertEditarCliente'
		);

		setTimeout(() => {
			const modal = bootstrap.Modal.getInstance(
				document.getElementById('modalEditarCliente')
			);
			modal.hide();
			cargarClientes();
		}, 600);
	})
	.catch(() => {
		mostrarAlerta(
			'danger',
			'Error de conexión',
			'alertEditarCliente'
		);
	});
}

/* =========================
   Registro de la vista
   ========================= */
window.initViews = window.initViews || {};
window.initViews.clientes = initClientes;