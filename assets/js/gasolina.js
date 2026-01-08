/* =========================
   Estado
   ========================= */
let gasolinaData = [];
let modalGasolina = null;
let guardandoGasolina = false;

/* =========================
   Init vista
   ========================= */
function initGasolina() {
	setMesActualGasolina();
	cargarGasolina();
}

/* =========================
   Fechas
   ========================= */
function setMesActualGasolina() {
	const hoy = new Date();
	const inicio = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
	const fin = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);

	document.getElementById('gasolinaDesde').value =
		inicio.toISOString().slice(0,10);
	document.getElementById('gasolinaHasta').value =
		fin.toISOString().slice(0,10);
}

/* =========================
   Fetch
   ========================= */
function cargarGasolina() {
	const desde = document.getElementById('gasolinaDesde').value;
	const hasta = document.getElementById('gasolinaHasta').value;

	mostrarOverlay('gasolina', 'Cargando gasolina…');

	fetch(`api/gastos_gasolina.php?desde=${desde}&hasta=${hasta}`)
		.then(r => r.json())
		.then(data => {
			gasolinaData = data;
			renderGasolina();
		})
		.catch(err => {
			console.error(err);
			document.getElementById('gasolinaResultado').innerHTML = `
				<div class="alert alert-secondary">
					Error al cargar gasolina
				</div>`;
		})
		.finally(() => ocultarOverlay('gasolina'));
}

/* =========================
   Render
   ========================= */
function renderGasolina() {
	const cont = document.getElementById('gasolinaResultado');

	if (!gasolinaData.length) {
		cont.innerHTML = `
			<div class="alert alert-secondary">
				No hay gastos en este rango
			</div>`;
		actualizarTotalGasolina(0);
		return;
	}

	let total = gasolinaData.reduce(
		(acc, g) => acc + Number(g.monto || 0), 0
	);

	actualizarTotalGasolina(total);

	let html = `
	<div class="card shadow-sm">
		<div class="card-body p-0">
			<div class="table-scroll">
				<table class="table table-sm mb-0 table-striped table-scroll-gasolina">
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

	gasolinaData.forEach(g => {
		html += `
			<tr>
				<td>${formatoFecha(g.fecha)}</td>
				<td class="text-end">
					$${Number(g.monto).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
				</td>
				<td class="text-wrap">${g.nota || '—'}</td>
				<td class="text-center">
					<button
						class="btn btn-sm btn-outline-danger"
						title="Eliminar este gasto"
						onclick="eliminarGasolina(${g.id})">
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
function actualizarTotalGasolina(monto) {
	const cont = document.getElementById('badgeTotalGasolina');
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
   MODAL - Agregar Gasolina
   ========================= */
function abrirModalGasolina() {

	cerrarModalesAbiertos(); // 👈 CLAVE
	if (!modalGasolina) {
		modalGasolina = new bootstrap.Modal(
			document.getElementById('modalGasolina')
		);
	}

	const hoy = new Date().toLocaleDateString('en-CA');
	document.getElementById('gasFecha').value = hoy;
	document.getElementById('gasMonto').value = '';
	document.getElementById('gasNota').value = '';

	modalGasolina.show();
}

function guardarGasolina() {

	if (guardandoGasolina) return;

	const fecha = document.getElementById('gasFecha').value;
	const monto = document.getElementById('gasMonto').value;
	const nota  = document.getElementById('gasNota').value;

	if (!fecha || !monto) {
		alert('Fecha y monto son obligatorios');
		return;
	}

	guardandoGasolina = true;

	const btn = document.querySelector('#modalGasolina .btn-success');
	if (btn) btn.disabled = true;

	mostrarOverlay('gasolina', 'Guardando gasto…');

	fetch('api/gastos_gasolina.php', {
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
		modalGasolina.hide();
		cargarGasolina();
	})
	.catch(() => alert('Error al guardar'))
	.finally(() => {
		guardandoGasolina = false;
		if (btn) btn.disabled = false;
		ocultarOverlay('gasolina');
	});
}

/* =========================
   Eliminar gasto
   ========================= */
function eliminarGasolina(id) {
	if (!confirm('¿Eliminar este gasto?')) return;

	mostrarOverlay('gasolina', 'Eliminando gasto…');

	fetch('api/gastos_gasolina.php', {
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
		cargarGasolina();
	})
	.catch(() => alert('Error al eliminar'))
	.finally(() => ocultarOverlay('gasolina'));
}

/* =========================
   Registro SPA
   ========================= */
window.initViews = window.initViews || {};
window.initViews.gasolina = initGasolina;
