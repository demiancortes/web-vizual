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
		// Método moderno (HTTPS)
		navigator.clipboard.writeText(texto)
			.then(() => feedbackCopiado())
			.catch(() => fallbackCopy(texto));
	} else {
		// Fallback para contextos no seguros
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
		feedbackCopiado();
	} catch (e) {
		alert('No se pudo copiar');
	}

	document.body.removeChild(textarea);
}

function feedbackCopiado() {
	const toast = document.createElement('div');
	toast.textContent = '✔️ Copiado';
	toast.style.position = 'fixed';
	toast.style.bottom = '20px';
	toast.style.left = '50%';
	toast.style.transform = 'translateX(-50%)';
	toast.style.background = '#198754';
	toast.style.color = '#fff';
	toast.style.padding = '6px 12px';
	toast.style.borderRadius = '6px';
	toast.style.fontSize = '14px';
	toast.style.zIndex = '9999';

	document.body.appendChild(toast);

	setTimeout(() => toast.remove(), 1200);
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
