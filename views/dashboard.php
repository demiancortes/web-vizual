<!-- DASHBOARD -->

<style>
	/* Cards resumen (más compactas) */
	.card-resumen{
		border:none;
		border-radius:.75rem;
		color:#fff;
		padding:.7rem .8rem;
		height:100%;
	}

	.card-resumen .icon{
		font-size:1.5rem;
		opacity:.9;
	}

	.card-resumen .value{
		font-size:1.2rem;
		font-weight:700;
		line-height:1.1;
	}

	.card-resumen small{
		opacity:.9;
	}

	/* Cards alerta */
	.card-alerta{
		border:none;
		border-radius:.75rem;
		padding:.8rem .9rem;
		color:#fff;
		height:100%;
	}

	.card-resumen .icon{
		font-size:1.5rem;
		opacity:.9;
	}
	

	/* Cards menú */
	.card-menu{
		border:none;
		border-radius:.75rem;
		padding:1.1rem .9rem;
		background:#fff;
		text-align:center;
		box-shadow:0 4px 10px rgba(0,0,0,.06);
		transition:.2s;
		height:100%;
		cursor:pointer;
	}

	.card-menu:hover{
		transform:translateY(-2px);
		box-shadow:0 6px 14px rgba(0,0,0,.1);
	}

	.card-menu .icon{
		font-size:1.9rem;
		margin-bottom:.25rem;
	}

	.card-menu .title{
		font-weight:600;
		font-size:.9rem;
	}
</style>

<div class="container-fluid p-0">

	<!-- 📊 RESUMEN DEL MES -->
	<div class="row g-3 mb-3">

		<div class="col-6 col-md-3">
			<div class="card-resumen bg-primary">
				<small>Ventas del mes</small>
				<div class="d-flex justify-content-between align-items-center mt-1">
					<div class="value" id="dashVentas">0</div>
					<div class="icon"><i class="bi bi-receipt"></i></div>
				</div>
			</div>
		</div>

		<div class="col-6 col-md-3">
			<div class="card-resumen bg-dark">
				<small>Total de ventas</small>
				<div class="d-flex justify-content-between align-items-center mt-1">
					<div class="value" id="dashTotal">$0</div>
					<div class="icon"><i class="bi bi-currency-dollar"></i></div>
				</div>
			</div>
		</div>

		<div class="col-6 col-md-3">
			<div class="card-resumen bg-info">
				<small>No. persianas</small>
				<div class="d-flex justify-content-between align-items-center mt-1">
					<div class="value" id="dashPersianas">0</div>
					<div class="icon"><i class="bi bi-window"></i></div>
				</div>
			</div>
		</div>

		<div class="col-6 col-md-3">
			<div class="card-resumen bg-success">
				<small>Ganancia</small>
				<div class="d-flex justify-content-between align-items-center mt-1">
					<div class="value" id="dashGanancia">$0</div>
					<div class="icon"><i class="bi bi-cash-stack"></i></div>
				</div>
			</div>
		</div>

	</div>

	<!-- ⚠️ ALERTAS -->
	<div class="row g-3 mb-4">

		<div class="col-6 col-md-3">
			<div class="card-alerta bg-danger">
				<small>Por instalar</small>
				<div class="d-flex justify-content-between align-items-center mt-1">
					<div class="value" id="dashPorInstalar">0</div>
					<div class="icon"><i class="bi bi-tools"></i></div>
				</div>
			</div>
		</div>

		<div class="col-6 col-md-3">
			<div class="card-alerta bg-secondary">
				<small>Ticket promedio</small>
				<div class="d-flex justify-content-between align-items-center mt-1">
					<div class="value" id="dashTicket">$0.00</div>
					<div class="icon"><i class="bi bi-graph-up"></i></div>
				</div>
			</div>
		</div>

	</div>

	<!-- 🧭 MENÚ -->
	<div class="row g-3">

		<div class="col-6 col-md-3">
			<div class="card-menu" onclick="cargarVista('ventas')">
				<div class="icon text-primary"><i class="bi bi-list-check"></i></div>
				<div class="title">Ventas del mes</div>
			</div>
		</div>

		<div class="col-6 col-md-3">
			<div class="card-menu" onclick="cargarVista('reporte_anual')">
				<div class="icon text-success"><i class="bi bi-bar-chart-line"></i></div>
				<div class="title">Reporte anual</div>
			</div>
		</div>

		<div class="col-6 col-md-3">
			<div class="card-menu" onclick="cargarVista('clientes')">
				<div class="icon text-info"><i class="bi bi-people"></i></div>
				<div class="title">Clientes</div>
			</div>
		</div>

		<div class="col-6 col-md-3">
			<div class="card-menu" onclick="cargarVista('pendientes')">
				<div class="icon text-danger"><i class="bi bi-exclamation-circle"></i></div>
				<div class="title">Por instalar</div>
			</div>
		</div>

		<div class="col-6 col-md-3">
			<div class="card-menu" onclick="cargarVista('gasolina')">
				<div class="icon text-warning"><i class="bi bi-fuel-pump"></i></div>
				<div class="title">Gasolina</div>
			</div>
		</div>

		<div class="col-6 col-md-3">
			<div class="card-menu" onclick="cargarVista('publicidad')">
				<div class="icon text-secondary"><i class="bi bi-megaphone"></i></div>
				<div class="title">Publicidad</div>
			</div>
		</div>

	</div>

</div>
