/* ======================================================
   MÓDULO: REGISTRAR VENTA
   ====================================================== */

/* ------------------------------------------------------
   VARIABLES GLOBALES
   ------------------------------------------------------ */
let modalVenta = null;
let origenesCargados = false;
let modelosCargados = false;
let ventaListenersCargados = false;

let permitirCerrarVenta = false;

/* Array principal de persianas (detalle de la venta) */
let persianas = [];

/* Cache de modelos (desde API) */
let modelos = [];

/* 🔍 Copia base para buscador (NO se modifica) */
let modelosBase = [];


/* ======================================================
   ABRIR MODAL
   ====================================================== */
function abrirModalVenta() {

	if (!modalVenta) {
		modalVenta = new bootstrap.Modal(
			document.getElementById('modalVenta')
			);
	}

	venFecha.value = new Date().toLocaleDateString('en-CA');

	if (!origenesCargados) cargarOrigenesVenta();
	if (!modelosCargados) cargarModelos();

	if (!ventaListenersCargados) {
		inicializarVentaListeners();
		ventaListenersCargados = true;
	}

	cargarPersianasStorage();
	renderTablaPersianas();
	actualizarEstadoVenta();

	modalVenta.show();

	/* 🎯 Foco inicial */
	setTimeout(() => {
		venCliente.focus();
	}, 300);
}


/* ======================================================
   CARGA DE CATÁLOGOS
   ====================================================== */

function cargarOrigenesVenta() {

	fetch('api/venta.php?accion=origenes')
	.then(r => r.json())
	.then(data => {
		if (!Array.isArray(data)) return;

		venOrigen.innerHTML = '<option value="">Seleccionar</option>';

		data.forEach(origen => {
			const opt = document.createElement('option');
			opt.value = origen;
			opt.textContent = origen;
			venOrigen.appendChild(opt);
		});

		origenesCargados = true;
	});
}


function cargarModelos() {

	fetch('api/venta.php?accion=modelos')
	.then(r => r.json())
	.then(data => {
		if (!Array.isArray(data)) return;

		modelos = data;
		modelosBase = data;

		renderSelectModelos(modelosBase);
		modelosCargados = true;
	});
}


function renderSelectModelos(lista) {

	perModelo.innerHTML = '<option value="">Seleccionar modelo</option>';

	lista.forEach(m => {
		const opt = document.createElement('option');
		opt.value = m.modelo;
		opt.textContent = `${m.tipo} - ${m.modelo}`;
		opt.dataset.precio = m.precio;
		perModelo.appendChild(opt);
	});
}


/* ======================================================
   UTILIDADES MODELO
   ====================================================== */

function obtenerModeloSeleccionado() {

	if (perModelo.value) return perModelo.value;

	if (typeof perModeloBuscar !== 'undefined') {
		const texto = perModeloBuscar.value.trim();
		if (texto) return texto.toUpperCase();
	}

	return '';
}

function hayModelosEnSelect() {
	return perModelo.options.length > 1;
}

function mostrarAlerta(tipo, mensaje) {

	const alertBox = document.getElementById('alertVenta');
	if (!alertBox) return;

	// Limpiar clases previas
	alertBox.className = 'alert small mb-2';

	// Tipos: success | danger | warning | info
	alertBox.classList.add(`alert-${tipo}`);

	alertBox.innerHTML = mensaje;
	alertBox.classList.remove('d-none');

	// Auto ocultar después de 4s (opcional)
	setTimeout(() => {
		alertBox.classList.add('d-none');
	}, 4000);
}



/* ======================================================
   LISTENERS
   ====================================================== */
