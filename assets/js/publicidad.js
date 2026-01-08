/* =========================
   Estado
   ========================= */
let publicidadData = [];
let modalPublicidad = null;
let guardandoPublicidad = false;

/* =========================
   Init vista
   ========================= */
function initPublicidad() {
	setMesActualPublicidad();
	cargarPublicidad();
}

/* =========================
   Fechas
   ========================= */
function setMesActualPublicidad() {
	const hoy = new Date();
	const inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
	const fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);

	document.getElementById('publicidadDesde').value =
		inicio.toISOString().slice(0,10);
	document.getElementById('publicidadHasta').value =
		fin.toISOString().slice(0,10);
}

/* =========================
   Fetch
   ========================= */
function cargarPublicidad() {
	const desde = document.getElementById('publicidadDesde').value;
	const hasta = document.getElementById('publicidadHasta').value;

	mostrarOverlay('publicidad', 'Cargando publicidad…');

	fetch(`api/gastos_publicidad.php?desde=${desde}&hasta=${hasta}`)
		.then(r => r.json())
		.then(data => {
			publicidadData = data;
			renderPublicidad();
		})
		.catch(() => {
			document.getElementById('publicidadResultado').innerHTML = `
				<div class="alert alert-secondary">
					Error al cargar publicidad
				</div>`;
		})
		.finally(() => ocultarOverlay('publicidad'));
}

/* =========================
   Render
   ========================= */
function renderPublicidad() {
	const cont = document.getElementById('publicidadResultado');

	if (!publicidadData.length) {
		cont.innerHTML = `
			<div class="alert alert-secondary">
				No hay gastos en este rango
			</div>`;
		actualizarTotalPublicidad(0);
		return;
	}

	let total = publicidadData.reduce(
		(acc, g) => acc + Number(g.monto || 0), 0
	);

	actualizarTotalPublicidad(total);

	let html = `
	<div class="card shadow-sm">
		<div class="card-body p-0">
			<div class="table-scroll">
				<table class="table table-sm mb-0 table-striped table-scroll-publicidad">
					<thead class="table-dark">
						<tr>
							<th style="width:110px">Fecha</th>
							<th style="width:100px">Monto</th>
							<th>Nota</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
	`;

	publicidadData.forEach(g => {
		html += `
			<tr>
				<td>${formatoFecha(g.fecha)}</td>
				<td class="text-end">
					$${Number(g.monto).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
				</td>
				<td class="text-wrap">${g.nota || '—'}</td>
				<td class="text-center">
					<button class="btn btn-sm btn-outline-danger"
						title="Eliminar este gasto"
						onclick="eliminarPublicidad(${g.id})">
						🗑️
					</button>
				</td>
			</tr>`;
	});

	html += `
					</tbody>
				</table>
			</div>
		</div>
	</div>`;

	cont.innerHTML = html;
}

/* =========================
   Total badge
   ========================= */
function actualizarTotalPublicidad(monto) {
	const cont = document.getElementById('badgeTotalPublicidad');
	if (!cont) return;

	if (!monto) {
		cont.innerHTML = '';
		return;
	}

	cont.innerHTML = `
		<span class="badge bg-success">
			Total: $${monto.toLocaleString()}
		</span>`;
}

/* =========================
   MODAL - Agregar Publicidad
   ========================= */
function abrirModalPublicidad() {
	cerrarModalesAbiertos(); // 👈 CLAVE
	if (!modalPublicidad) {
		modalPublicidad = new bootstrap.Modal(
			document.getElementById('modalPublicidad')
		);
	}

	const hoy = new Date().toLocaleDateString('en-CA');
	document.getElementById('pubFecha').value = hoy;
	document.getElementById('pubMonto').value = '';
	document.getElementById('pubNota').value = '';

	modalPublicidad.show();
}

function guardarPublicidad() {

	if (guardandoPublicidad) return;

	const fecha = document.getElementById('pubFecha').value;
	const monto = document.getElementById('pubMonto').value;
	const nota  = document.getElementById('pubNota').value;

	if (!fecha || !monto) {
		alert('Fecha y monto son obligatorios');
		return;
	}

	guardandoPublicidad = true;

	const btn = document.querySelector('#modalPublicidad .btn-success');
	if (btn) btn.disabled = true;

	mostrarOverlay('publicidad', 'Guardando gasto…');

	fetch('api/gastos_publicidad.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ fecha, monto, nota })
	})
	.then(r => r.json())
	.then(resp => {
		if (!resp.ok) {
			alert('No se pudo guardar el gasto');
			return;
		}
		modalPublicidad.hide();
		cargarPublicidad();
	})
	.catch(() => alert('Error al guardar'))
	.finally(() => {
		guardandoPublicidad = false;
		if (btn) btn.disabled = false;
		ocultarOverlay('publicidad');
	});
}

/* =========================
   Eliminar gasto
   ========================= */
function eliminarPublicidad(id) {
	if (!confirm('¿Eliminar este gasto?')) return;

	mostrarOverlay('publicidad', 'Eliminando gasto…');

	fetch('api/gastos_publicidad.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify({ eliminar: id })
	})
	.then(r => r.json())
	.then(resp => {
		if (!resp.ok) {
			alert('No se pudo eliminar');
			return;
		}
		cargarPublicidad();
	})
	.catch(() => alert('Error al eliminar'))
	.finally(() => ocultarOverlay('publicidad'));
}

/* =========================
   Registro SPA
   ========================= */
window.initViews = window.initViews || {};
window.initViews.publicidad = initPublicidad;
