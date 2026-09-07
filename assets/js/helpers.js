function mostrarOverlay(vista, texto = 'Cargando…') {
	const overlay = document.getElementById(`overlay-${vista}`);
	const label = document.getElementById(`overlay-text-${vista}`);
	if (!overlay) return;

	if (label) label.textContent = texto;
	overlay.classList.remove('d-none');
}

function ocultarOverlay(vista) {
	const overlay = document.getElementById(`overlay-${vista}`);
	if (overlay) overlay.classList.add('d-none');
}

function copiarCliente(texto) {
	if (navigator.clipboard && window.isSecureContext) {
		navigator.clipboard.writeText(texto)
			.then(() => mostrarToast('Datos copiados', 'info'))
			.catch(() => fallbackCopy(texto));
	} else {
		fallbackCopy(texto);
	}
}

function fallbackCopy(texto) {
	const textarea = document.createElement('textarea');
	textarea.value = texto;
	textarea.style.position = 'fixed';
	textarea.style.opacity = '0';

	document.body.appendChild(textarea);
	textarea.focus();
	textarea.select();

	try {
		document.execCommand('copy');
		mostrarToast('Datos copiados', 'info');
	} catch (e) {
		mostrarToast('No se pudo copiar', 'danger');
	}

	document.body.removeChild(textarea);
}

function formatoMoneda(valor) {
	const num = Number(valor);

	if (isNaN(num)) return '0.00';

	return num
		.toFixed(2)
		.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function formatearImporte(input) {

	let valor = parseFloat(input.value.replace(/,/g, ''));

	if (isNaN(valor)) {
		input.value = '';
		return;
	}

	input.value = valor.toFixed(2);
}
