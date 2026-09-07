let enlacesData = [];
let modalEnlace = null;
let guardandoEnlace = false;

function initEnlaces() {
	cargarEnlaces();
}

function cargarEnlaces() {
	mostrarOverlay('enlaces', 'Cargando enlaces…');

	fetch('api/enlaces.php')
		.then(r => {
			if (!r.ok) throw new Error('Error HTTP');
			return r.json();
		})
		.then(data => {
			if (!Array.isArray(data)) throw new Error('Respuesta inválida');
			enlacesData = data;
			renderEnlaces();
		})
		.catch(err => {
			console.error(err);
			document.getElementById('enlaceResultado').innerHTML = '<div class="alert alert-secondary">Error al cargar los enlaces</div>';
		})
		.finally(() => ocultarOverlay('enlaces'));
}

function escaparHtml(valor) {
	return String(valor ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function obtenerTipoDestino(destino) {
	const url = String(destino || '').toLowerCase();
	if (url.includes('wa.me/') || url.includes('api.whatsapp.com/') || url.includes('whatsapp.com/')) return 'WhatsApp';
	if (url.includes('persianasvizual.com/')) return 'Página';
	return 'Enlace';
}

function formatearUltimoClick(fecha) {
	if (!fecha) return '—';

	const valor = String(fecha).replace(' ', 'T');
	const d = new Date(valor);

	if (Number.isNaN(d.getTime())) return escaparHtml(fecha);

	const meses = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
	const dia = String(d.getDate()).padStart(2, '0');
	const mes = meses[d.getMonth()];
	const anio = String(d.getFullYear()).slice(-2);
	const hora = String(d.getHours()).padStart(2, '0');
	const minutos = String(d.getMinutes()).padStart(2, '0');

	return `${dia} ${mes} ${anio} ${hora}:${minutos}`;
}

function obtenerUrlSlug(slug) {
	return 'https://persianasvizual.com/go/' + encodeURIComponent(slug);
}

function renderEnlaces(lista = enlacesData) {
	const cont = document.getElementById('enlaceResultado');
	if (!cont) return;

	if (!lista.length) {
		cont.innerHTML = '<div class="alert alert-secondary">No hay enlaces registrados</div>';
		return;
	}

	let html = `
	<div class="card shadow-sm">
		<div class="card-body p-0">
			<div class="table-scroll">
				<table class="table table-sm mb-0 table-striped table-enlaces">
					<thead class="table-dark">
						<tr>
							<th style="width:170px">Vista previa</th>
							<th style="min-width:220px">Información</th>
							<th>Slug</th>
							<th>Tipo</th>
							<th class="text-center">Clicks</th>
							<th>Último click</th>
							<th>Estado</th>
							<th class="text-center">Acciones</th>
						</tr>
					</thead>
					<tbody>`;

	lista.forEach(e => {
		const imagen = e.imagen ? 'https://persianasvizual.com/go/img/' + encodeURIComponent(e.imagen) : '';
		const tipo = obtenerTipoDestino(e.destino);
		const urlSlug = obtenerUrlSlug(e.slug);
		const inactivo = Number(e.activo) !== 1;

		const preview = imagen
			? `<img src="${escaparHtml(imagen)}" alt="${escaparHtml(e.titulo)}" class="enlace-preview" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"><div class="enlace-preview-vacio" style="display:none">Sin imagen</div>`
			: '<div class="enlace-preview-vacio">Sin imagen</div>';

		html += `
			<tr class="${inactivo ? 'table-danger' : ''}">
				<td class="align-middle"><div class="enlace-preview-wrap">${preview}</div></td>
				<td class="align-middle">
					<div class="fw-semibold">${escaparHtml(e.titulo)}</div>
					<div class="small text-muted">${escaparHtml(e.descripcion || '—')}</div>
				</td>
				<td class="align-middle"><code>${escaparHtml(e.slug)}</code></td>
				<td class="align-middle"><span class="badge bg-secondary">${tipo}</span></td>
				<td class="text-center align-middle fw-semibold">${Number(e.clicks || 0).toLocaleString('es-MX')}</td>
				<td class="align-middle">${formatearUltimoClick(e.ultimo_click)}</td>
				<td class="align-middle">${Number(e.activo) === 1 ? '<span class="badge bg-success">ACTIVO</span>' : '<span class="badge bg-danger">INACTIVO</span>'}</td>
				<td class="text-center align-middle">
					<div class="d-flex gap-1 justify-content-center">
						<button class="btn btn-sm btn-outline-primary" title="Editar" onclick="editarEnlace(${Number(e.id)})">✏️</button>
						<button class="btn btn-sm btn-outline-secondary" title="Copiar enlace" onclick="copiarEnlace('${escaparHtml(urlSlug)}')">🔗</button>
						<button class="btn btn-sm ${Number(e.activo) === 1 ? 'btn-outline-warning' : 'btn-outline-success'}" title="${Number(e.activo) === 1 ? 'Desactivar' : 'Activar'}" onclick="cambiarEstadoEnlace(${Number(e.id)})">${Number(e.activo) === 1 ? '⏸️' : '▶️'}</button>
					</div>
				</td>
			</tr>`;
	});

	html += '</tbody></table></div></div></div>';
	cont.innerHTML = html;
}

function filtrarEnlaces() {
	const termino = document.getElementById('buscadorEnlace').value.trim().toLowerCase();

	if (!termino) {
		renderEnlaces();
		return;
	}

	const filtrados = enlacesData.filter(e => {
		const texto = [
			e.slug,
			e.titulo,
			e.descripcion,
			obtenerTipoDestino(e.destino)
		].join(' ').toLowerCase();

		return texto.includes(termino);
	});

	renderEnlaces(filtrados);
}

function abrirModalEnlace(id = 0) {
	cerrarModalesAbiertos();

	if (!modalEnlace) modalEnlace = new bootstrap.Modal(document.getElementById('modalEnlace'));

	limpiarFormularioEnlace();

	document.getElementById('modalEnlaceTitulo').textContent = id ? '✏️ Editar enlace' : '➕ Agregar enlace';

	if (!id) {
		modalEnlace.show();
		return;
	}

	mostrarOverlay('enlaces', 'Cargando enlace…');

	fetch('api/enlaces.php?id=' + encodeURIComponent(id))
		.then(r => {
			if (!r.ok) throw new Error('No se pudo obtener el enlace');
			return r.json();
		})
		.then(enlace => {
			if (!enlace || enlace.ok === false) throw new Error(enlace.mensaje || 'Enlace no encontrado');

			document.getElementById('enlaceId').value = enlace.id || '';
			document.getElementById('enlaceSlug').value = enlace.slug || '';
			document.getElementById('enlaceTitulo').value = enlace.titulo || '';
			document.getElementById('enlaceDescripcion').value = enlace.descripcion || '';
			document.getElementById('enlaceDestino').value = enlace.destino || '';
			document.getElementById('enlaceActivo').checked = Number(enlace.activo) === 1;
			document.getElementById('enlaceImagenActual').value = enlace.imagen || '';

			const preview = document.getElementById('enlaceImagenPreview');
			const vacio = document.getElementById('enlaceImagenSinPreview');

			if (enlace.imagen) {
				preview.src = 'https://persianasvizual.com/go/img/' + encodeURIComponent(enlace.imagen);
				preview.style.display = 'block';
				vacio.style.display = 'none';
			}

			modalEnlace.show();
		})
		.catch(err => {
			console.error(err);
			alert(err.message || 'Error al cargar el enlace');
		})
		.finally(() => ocultarOverlay('enlaces'));
}

function limpiarFormularioEnlace() {
	document.getElementById('enlaceId').value = '';
	document.getElementById('enlaceSlug').value = '';
	document.getElementById('enlaceTitulo').value = '';
	document.getElementById('enlaceDescripcion').value = '';
	document.getElementById('enlaceDestino').value = '';
	document.getElementById('enlaceActivo').checked = true;
	document.getElementById('enlaceImagenArchivo').value = '';
	document.getElementById('enlaceImagenActual').value = '';

	const preview = document.getElementById('enlaceImagenPreview');
	const vacio = document.getElementById('enlaceImagenSinPreview');

	preview.removeAttribute('src');
	preview.style.display = 'none';
	vacio.style.display = 'block';
}

function editarEnlace(id) {
	abrirModalEnlace(id);
}

function actualizarPreviewEnlace() {
	const archivo = document.getElementById('enlaceImagenArchivo');
	const preview = document.getElementById('enlaceImagenPreview');
	const vacio = document.getElementById('enlaceImagenSinPreview');

	if (!archivo.files || !archivo.files[0]) {
		const actual = document.getElementById('enlaceImagenActual').value;

		if (actual) {
			preview.src = 'https://persianasvizual.com/go/img/' + encodeURIComponent(actual);
			preview.style.display = 'block';
			vacio.style.display = 'none';
		} else {
			preview.style.display = 'none';
			vacio.style.display = 'block';
		}

		return;
	}

	const file = archivo.files[0];

	if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
		alert('La imagen debe ser JPG, PNG o WebP');
		archivo.value = '';
		return;
	}

	if (file.size > 5 * 1024 * 1024) {
		alert('La imagen no puede superar 5 MB');
		archivo.value = '';
		return;
	}

	const reader = new FileReader();

	reader.onload = e => {
		preview.src = e.target.result;
		preview.style.display = 'block';
		vacio.style.display = 'none';
	};

	reader.readAsDataURL(file);
}

function guardarEnlace() {
	if (guardandoEnlace) return;

	const id = Number(document.getElementById('enlaceId').value || 0);
	const slug = document.getElementById('enlaceSlug').value.trim();
	const titulo = document.getElementById('enlaceTitulo').value.trim();
	const descripcion = document.getElementById('enlaceDescripcion').value.trim();
	const destino = document.getElementById('enlaceDestino').value.trim();
	const activo = document.getElementById('enlaceActivo').checked ? 1 : 0;
	const archivo = document.getElementById('enlaceImagenArchivo').files[0];

	if (!slug || !titulo || !destino) {
		alert('Slug, título y destino son obligatorios');
		return;
	}

	if (!/^[a-zA-Z0-9_-]+$/.test(slug)) {
		alert('El slug solo puede contener letras, números, guiones y guion bajo');
		return;
	}

	if (!/^https?:\/\//i.test(destino)) {
		alert('El destino debe comenzar con http:// o https://');
		return;
	}

	if (archivo) {
		if (!['image/jpeg', 'image/png', 'image/webp'].includes(archivo.type)) {
			alert('La imagen debe ser JPG, PNG o WebP');
			return;
		}

		if (archivo.size > 5 * 1024 * 1024) {
			alert('La imagen no puede superar 5 MB');
			return;
		}
	}

	guardandoEnlace = true;

	const btn = document.querySelector('#modalEnlace .btn-success');
	if (btn) btn.disabled = true;

	mostrarOverlay('enlaces', 'Guardando enlace…');

	const formData = new FormData();

	formData.append('accion', 'guardar');
	formData.append('id', id);
	formData.append('slug', slug);
	formData.append('titulo', titulo);
	formData.append('descripcion', descripcion);
	formData.append('destino', destino);
	formData.append('activo', activo);
	formData.append('imagenActual', document.getElementById('enlaceImagenActual').value || '');

	if (archivo) formData.append('imagenArchivo', archivo);

	fetch('api/enlaces.php', {
		method: 'POST',
		body: formData
	})
	.then(r => r.json())
	.then(resp => {
		if (!resp.ok) {
			alert(resp.mensaje || 'No se pudo guardar el enlace');
			return;
		}

		modalEnlace.hide();
		cargarEnlaces();
	})
	.catch(err => {
		console.error(err);
		alert('Error al guardar el enlace');
	})
	.finally(() => {
		guardandoEnlace = false;
		if (btn) btn.disabled = false;
		ocultarOverlay('enlaces');
	});
}

function cambiarEstadoEnlace(id) {
	const enlace = enlacesData.find(e => Number(e.id) === Number(id));
	if (!enlace) return;

	const accion = Number(enlace.activo) === 1 ? 'desactivar' : 'activar';

	if (!confirm(`¿${accion.charAt(0).toUpperCase() + accion.slice(1)} este enlace?`)) return;

	mostrarOverlay('enlaces', 'Actualizando estado…');

	const formData = new FormData();
	formData.append('accion', 'toggle');
	formData.append('id', id);

	fetch('api/enlaces.php', {
		method: 'POST',
		body: formData
	})
	.then(r => r.json())
	.then(resp => {
		if (!resp.ok) {
			alert(resp.mensaje || 'No se pudo actualizar el estado');
			return;
		}
		cargarEnlaces();
	})
	.catch(err => {
		console.error(err);
		alert('Error al actualizar el estado');
	})
	.finally(() => ocultarOverlay('enlaces'));
}

function copiarEnlace(url) {
	if (navigator.clipboard && window.isSecureContext) {
		navigator.clipboard.writeText(url).then(() => mostrarToast('Enlace copiado', 'info')).catch(() => copiarEnlaceFallback(url));
		return;
	}
	copiarEnlaceFallback(url);
}

function copiarEnlaceFallback(url) {
	const input = document.createElement('input');
	input.value = url;
	document.body.appendChild(input);
	input.select();
	document.execCommand('copy');
	input.remove();
	mostrarToast('Enlace copiado', 'info');
}

window.initViews = window.initViews || {};
window.initViews.enlaces = initEnlaces;
