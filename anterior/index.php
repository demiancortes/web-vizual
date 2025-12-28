<?php
require 'db.php';

/* Meses en español */
$meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];

$sql = "
SELECT
v.fecha_cotizacion,
c.id AS cliente_id,
c.nombre AS cliente,
c.telefono,
c.domicilio,
c.fraccionamiento,
c.ubicacion,
v.modelo,
v.medida_real,
v.largo,
v.alto,
v.total,
v.costo,
v.ganancia
FROM ventas v
JOIN clientes c ON v.cliente_id = c.id
ORDER BY v.fecha_cotizacion ASC, v.id ASC
";

function formatoTelefonoMX($tel){
  $tel = preg_replace('/\D/', '', $tel);
  if(strlen($tel) === 10){
    return substr($tel,0,3).' '.substr($tel,3,3).' '.substr($tel,6,4);
  }
  return $tel;
}

$rows = $pdo->query($sql)->fetchAll();
$clienteActual = null;

function badgeUbicacion($u){
  if(empty($u)) return ['N/A','badge-na'];
  $u = strtolower(trim($u));
  if($u === 'publicidad') return ['PUBLICIDAD','badge-publicidad'];
  if($u === 'recomendación' || $u === 'recomendacion') return ['RECOMENDACIÓN','badge-recomendacion'];
  if($u === 'mkt karen') return ['MKT KAREN','badge-mkt'];
  return [strtoupper($u),'badge-na'];
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Historial de ventas | Persianas Vizual</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
.table-fixed{table-layout:fixed;width:100%}
.table-fixed th,.table-fixed td{vertical-align:middle;word-wrap:break-word}
.card-stat{color:#fff}
.card-stat .value{font-size:1.7rem;font-weight:700}

.indicador{padding:8px 14px;border-radius:999px;font-weight:600;font-size:.85rem;display:flex;align-items:center;gap:6px;white-space:nowrap}
.indicador span{font-weight:700}

.ind-publicidad{background:#6f42c1;color:#fff}
.ind-recomendacion{background:#6c757d;color:#fff}
.ind-mkt{background:#0d6efd;color:#fff}
.ind-mejor-dia{background:#212529;color:#fff}
.ind-hoy{background:#f8f9fa;color:#212529;border:2px dashed #0d6efd}

.badge-publicidad{background:#6f42c1;color:#fff}
.badge-recomendacion{background:#6c757d;color:#fff}
.badge-mkt{background:#0d6efd;color:#fff}
.badge-na{background:#6c757d;color:#fff}

#btnArriba{position:fixed;bottom:30px;right:30px;display:none;z-index:999;width:48px;height:48px}
</style>
</head>

<body class="bg-light">
<div class="container py-4">

<!-- 🔝 TÍTULO + INDICADORES -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h4 class="mb-0">📋 Historial de ventas</h4>
  <div class="d-flex flex-wrap gap-2 justify-content-end">
    <div class="indicador ind-publicidad">Publicidad (<span id="cardPublicidad">0</span> | <span id="cardPublicidadTotal">$0</span>)</div>
    <div class="indicador ind-recomendacion">Recomendación (<span id="cardRecomendacion">0</span> | <span id="cardRecomendacionTotal">$0</span>)</div>
    <div class="indicador ind-mkt">MKT Karen (<span id="cardMktKaren">0</span> | <span id="cardMktKarenTotal">$0</span>)</div>
    <div class="indicador ind-mejor-dia"><span id="cardMejorDia">—</span> / <span id="cardMejorDiaTotal">$0</span></div>
    <div class="indicador ind-hoy">HOY <span id="cardHoyTotal">$0</span></div>
  </div>
</div>

<!-- 🔢 CARDS -->
<div class="row justify-content-center g-3 mb-4">
  <div class="col-md-2"><div class="card card-stat bg-primary text-center p-3"><div>Ventas</div><div id="cardVentas" class="value">0</div></div></div>
  <div class="col-md-2"><div class="card card-stat bg-info text-center p-3"><div>Persianas</div><div id="cardPersianas" class="value">0</div></div></div>
  <div class="col-md-3"><div class="card card-stat bg-warning text-center p-3"><div>Total ventas</div><div id="cardTotal" class="value">$0</div></div></div>
  <div class="col-md-3"><div class="card card-stat bg-success text-center p-3"><div>Ganancia</div><div id="cardGanancia" class="value">$0</div></div></div>
  <div class="col-md-2"><div class="card card-stat bg-danger text-center p-3"><div>Ticket promedio</div><div id="cardTicket" class="value">$0.00</div></div></div>
</div>

<!-- 📅 FILTROS -->
<div class="row g-2 mb-3">
  <div class="col-md-3"><label class="form-label">Desde</label><input type="date" id="fechaDesde" class="form-control"></div>
  <div class="col-md-3"><label class="form-label">Hasta</label><input type="date" id="fechaHasta" class="form-control"></div>
  <div class="col-md-6">
    <div class="d-flex gap-2 mt-4">
      <button id="btnActualizar" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Buscar</button>
      <button id="btnReset" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-repeat me-1"></i>Restablecer</button>
      <button id="btnReporteAnual" class="btn btn-dark w-100"><i class="bi bi-bar-chart-line me-1"></i>Reporte anual</button>
    </div>
  </div>
</div>

<!-- 🔍 BUSCADOR -->
<div class="mb-4">
  <input id="buscadorGlobal" type="text" class="form-control form-control-lg"
         placeholder="🔍 Buscar por cliente, fraccionamiento o modelo… (Enter)">
</div>

<?php foreach($rows as $i=>$r): ?>
<?php if($clienteActual!==$r['cliente_id']): ?>
<?php
if($clienteActual!==null) echo "</tbody></table></div></div>";
$clienteActual=$r['cliente_id'];
$totalBloque=0;
for($j=$i;$j<count($rows);$j++){
  if($rows[$j]['cliente_id']!==$clienteActual) break;
  $totalBloque+=$rows[$j]['total'];
}
list($txtUb,$classUb)=badgeUbicacion($r['ubicacion']);
?>
<div class="card mb-3 shadow-sm bloque-venta"
     data-cliente="<?= strtolower($r['cliente']) ?>"
     data-fraccionamiento="<?= strtolower($r['fraccionamiento']) ?>"
     data-ubicacion="<?= strtoupper(!empty($r['ubicacion'])?$r['ubicacion']:'N/A') ?>">
<div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
  <div>
    <div class="fw-bold"><?= htmlspecialchars($r['cliente']) ?> | 📞 (<?= formatoTelefonoMX($r['telefono']) ?>)</div>
    <small>📍 <?= htmlspecialchars($r['domicilio']) ?> <?= htmlspecialchars($r['fraccionamiento']) ?></small>
  </div>
  <div class="d-flex gap-2">
    <span class="badge <?= $classUb ?>"><?= $txtUb ?></span>
    <span class="badge bg-success">$<?= number_format($totalBloque,0) ?></span>
  </div>
</div>
<div class="card-body p-0">
<table class="table table-sm mb-0 table-fixed">
<thead class="table-light">
<tr><th>Fecha</th><th>Modelo</th><th>Medida</th><th class="text-end">Total</th><th class="text-end">Costo</th><th class="text-end">Ganancia</th></tr>
</thead>
<tbody>
<?php endif; ?>
<?php $ts=strtotime($r['fecha_cotizacion']); ?>
<tr data-modelo="<?= strtolower($r['modelo']) ?>"
    data-total="<?= $r['total'] ?>"
    data-ganancia="<?= $r['ganancia'] ?>"
    data-fecha="<?= date('Y-m-d',$ts) ?>">
<td><?= date('d',$ts).' '.$meses[(int)date('n',$ts)-1].' '.date('Y',$ts) ?></td>
<td><?= htmlspecialchars($r['modelo']) ?></td>
<td><?= $r['medida_real'] ?: $r['largo'].' x '.$r['alto'] ?></td>
<td class="text-end">$<?= number_format($r['total'],2) ?></td>
<td class="text-end">$<?= number_format($r['costo'],2) ?></td>
<td class="text-end fw-bold text-success">$<?= number_format($r['ganancia'],2) ?></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
</div>

<button id="btnArriba" class="btn btn-dark rounded-circle">↑</button>

<!-- 📊 MODAL REPORTE ANUAL -->
<div class="modal fade" id="modalReporteAnual" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-bar-chart-line me-1"></i>Reporte anual</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="contenidoReporteAnual">
        <div class="text-center text-muted py-5">
          <div class="spinner-border mb-3"></div>
          Procesando información…
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ===== FECHAS ===== */
function primerDiaMes(){
  const d=new Date();
  return new Date(d.getFullYear(),d.getMonth(),1).toISOString().split('T')[0];
}
function hoyLocal(){
  const d=new Date();
  return d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
}
fechaDesde.value=primerDiaMes();
fechaHasta.value=hoyLocal();

/* ===== ANIMACIÓN ===== */
function animate(el,to,prefix='',dec=0){
  let s=0,st=25,sp=to/st,c=0;
  let i=setInterval(()=>{
    c++;s+=sp;
    if(c>=st){s=to;clearInterval(i);}
    el.textContent=(prefix?prefix:'')+
      Number(s).toLocaleString(undefined,{minimumFractionDigits:dec,maximumFractionDigits:dec});
  },15);
}

/* ===== FILTROS ===== */
function aplicarFiltroFechas(){
  const d=fechaDesde.value,h=fechaHasta.value;
  document.querySelectorAll('.bloque-venta').forEach(b=>{
    let ok=false;
    b.querySelectorAll('tbody tr').forEach(tr=>{
      const f=tr.dataset.fecha;
      if(f>=d&&f<=h) ok=true;
    });
    b.style.display=ok?'':'none';
  });
}
function aplicarFiltroTexto(f){
  document.querySelectorAll('.bloque-venta').forEach(b=>{
    if(b.style.display==='none') return;
    let ok=b.dataset.cliente.includes(f)||b.dataset.fraccionamiento.includes(f);
    b.querySelectorAll('tbody tr').forEach(tr=>{
      if((tr.dataset.modelo||'').includes(f)) ok=true;
    });
    b.style.display=ok?'':'none';
  });
}

/* ===== RECÁLCULO ===== */
function recalcular(){
  let filas=[],ventas=0;
  let publicidad=0,recomendacion=0,mkt=0;
  let totalPublicidad=0,totalRecomendacion=0,totalMkt=0;
  let totalesDia={};
  const hoyStr=hoyLocal();
  let totalHoy=0;

  document.querySelectorAll('.bloque-venta').forEach(b=>{
    if(b.style.display!=='none'){
      ventas++;
      const u=b.dataset.ubicacion;

      if(u==='PUBLICIDAD') publicidad++;
      else if(u==='RECOMENDACIÓN'||u==='RECOMENDACION') recomendacion++;
      else if(u==='MKT KAREN') mkt++;

      b.querySelectorAll('tbody tr').forEach(tr=>{
        filas.push(tr);
        const f=tr.dataset.fecha;
        const t=parseFloat(tr.dataset.total||0);

        if(!totalesDia[f]) totalesDia[f]=0;
        totalesDia[f]+=t;

        if(u==='PUBLICIDAD') totalPublicidad+=t;
        else if(u==='RECOMENDACIÓN'||u==='RECOMENDACION') totalRecomendacion+=t;
        else if(u==='MKT KAREN') totalMkt+=t;

        if(f===hoyStr) totalHoy+=t;
      });
    }
  });

  let pers=filas.length,total=0,gan=0;
  filas.forEach(f=>{
    total+=parseFloat(f.dataset.total||0);
    gan+=parseFloat(f.dataset.ganancia||0);
  });

  animate(cardVentas,ventas);
  animate(cardPersianas,pers);
  animate(cardTotal,total,'$');
  animate(cardGanancia,gan,'$');
  animate(cardTicket,ventas>0?total/ventas:0,'$',2);

  animate(cardPublicidad,publicidad);
  animate(cardPublicidadTotal,totalPublicidad,'$');
  animate(cardRecomendacion,recomendacion);
  animate(cardRecomendacionTotal,totalRecomendacion,'$');
  animate(cardMktKaren,mkt);
  animate(cardMktKarenTotal,totalMkt,'$');

  let mejorDia='—',mejorTotal=0;
  Object.entries(totalesDia).forEach(([f,m])=>{
    if(m>mejorTotal){mejorTotal=m;mejorDia=f;}
  });

  if(mejorTotal>0){
    const d=new Date(mejorDia+'T00:00:00');
    cardMejorDia.textContent=`${d.getDate()} ${['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'][d.getMonth()]}`;
    animate(cardMejorDiaTotal,mejorTotal,'$');
  }else{
    cardMejorDia.textContent='—';
    cardMejorDiaTotal.textContent='$0';
  }

  animate(cardHoyTotal,totalHoy,'$');
}

/* ===== EVENTOS EXISTENTES ===== */
btnActualizar.onclick=()=>{aplicarFiltroFechas();recalcular();};
btnReset.onclick=()=>{
  fechaDesde.value=primerDiaMes();
  fechaHasta.value=hoyLocal();
  buscadorGlobal.value='';
  document.querySelectorAll('.bloque-venta').forEach(b=>b.style.display='');
  aplicarFiltroFechas();recalcular();
};

buscadorGlobal.addEventListener('keyup',e=>{
  if(e.key==='Enter') return;
  aplicarFiltroFechas();
  const f=buscadorGlobal.value.toLowerCase().trim();
  if(f) aplicarFiltroTexto(f);
  recalcular();
});
buscadorGlobal.addEventListener('keydown',e=>{
  if(e.key==='Enter'){
    e.preventDefault();
    aplicarFiltroFechas();
    aplicarFiltroTexto(buscadorGlobal.value.toLowerCase().trim());
    recalcular();
  }
});

/* Botón arriba */
window.addEventListener('scroll',()=>{btnArriba.style.display=window.scrollY>300?'block':'none';});
btnArriba.onclick=()=>window.scrollTo({top:0,behavior:'smooth'});

/* Inicial */
aplicarFiltroFechas();
recalcular();

/* ===== REPORTE ANUAL (NUEVO, NO INTERFIERE) ===== */
const modalReporteAnual = new bootstrap.Modal(
  document.getElementById('modalReporteAnual')
);

btnReporteAnual.addEventListener('click',()=>{
  modalReporteAnual.show();
  fetch('reporte_anual.php')
    .then(r=>r.text())
    .then(html=>contenidoReporteAnual.innerHTML=html)
    .catch(()=>contenidoReporteAnual.innerHTML='<div class="text-danger text-center">Error al cargar reporte</div>');
});

function cargarReporteAnual(anio){
  fetch('reporte_anual.php?anio=' + anio)
    .then(r => r.text())
    .then(html => {
      document.getElementById('contenidoReporteAnual').innerHTML = html;
    });
}

</script>

</body>
</html>