function inicializarVentaListeners() {

	const modal = document.getElementById('modalVenta');

	/* 🚪 Aviso al salir si hay persianas */
	modal.addEventListener('hide.bs.modal', function (e) {

		// Si ya está permitido cerrar, no bloquear
		if (permitirCerrarVenta) {
			permitirCerrarVenta = false;
			return;
		}

		// Si hay persianas, interceptar
		if (persianas.length > 0) {

			e.preventDefault();

			mostrarConfirmacion({
				titulo: 'Salir de la venta',
				contenido: `
				<div>Hay <b>${persianas.length}</b> persiana(s) capturada(s).</div>
				<div>Si sales ahora, se perderán.</div>
					`,
					textoConfirmar: 'Salir',
					claseConfirmar: 'btn-danger',
					onConfirmar: () => {
						permitirCerrarVenta = true;
				modalVenta.hide(); // ✅ ahora sí, fuera del flujo original
			}
		});
		}
	});

	/* Limpieza real */
	modal.addEventListener('hidden.bs.modal', resetFormularioVenta);

	/* Teléfono */
	venTelefono.addEventListener('input', function () {
		let v = this.value.replace(/\D/g, '').substring(0, 10);
		this.value =
		v.length > 6 ? `(${v.slice(0,3)}) ${v.slice(3,6)}-${v.slice(6)}` :
		v.length > 3 ? `(${v.slice(0,3)}) ${v.slice(3)}` :
		v.length > 0 ? `(${v}` : '';
	});

	venTotal.addEventListener('input', calcularPendiente);
	venAnticipo.addEventListener('input', calcularPendiente);

	perModelo.addEventListener('change', function () {
		const opt = this.options[this.selectedIndex];
		perPrecio.value = opt && opt.dataset.precio ? opt.dataset.precio : '';
		calcularPersiana();

		/* 🎯 Foco a ancho */
		setTimeout(() => {
			perAncho.focus();
		}, 100);
	});

	perAncho.addEventListener('input', calcularPersiana);
	perAlto.addEventListener('input', calcularPersiana);
	perPrecio.addEventListener('input', calcularPersiana);
	perTotal.addEventListener('input', calcularPersiana);

	if (typeof perModeloBuscar !== 'undefined') {
		perModeloBuscar.addEventListener('input', function () {

			const texto = this.value.toLowerCase().trim();

			if (!texto) {
				renderSelectModelos(modelosBase);
				return;
			}

			const filtrados = modelosBase.filter(m =>
				m.modelo.toLowerCase().includes(texto)
				);

			renderSelectModelos(filtrados);
		});
	}
}


/* ======================================================
   CÁLCULOS
   ====================================================== */

function calcularPendiente() {

	const total = parseFloat(venTotal.value) || 0;
	let anticipo = parseFloat(venAnticipo.value) || 0;

	if (anticipo > total) {
		anticipo = total;
		venAnticipo.value = total.toFixed(2);
	}

	venPendiente.value = (total - anticipo).toFixed(2);
}


function calcularPersiana() {

	const ancho = parseFloat(perAncho.value);
	const alto = parseFloat(perAlto.value);

	if (!isNaN(ancho) && !isNaN(alto)) {
		perMedidaReal.value = `${ancho.toFixed(2)} x ${alto.toFixed(2)}`;
	} else {
		perMedidaReal.value = '';
	}
}


/* ======================================================
   PERSIANAS
   ====================================================== */

function agregarPersiana() {

	const modeloFinal = obtenerModeloSeleccionado();

	if (hayModelosEnSelect() && !perModelo.value) {
		mostrarAlerta('warning', 'Selecciona un modelo de la lista');
		return;
	}

	if (!modeloFinal) return mostrarAlerta('warning', 'Escribe el nombre del modelo');
	if (!perCadena.value) return mostrarAlerta('warning', 'Selecciona la cadena');
	if (!perAncho.value || perAncho.value <= 0) return mostrarAlerta('warning', 'Ancho inválido');
	if (!perAlto.value || perAlto.value <= 0) return mostrarAlerta('warning', 'Alto inválido');
	if (!perPrecio.value || perPrecio.value <= 0) return mostrarAlerta('warning', 'Precio inválido');
	if (!perTotal.value || perTotal.value <= 0) return mostrarAlerta('warning', 'Total inválido');

	const ancho = parseFloat(perAncho.value);
	const alto = parseFloat(perAlto.value);
	const precio = parseFloat(perPrecio.value);
	const total = parseFloat(perTotal.value);

	const costo = (ancho < 1 ? 1 : ancho) * (alto < 1 ? 1 : alto) * precio;
	const ganancia = total - costo;

	persianas.push({
		modelo: modeloFinal,
		cadena: perCadena.value,
		ancho,
		alto,
		medida_real: `${ancho.toFixed(2)} x ${alto.toFixed(2)}`,
		precio,
		costo,
		total,
		ganancia
	});

	guardarPersianasStorage();
	renderTablaPersianas();
	actualizarTotalVenta();
	actualizarEstadoVenta();
	limpiarFormularioPersiana();
}


/* ======================================================
   TABLA
   ====================================================== */

