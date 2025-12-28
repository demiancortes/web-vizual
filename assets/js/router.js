// assets/js/router.js

function cargarVista(vista){
  const contenedor = document.getElementById('appContent');

  contenedor.innerHTML = `
    <div class="loader">
      <div>
        <span class="spinner-border spinner-border-sm me-2"></span>
        Cargando…
      </div>
    </div>
  `;

  fetch('views/' + vista + '.php')
    .then(r => {
      if(!r.ok) throw new Error();
      return r.text();
    })
    .then(html => {
      contenedor.innerHTML = html;
      window.scrollTo({ top: 0 });

      // init por vista
      if(window.initViews?.[vista]){
        window.initViews[vista]();
      }
    })
    .catch(() => {
      contenedor.innerHTML = `
        <div class="alert alert-danger">
          No se pudo cargar la vista <strong>${vista}</strong>
        </div>
      `;
    });
}
