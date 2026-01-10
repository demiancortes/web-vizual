function initVentas(){
	const desde = document.getElementById('ventasDesde');
	const hasta = document.getElementById('ventasHasta');

	if(!desde || !hasta){
		console.warn('Inputs de fecha no encontrados');
		return;
	}

	desde.value = primerDiaMes();
	hasta.value = hoyLocal();

	cargarVentas();

	document.getElementById('ventasBuscar').onclick = cargarVentas;

	document.getElementById('ventasReset').onclick = () => {
		desde.value = primerDiaMes();
		hasta.value = hoyLocal();
		cargarVentas();
		document.getElementById('ventasBuscador').value = '';
	};
}

function aplicarFiltroTextoVentas(texto){
	const cards = document.querySelectorAll('#ventasResultado .card');

	cards.forEach(card=>{
		const contenido = card.innerText.toLowerCase();
		card.style.display = contenido.includes(texto) ? '' : 'none';
	});

	recalcularBadgesVentas();
	
}

function recalcularBadgesVentas(){
	let ventas = 0;
	let persianas = 0;
	let total = 0;
	let ganancia = 0;

	let totalPub = 0;
	let totalRec = 0;
	let totalMkt = 0;

	document.querySelectorAll('#ventasResultado .card').forEach(card=>{
		if(card.style.display === 'none') return;

		ventas++;

		const badge = card.querySelector('.badge');
		let origen = 'na';

		if (badge) {
			if (badge.classList.contains('badge-publicidad')) origen = 'publicidad';
			else if (badge.classList.contains('badge-recomendacion')) origen = 'recomendacion';
			else if (badge.classList.contains('badge-mkt')) origen = 'mkt';
		}

		card.querySelectorAll('tbody tr').forEach(tr=>{
			persianas++;

			const t = tr.querySelector('td:nth-child(4)');
			const g = tr.querySelector('td:nth-child(6)');
			const totalFila = t ? parseFloat(t.innerText.replace(/[^0-9.]/g,'')) : 0;

			if(t) total += totalFila;
			if(g) ganancia += parseFloat(g.innerText.replace(/[^0-9.]/g,''));

			if(origen === 'publicidad') totalPub += totalFila;
			else if(origen === 'recomendacion') totalRec += totalFila;
			else if(origen === 'mkt') totalMkt += totalFila;
		});
	});

	// badges generales
	bVentas.textContent = ventas;
	bPersianas.textContent = persianas;
	bTotal.textContent = total.toLocaleString();
	bGanancia.textContent = Math.round(ganancia).toLocaleString();

	// porcentajes
	const pct = v => total > 0 ? Math.round((v / total) * 100) : 0;

	bPubTotal.textContent = totalPub.toLocaleString();
	bRecTotal.textContent = totalRec.toLocaleString();
	bMktTotal.textContent = totalMkt.toLocaleString();

	bPubPct.textContent = pct(totalPub) + '%';
	bRecPct.textContent = pct(totalRec) + '%';
	bMktPct.textContent = pct(totalMkt) + '%';
}

function initBuscadorVentas(){
	const input = document.getElementById('ventasBuscador');
	if(!input) return;

	input.addEventListener('keydown', e=>{
		if(e.key === 'Enter'){
			e.preventDefault();
			aplicarFiltroTextoVentas(input.value.trim().toLowerCase());
		}
	});
}



/* =========================
   CARGAR DATA
   ========================= */
function cargarVentas(){
	mostrarOverlay('ventas', 'Cargando ventas…');
	document.getElementById('ventasBuscador').value = '';
	const desde = ventasDesde.value;
	const hasta = ventasHasta.value;

	fetch(`api/ventas.php?desde=${desde}&hasta=${hasta}`)
	.then(r => r.json())
	.then(data => {
		renderVentas(data);
		recalcularBadgesVentas();
		initBuscadorVentas();

	}).finally(() => {
			ocultarOverlay('ventas');
		});
}

/* =========================
   RENDER
   ========================= */
function renderVentas(data){
	const cont = document.getElementById('ventasResultado');

	if(!data.length){
		cont.innerHTML = `
			<div class="alert alert-secondary">
				No hay ventas en este rango
		</div>`;
		return;
	}

	initBuscadorVentas();

	/* =========================
	   1️⃣ Totales por cliente
	   ========================= */
	const totales = {};
	data.forEach(v=>{
		if(!totales[v.nombre]) totales[v.nombre] = 0;
		totales[v.nombre] += parseFloat(v.total);
	});

	let html = '';
	let clienteActual = null;

	data.forEach((v, i) => {

		/* =========================
		   Cambio de cliente
		   ========================= */
		if(clienteActual !== v.nombre){

			// cerrar cliente anterior
			if(clienteActual !== null){
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
					<div class="d-flex gap-2">
						${badgeUbicacion(v.ubicacion)}
						<span class="badge bg-success">
							$${totales[v.nombre].toLocaleString()}
						</span>
					</div>
				</div>

				<div class="card-body p-0">
					<div class="table-scroll table-scroll-ventas">
						<table class="table table-sm mb-0 table-fixed">
							<thead class="table-light">
								<tr>
									<th>Fecha</th>
									<th>Modelo</th>
									<th>Medida</th>
									<th class="text-end">Total</th>
									<th class="text-end">Costo</th>
									<th class="text-end">Ganancia</th>
								</tr>
							</thead>
				<tbody>`;
			}

		/* =========================
		   Fila
		   ========================= */
			html += `
			<tr>
				<td>${formatoFecha(v.fecha_cotizacion)}</td>
				<td>${v.modelo}</td>
				<td>${v.medida_real || `${v.largo} x ${v.alto}`}</td>
				<td class="text-end">$${Number(v.total).toLocaleString(undefined,{minimumFractionDigits:2})}</td>
				<td class="text-end">$${Number(v.costo).toLocaleString(undefined,{minimumFractionDigits:2})}</td>
				<td class="text-end fw-bold text-success">
					$${Number(v.ganancia).toLocaleString(undefined,{minimumFractionDigits:2})}
				</td>
			</tr>`;
		});

	/* =========================
	   Cierre FINAL (una sola vez)
	   ========================= */
	html += `
				</tbody>
			</table>
		</div>
	</div>
	</div>`;

	cont.innerHTML = html;
}



window.initViews = window.initViews || {};
window.initViews.ventas = initVentas;