function renderTablaPersianas() {

	const tbody = document.getElementById('tablaPersianas');
	tbody.innerHTML = '';

	persianas.forEach((p, i) => {
		tbody.innerHTML += `
		<tr>
			<td>${p.modelo}</td>
			<td>${p.cadena}</td>
			<td>${p.medida_real}</td>
			<td>$${p.precio.toFixed(2)}</td>
			<td>$${p.costo.toFixed(2)}</td>
			<td>$${p.total.toFixed(2)}</td>
			<td class="${p.ganancia < 0 ? 'text-danger fw-bold' : ''}">
				$${p.ganancia.toFixed(2)}
			</td>
			<td class="text-center">
				<button class="btn btn-sm btn-outline-secondary"
					onclick="cargarPersianaEnFormulario(${i})">📄</button>
				<button class="btn btn-sm btn-outline-danger"
					onclick="eliminarPersiana(${i})">❌</button>
			</td>
		</tr>`;
	});
}


function eliminarPersiana(i) {
	persianas.splice(i, 1);
	guardarPersianasStorage();
	renderTablaPersianas();
	actualizarTotalVenta();
	actualizarEstadoVenta();
}


function cargarPersianaEnFormulario(i) {

	const p = persianas[i];
	if (!p) return;

	perModelo.value = p.modelo;
	perModelo.dispatchEvent(new Event('change'));
	perCadena.value = p.cadena;
	perAncho.value = p.ancho;
	perAlto.value = p.alto;
	perMedidaReal.value = p.medida_real;
	perPrecio.value = p.precio;
	perTotal.value = p.total;

	perAncho.focus();
}


/* ======================================================
   TOTALES
   ====================================================== */

function actualizarTotalVenta() {

	const total = persianas.reduce((sum, p) => sum + p.total, 0);
	venTotal.value = total.toFixed(2);
	calcularPendiente();
}


/* ======================================================
   ESTADO VISUAL
   ====================================================== */

function actualizarEstadoVenta() {

	const box = document.getElementById('estadoVenta');
	if (!box) return;

	if (persianas.length > 0) {
		box.classList.remove('d-none');
		box.innerHTML =
	`ℹ️ Venta en progreso · ${persianas.length} persiana(s) · Total $${venTotal.value}`;
} else {
	box.classList.add('d-none');
}
}


/* ======================================================
   LOCALSTORAGE
   ====================================================== */

function guardarPersianasStorage() {
	localStorage.setItem('venta_persianas', JSON.stringify(persianas));
}

function cargarPersianasStorage() {
	const data = localStorage.getItem('venta_persianas');
	persianas = data ? JSON.parse(data) : [];
}


/* ======================================================
   LIMPIEZAS
   ====================================================== */

function limpiarFormularioPersiana() {

	perModelo.value = '';
	perCadena.value = 'IZQ';
	perAncho.value = '';
	perAlto.value = '';
	perMedidaReal.value = '';
	perPrecio.value = '';
	perTotal.value = '';

	if (typeof perModeloBuscar !== 'undefined') {
		perModeloBuscar.value = '';
		renderSelectModelos(modelosBase);
		perModeloBuscar.focus();
	}
}


function resetFormularioVenta() {

	venCliente.value = '';
	venTelefono.value = '';
	venDomicilio.value = '';
	venFraccionamiento.value = '';
	venOrigen.value = '';

	venTotal.value = '';
	venAnticipo.value = '';
	venPendiente.value = '';

	persianas = [];
	localStorage.removeItem('venta_persianas');
	renderTablaPersianas();
	actualizarEstadoVenta();
}

