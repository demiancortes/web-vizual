// assets/js/dashboard.js

function cargarResumenDashboard(){
  fetch('api/dashboard_resumen.php')
    .then(r => r.json())
    .then(d => {
      animateValue('dashVentas', d.ventas);
      animateValue('dashTotal', d.total, '$');
      animateValue('dashPersianas', d.persianas);
      animateValue('dashGanancia', d.ganancia, '$');
      animateValue('dashTicket', d.ticket, '$', 2);
      animateValue('dashPorInstalar', d.pendientes);
    });
}

// registrar init
window.initViews = window.initViews || {};
window.initViews.dashboard = cargarResumenDashboard;
