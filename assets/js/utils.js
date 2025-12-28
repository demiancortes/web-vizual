// assets/js/utils.js

/* =========================
   FECHAS COMUNES (SPA)
   ========================= */

function primerDiaMes(){
  const hoy = new Date();
  const y = hoy.getFullYear();
  const m = hoy.getMonth(); // 0-11
  return new Date(y, m, 1).toISOString().split('T')[0];
}

function hoyLocal(){
  const hoy = new Date();
  const y = hoy.getFullYear();
  const m = String(hoy.getMonth() + 1).padStart(2, '0');
  const d = String(hoy.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}

function animateValue(id, to, prefix = '', dec = 0){
  const el = document.getElementById(id);
  if(!el) return;

  let s = 0, steps = 25, step = to / steps, c = 0;

  const i = setInterval(() => {
    c++;
    s += step;
    if(c >= steps){
      s = to;
      clearInterval(i);
    }
    el.textContent =
      prefix +
      Number(s).toLocaleString('es-MX', {
        minimumFractionDigits: dec,
        maximumFractionDigits: dec
      });
  }, 15);
}

function formatoFecha(f){
  const d = new Date(f + 'T00:00:00');
  const meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
  return `${String(d.getDate()).padStart(2,'0')} ${meses[d.getMonth()]} ${d.getFullYear()}`;
}

function formatoTelefono(t){
  if(!t) return '';
  t = t.replace(/\D/g,'');
  return `${t.slice(0,3)} ${t.slice(3,6)} ${t.slice(6)}`;
}

function badgeUbicacion(u){
  if(!u) return `<span class="badge badge-na">N/A</span>`;
  u = u.toUpperCase();

  if(u === 'PUBLICIDAD') return `<span class="badge badge-publicidad">PUBLICIDAD</span>`;
  if(u === 'RECOMENDACIÓN' || u === 'RECOMENDACION')
    return `<span class="badge badge-recomendacion">RECOMENDACIÓN</span>`;
  if(u === 'MKT KAREN') return `<span class="badge badge-mkt">MKT KAREN</span>`;

  return `<span class="badge badge-na">${u}</span>`;
}
