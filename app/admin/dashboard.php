<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/helpers.php';

requireLogin();

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

$pdo = getPdoConnection();
// Contar reservas y pedidos pendientes (estado 1 = pendiente)
$reservasPendientes = (int)($pdo->query('SELECT COUNT(*) as total FROM reservas WHERE id_estado = 1')->fetch()['total'] ?? 0);
$pedidosPendientes = (int)($pdo->query('SELECT COUNT(*) as total FROM pedidos WHERE id_estado = 1')->fetch()['total'] ?? 0);

$pageTitle = 'Panel de Administración - Café-Bar Lara';
$activePage = 'dashboard';
require_once __DIR__ . '/includes/header.php';
?>

	<main class="py-5">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h1 class="mb-4">Panel de Administración</h1>
					
					<?php if ($success): ?>
						<div class="alert alert-success alert-dismissible fade show" role="alert">
							<?php echo e($success); ?>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					<?php endif; ?>
					
					<?php if ($error): ?>
						<div class="alert alert-danger alert-dismissible fade show" role="alert">
							<strong>Error:</strong> <?php echo e($error); ?>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					<?php endif; ?>

					<?php if ($reservasPendientes > 0): ?>
						<div class="alert alert-warning alert-dismissible fade show" role="alert">
							<strong>⚠️ Hay <?php echo $reservasPendientes; ?> reserva(s) pendiente(s) de confirmar o rechazar.</strong>
							<a href="reservas.php" class="alert-link ms-2">Gestionar reservas</a>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					<?php endif; ?>
					
					<?php if ($pedidosPendientes > 0): ?>
						<div class="alert alert-info alert-dismissible fade show" role="alert">
							<strong>📦 Hay <?php echo $pedidosPendientes; ?> pedido(s) pendiente(s) de confirmar o rechazar.</strong>
							<a href="pedidos.php" class="alert-link ms-2">Gestionar pedidos</a>
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Menú de opciones -->
			<div class="row">
				<div class="col-12">
					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<div class="d-flex flex-wrap gap-3">
								<a href="add_item.php" class="btn btn-primary btn-lg">
									➕ Añadir Elementos
								</a>
								<a href="edit_items.php" class="btn btn-outline-primary btn-lg">
									✏️ Editar Elementos
								</a>
								<a href="reservas.php" class="btn btn-outline-warning btn-lg position-relative">
									📅 Gestionar Reservas
									<?php if ($reservasPendientes > 0): ?>
										<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
											<?php echo $reservasPendientes; ?>
											<span class="visually-hidden">reservas pendientes</span>
										</span>
									<?php endif; ?>
								</a>
								<a href="pedidos.php" class="btn btn-outline-info btn-lg position-relative">
									📦 Gestionar Pedidos
									<?php if ($pedidosPendientes > 0): ?>
										<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
											<?php echo $pedidosPendientes; ?>
											<span class="visually-hidden">pedidos pendientes</span>
										</span>
									<?php endif; ?>
								</a>
								<a href="valoraciones.php" class="btn btn-outline-success btn-lg">
									⭐ Visualizar Valoraciones
								</a>
								<a href="usuarios.php" class="btn btn-outline-secondary btn-lg">
									👥 Gestión de Clientes
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<!-- Información del panel -->
			<div class="row">
				<div class="col-12">
					<div class="card shadow-sm">
						<div class="card-body">
							<h3 class="card-title">Bienvenido al Panel de Administración</h3>
							<p class="card-text">
								Desde aquí puedes gestionar los elementos del menú del Café-Bar Lara.
								Utiliza las opciones del menú para añadir nuevos elementos o editar los existentes.<br>
								También debes gestionar las reservas y los pedidos pendientes de confirmar o rechazar.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

