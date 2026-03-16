let calendarioData = null;

/* ========================= */
function initCalendario() {
	cargarSelectoresCalendario();
	cargarCalendario();
}

/* ========================= */
function cargarSelectoresCalendario(){

	const hoy = new Date();
	const anioActual = hoy.getFullYear();
	const mesActual = hoy.getMonth() + 1;

	const selAnio = document.getElementById('calAnio');
	const selMes  = document.getElementById('calMes');

	/* =========================
	   AÑOS (2021 → actual)
	   ========================= */
	selAnio.innerHTML = '';

	for(let i = 2021; i <= anioActual; i++){
		selAnio.innerHTML += `<option value="${i}">${i}</option>`;
	}

	selAnio.value = anioActual;

	/* =========================
	   Cargar meses según año
	   ========================= */
	function cargarMeses(anioSeleccionado){

		selMes.innerHTML = '';

		const meses = [
			'Enero','Febrero','Marzo','Abril','Mayo','Junio',
			'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'
		];

		let limiteMes = 12;

		if(Number(anioSeleccionado) === anioActual){
			limiteMes = mesActual;
		}

		for(let i = 1; i <= limiteMes; i++){
			selMes.innerHTML += `
				<option value="${i}">
					${meses[i-1]}
			</option>`;
		}
	}

	/* =========================
	   Inicial
	   ========================= */
	cargarMeses(anioActual);
	selMes.value = mesActual;

	/* =========================
	   Cambio de año dinámico
	   ========================= */
	selAnio.addEventListener('change', function(){

		const anioSeleccionado = Number(this.value);

		cargarMeses(anioSeleccionado);

		// Si es año actual → seleccionar mes actual
		if(anioSeleccionado === anioActual){
			selMes.value = mesActual;
		}else{
			selMes.value = 1; // enero por defecto en años pasados
		}
	});
}

/* ========================= */
function cargarCalendario(){

	const anio = document.getElementById('calAnio').value;
	const mes  = document.getElementById('calMes').value;

	mostrarOverlay('calendario','Cargando calendario…');

	fetch(`api/calendario.php?anio=${anio}&mes=${mes}`)
	.then(r=>r.json())
	.then(data=>{
		calendarioData = data;
		renderCalendario(anio, mes);
	})
	.finally(()=> ocultarOverlay('calendario'));
}

/* ========================= */
function renderCalendario(anio, mes){

	const cont = document.getElementById('calendarioResultado');
	cont.innerHTML = '';

	const diasEnMes = new Date(anio, mes, 0).getDate();
	const primerDiaSemana = new Date(anio, mes - 1, 1).getDay();

	/* =========================
	   MAPS
	========================= */
	const ventasMap = {};
	const instMap = {};

	calendarioData.ventas.forEach(v=>{
		ventasMap[v.fecha] = v;
	});

	calendarioData.instalaciones.forEach(i=>{
		instMap[i.fecha] = i;
	});

	/* =========================
	   RESUMEN
	========================= */
	let totalMes = 0;
	let totalVentasMes = 0;
	let totalInstMes = 0;
	let diasConVenta = 0;

	calendarioData.ventas.forEach(v=>{
		totalMes += Number(v.ventas_total);
		totalVentasMes += Number(v.ventas_dia);

		if(Number(v.ventas_total) > 0){
			diasConVenta++;
		}
	});

	calendarioData.instalaciones.forEach(i=>{
		totalInstMes += Number(i.instaladas);
	});

	const porcentajeActivo = diasEnMes > 0
		? Math.round((diasConVenta / diasEnMes) * 100)
		: 0;

	/* =========================
	   DECLARAR HTML PRIMERO
	========================= */
	let html = '';

	/* =========================
	   CARD RESUMEN
	========================= */
	html += `
	<div class="card mb-3 shadow-sm">
		<div class="card-body calendar-resumen py-2 px-3 small d-flex flex-wrap gap-4">
			<div><strong>💰 Total mes:</strong> $${totalMes.toLocaleString()}</div>
			<div><strong>📝 Ventas:</strong> ${totalVentasMes}</div>
			<div><strong>🛠️ Instalaciones:</strong> ${totalInstMes}</div>
			<div><strong>📅 Días activos:</strong> ${diasConVenta} (${porcentajeActivo}%)</div>
		</div>
	</div>
	`;

	/* =========================
	   GRID
	========================= */
	html += `<div class="calendar-scroll"><div class="calendar-grid">`;

	html += `
		<div class="calendar-header">Domingo</div>
		<div class="calendar-header">Lunes</div>
		<div class="calendar-header">Martes</div>
		<div class="calendar-header">Miércoles</div>
		<div class="calendar-header">Jueves</div>
		<div class="calendar-header">Viernes</div>
		<div class="calendar-header">Sábado</div>
	`;

	/* =========================
	   ESPACIOS VACÍOS
	========================= */
	for(let i = 0; i < primerDiaSemana; i++){
		html += `<div class="calendar-day empty"></div>`;
	}

	/* =========================
	   DÍAS
	========================= */
	for(let d=1; d<=diasEnMes; d++){

		const fecha = `${anio}-${String(mes).padStart(2,'0')}-${String(d).padStart(2,'0')}`;

		const ventaDia = ventasMap[fecha];
		const instDia  = instMap[fecha];

		const total = ventaDia ? Number(ventaDia.ventas_total) : 0;
		const ventasDia = ventaDia ? ventaDia.ventas_dia : 0;
		const instaladas = instDia ? instDia.instaladas : 0;

		let clase = '';
		const maxVenta = Math.max(
			0,
			...calendarioData.ventas.map(v=>Number(v.ventas_total))
		);

		if(total > 0 && total === maxVenta) {
			clase = 'border-success bg-success bg-opacity-10';
		}

		if(total === 0 && instaladas === 0) {
			html += `
				<div class="calendar-day day-disabled">
					<div class="calendar-day-number">${d}</div>
				</div>
			`;
		} else {
			html += `
				<div class="calendar-day ${clase}">
					<div class="calendar-day-number">${d}</div>
					<div class="calendar-stats">
						<div class="stat-money">💰 ${total.toLocaleString()}</div>
						<div class="stat-compact">📝 ${ventasDia} · 🛠️ ${instaladas}</div>
					</div>
				</div>
			`;
		}
	}

	html += `</div></div>`;

	cont.innerHTML = html;
}

/* ========================= */
window.initViews = window.initViews || {};
window.initViews.calendario = initCalendario;