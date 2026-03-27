/* =========================
   Init
   ========================= */
function initReporteAnual() {
	cargarSelectAnios();
	cargarReporteAnual(new Date().getFullYear());
}

/* =========================
   Select años
   ========================= */
function cargarSelectAnios() {
	const select = document.getElementById('reporteAnio');
	if (!select) return;

	const anioActual = new Date().getFullYear();
	const anioInicio = 2021;

	select.innerHTML = '';

	for (let y = anioActual; y >= anioInicio; y--) {
		const opt = document.createElement('option');
		opt.value = y;
		opt.textContent = y;

		if (y === anioActual) {
			opt.selected = true;
		}

		select.appendChild(opt);
	}
}


/* =========================
   Fetch
   ========================= */
function cargarReporteAnual(anio) {

	mostrarOverlay('reporte-anual', 'Cargando reporte…');

	fetch(`api/reporte_anual.php?anio=${anio}`)
	.then(r => r.json())
	.then(data => renderReporteAnual(data, anio))
	.catch(() => {
		document.getElementById('reporteAnualResultado').innerHTML = `
				<div class="alert alert-secondary">
					Error al cargar el reporte
		</div>`;
	})
	.finally(() => ocultarOverlay('reporte-anual'));
}

/* =========================
   Render
   ========================= */
function renderReporteAnual(data, anioSeleccionado) {

	const cont = document.getElementById('reporteAnualResultado');
	if (!data || !data.length) {
		cont.innerHTML = `
			<div class="alert alert-secondary">
				Sin información
		</div>`;
		return;
	}

	let html = `
	<div class="card shadow-sm">
		<div class="card-body p-0">
			<div class="table-scroll">
				<table class="table table-sm table-hover align-middle mb-0 table-fixed">
					<thead class="table-dark">
						<tr>
							<th>Mes</th>
							<th class="text-end">Ventas</th>
							<th class="text-end">Costo</th>
							<th class="text-end">Ganancia</th>
							<th class="text-end">Gasolina</th>
							<th class="text-end">Publicidad</th>
							<th class="text-end">Ganancia neta</th>
						</tr>
					</thead>
					<tbody>
	`;

	data.forEach((r, index) => {

		const isTotal = r.mes === 'TOTAL';
		const anioActual = new Date().getFullYear();
		const mesActual = new Date().getMonth() + 1;

		html += `
		<tr class="${isTotal ? 'table-secondary fw-bold' : ''}">
			<td>${r.mes}</td>
			<td class="text-end">$${fmt(r.ventas)}</td>
			<td class="text-end">$${fmt(r.costo)}</td>
			<td class="text-end text-success">$${fmt(r.ganancia)}</td>
			<td class="text-end text-danger">$${fmt(r.gasolina)}</td>
			<td class="text-end text-danger">$${fmt(r.publicidad)}</td>
			<td class="text-end fw-bold ${r.neta >= 0 ? 'text-success' : 'text-danger'}">
				$${fmt(r.neta)}
			</td>
		</tr>
		`;

		if (!isTotal && r.desglose) {

			const numeroMes = index + 1;

			const mostrar =
			(anioSeleccionado < anioActual) ||
			(anioSeleccionado == anioActual && numeroMes <= mesActual);

			if (mostrar) {

				const canales = ['publicidad','recomendacion','otros'];

				canales.forEach(canal => {

					const d = r.desglose[canal];
					if (!d) return;

					const ventasCanal = Number(d.ventas || 0);

					if (ventasCanal > 0) {

						const porcentaje = r.ventas > 0
						? Math.round((ventasCanal / r.ventas) * 100)
						: 0;

						html += `
						<tr class="table-light small reporte-subfila">
							<td class="ps-4">
								↳ ${canal.charAt(0).toUpperCase() + canal.slice(1)}
								<span class="text-muted">(${porcentaje}%)</span>
							</td>
							<td class="text-end">$${fmt(d.ventas)}</td>
							<td class="text-end">$${fmt(d.costo)}</td>
							<td class="text-end text-success">$${fmt(d.ganancia)}</td>
							<td colspan="3"></td>
						</tr>
						`;
					}
				});
			}
		}
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
   Helpers
   ========================= */
function fmt(n) {
	return Number(n || 0).toLocaleString();
}

/* =========================
   Registro SPA
   ========================= */
window.initViews = window.initViews || {};
window.initViews.reporte_anual = initReporteAnual;