function validarVentaFinal() {

	if (!venCliente.value.trim()) {
		mostrarAlerta('warning', 'Captura el nombre del cliente');
		venCliente.focus();
		return false;
	}

	if (!venTelefono.value.trim()) {
		mostrarAlerta('warning', 'Captura el teléfono del cliente');
		venTelefono.focus();
		return false;
	}

	if (!venDomicilio.value.trim()) {
		mostrarAlerta('warning', 'Captura la dirección del cliente');
		venDomicilio.focus();
		return false;
	}

	if (!venFraccionamiento.value.trim()) {
		mostrarAlerta('warning', 'Captura el fraccionamiento del cliente');
		venFraccionamiento.focus();
		return false;
	}

	if (!venFecha.value) {
		mostrarAlerta('warning', 'Selecciona la fecha de la venta');
		venFecha.focus();
		return false;
	}

	if (!venOrigen.value) {
		mostrarAlerta('warning', 'Selecciona el origen de la venta');
		venOrigen.focus();
		return false;
	}

	if (persianas.length === 0) {
		mostrarAlerta('danger', 'Agrega al menos una persiana a la venta');
		return false;
	}

	const total = parseFloat(venTotal.value) || 0;
	const anticipo = parseFloat(venAnticipo.value) || 0;
	const pendiente = parseFloat(venPendiente.value) || 0;

	if (total <= 0) {
		mostrarAlerta('warning', 'El total de la venta no es válido');
		return false;
	}

	if (anticipo < 0 || anticipo > total) {
		mostrarAlerta('warning', 'El anticipo no es válido');
		venAnticipo.focus();
		return false;
	}

	if (pendiente < 0) {
		mostrarAlerta('warning', 'El pendiente no es válido');
		return false;
	}

	return true;
}

function mostrarConfirmacion({
	titulo = 'Confirmar',
	contenido = '',
	textoConfirmar = 'Confirmar',
	claseConfirmar = 'btn-success',
	onConfirmar = null,
	onCancelar = null
}) {
	const modalEl = document.getElementById('modalConfirmar');
	const modal = new bootstrap.Modal(modalEl, {
		backdrop: 'static',
		keyboard: false
	});

	document.getElementById('confirmarTitulo').textContent = titulo;
	document.getElementById('confirmarContenido').innerHTML = contenido;

	const btn = document.getElementById('btnConfirmarAccion');
	btn.textContent = textoConfirmar;
	btn.className = `btn btn-sm ${claseConfirmar}`;

	btn.onclick = function () {
		modal.hide();
		if (onConfirmar) onConfirmar();
	};

	// Cancelar → regresar al modal venta
	modalEl.querySelector('[data-bs-dismiss]')
		.onclick = function () {
			if (onCancelar) onCancelar();
		};

	modal.show();
}


/* ======================================================
   GUARDAR VENTA (pendiente)
   ====================================================== */

function guardarVenta() {

	if (!validarVentaFinal()) return;

	const contenido = `
		<div><b>Cliente:</b> ${venCliente.value}</div>
		<div><b>Persianas:</b> ${persianas.length}</div>
		<div><b>Total:</b> $${venTotal.value}</div>
		<div><b>Pendiente:</b> $${venPendiente.value}</div>
	`;

	mostrarConfirmacion({
		titulo: 'Confirmar venta',
		contenido,
		textoConfirmar: 'Guardar',
		claseConfirmar: 'btn-success',
		onConfirmar: () => {
			guardarVentaFinal();
		}
	});
}

function guardarVentaFinal() {

	const btn = document.getElementById('btnGuardarVenta');
	if (btn) {
		btn.disabled = true;
		btn.textContent = 'Guardando...';
	}

	const payload = {
		cliente: {
			nombre: venCliente.value.trim(),
			domicilio: venDomicilio.value.trim(),
			fraccionamiento: venFraccionamiento.value.trim(),
			telefono: venTelefono.value.replace(/\D/g, ''),
			total: parseFloat(venTotal.value),
			anticipo: parseFloat(venAnticipo.value),
			pendiente: parseFloat(venPendiente.value),
			ubicacion: venOrigen.value,
			fecha: venFecha.value
		},
		persianas: persianas
	};

	fetch('api/ventas_guardar.php', {
		method: 'POST',
		headers: { 'Content-Type': 'application/json' },
		body: JSON.stringify(payload)
	})
	.then(r => r.json())
	.then(resp => {

		if (!resp.ok) {
			mostrarAlerta('danger', resp.error || 'Error al guardar la venta');
			if (btn) {
				btn.disabled = false;
				btn.textContent = 'Guardar';
			}
			return;
		}

		// ✅ Éxito
		mostrarAlerta('success', 'Venta guardada correctamente');
		cargarResumenDashboard();
		// Limpieza total
		persianas = [];
		localStorage.removeItem('venta_persianas');
		permitirCerrarVenta = true;
		btn.disabled = false;
		btn.textContent = 'Guardar';
		modalVenta.hide();

	})
	.catch(err => {
		console.error(err);
		mostrarAlerta('danger', 'Error de conexión');
		if (btn) {
			btn.disabled = false;
			btn.textContent = 'Guardar';
		}
	});
}