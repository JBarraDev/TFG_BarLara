<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/helpers.php';

requireLogin();

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

$pdo = getPdoConnection();

// Obtener reservas pendientes (id_estado = 1)
$reservas = [];
// Obtener próximas reservas aceptadas (id_estado = 2, fecha >= hoy)
$proximasReservas = [];
// Obtener reservas caducadas y rechazadas
$reservasCaducadasRechazadas = [];

try {
	// Reservas pendientes
	$stmt = $pdo->prepare('
		SELECT r.id, r.fecha, r.num_personas, r.zona, r.id_estado,
			   u.user, u.email, u.telefono
		FROM reservas r
		INNER JOIN usuarios u ON r.id_user = u.id
		WHERE r.id_estado = 1
		ORDER BY r.fecha ASC
	');
	$stmt->execute();
	$reservas = $stmt->fetchAll();
	
	// Próximas reservas aceptadas (futuras)
	$hoy = date('Y-m-d 00:00:00');
	$stmt = $pdo->prepare('
		SELECT r.id, r.fecha, r.num_personas, r.zona, r.id_estado,
			   u.user, u.email, u.telefono
		FROM reservas r
		INNER JOIN usuarios u ON r.id_user = u.id
		WHERE r.id_estado = 2 AND r.fecha >= ?
		ORDER BY r.fecha ASC
	');
	$stmt->execute([$hoy]);
	$proximasReservas = $stmt->fetchAll();
	
	// Reservas caducadas (aceptadas pero fecha < hoy) y rechazadas
	$stmt = $pdo->prepare('
		SELECT r.id, r.fecha, r.num_personas, r.zona, r.id_estado,
			   u.user, u.email, u.telefono
		FROM reservas r
		INNER JOIN usuarios u ON r.id_user = u.id
		WHERE (r.id_estado = 2 AND r.fecha < ?) OR r.id_estado = 3
		ORDER BY r.fecha DESC
	');
	$stmt->execute([$hoy]);
	$reservasCaducadasRechazadas = $stmt->fetchAll();
} catch (PDOException $e) {
	$error = 'Error al cargar las reservas';
}

$pageTitle = 'Gestionar Reservas - Café-Bar Lara';
$basePath = '../../public/';
$activePage = 'reservas';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<h1 class="mb-4">Gestionar Reservas</h1>
				
				<div class="mb-3">
					<a href="dashboard.php" class="btn btn-outline-secondary">
						← Volver al Panel
					</a>
				</div>
				
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

				<!-- Sección: Reservas Pendientes -->
				<h2 class="h4 mb-3 mt-4">Reservas Pendientes</h2>
				<?php if (empty($reservas)): ?>
					<div class="alert alert-info" role="alert">
						<strong>No hay reservas pendientes</strong>
					</div>
				<?php else: ?>
					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Fecha y Hora</th>
											<th>Cliente</th>
											<th>Email</th>
											<th>Teléfono</th>
											<th>Personas</th>
											<th>Zona</th>
											<th>Acciones</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($reservas as $reserva): ?>
											<tr>
												<td>
														<?php 
															$fechaObj = new DateTime($reserva['fecha']);
															echo e($fechaObj->format('d/m/Y H:i')); 
														?>
												</td>
												<td><?php echo e($reserva['user']); ?></td>
												<td><?php echo e($reserva['email']); ?></td>
												<td><?php echo e($reserva['telefono'] ?? '-'); ?></td>
												<td><?php echo e($reserva['num_personas']); ?></td>
												<td>
													<span class="badge bg-info text-dark">
														<?php echo e(ucfirst($reserva['zona'])); ?>
													</span>
												</td>
												<td>
													<div class="btn-group" role="group">
														<form method="POST" action="../process/process_reserva_admin.php" class="d-inline">
															<input type="hidden" name="reserva_id" value="<?php echo e($reserva['id']); ?>">
															<input type="hidden" name="accion" value="aceptar">
															<button type="submit" class="btn btn-success btn-sm" onclick="return confirm('¿Confirmar esta reserva?');">
																✓ Aceptar
															</button>
														</form>
														<form method="POST" action="../process/process_reserva_admin.php" class="d-inline">
															<input type="hidden" name="reserva_id" value="<?php echo e($reserva['id']); ?>">
															<input type="hidden" name="accion" value="rechazar">
															<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Rechazar esta reserva?');">
																✗ Rechazar
															</button>
														</form>
													</div>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<!-- Sección: Próximas Reservas -->
				<h2 class="h4 mb-3 mt-5">Próximas Reservas</h2>
				<?php if (empty($proximasReservas)): ?>
					<div class="alert alert-info" role="alert">
						<strong>No hay próximas reservas confirmadas</strong>
					</div>
				<?php else: ?>
					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Fecha y Hora</th>
											<th>Cliente</th>
											<th>Email</th>
											<th>Teléfono</th>
											<th>Personas</th>
											<th>Zona</th>
											<th>Estado</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($proximasReservas as $reserva): ?>
											<tr>
												<td>
													<?php 
													$fechaObj = new DateTime($reserva['fecha']);
													echo e($fechaObj->format('d/m/Y H:i')); 
													?>
												</td>
												<td><?php echo e($reserva['user']); ?></td>
												<td><?php echo e($reserva['email']); ?></td>
												<td><?php echo e($reserva['telefono'] ?? '-'); ?></td>
												<td><?php echo e($reserva['num_personas']); ?></td>
												<td>
													<span class="badge bg-info text-dark">
														<?php echo e(ucfirst($reserva['zona'])); ?>
													</span>
												</td>
												<td>
													<span class="badge bg-success">
														Aceptada
													</span>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<!-- Sección: Reservas Caducadas y Rechazadas -->
				<h2 class="h4 mb-3 mt-5">Reservas Caducadas y Rechazadas</h2>
				<?php if (empty($reservasCaducadasRechazadas)): ?>
					<div class="alert alert-info" role="alert">
						<strong>No hay reservas caducadas o rechazadas</strong>
					</div>
				<?php else: ?>
					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Fecha y Hora</th>
											<th>Cliente</th>
											<th>Email</th>
											<th>Teléfono</th>
											<th>Personas</th>
											<th>Zona</th>
											<th>Estado</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($reservasCaducadasRechazadas as $reserva): ?>
											<tr>
												<td>
													<?php 
													$fechaObj = new DateTime($reserva['fecha']);
													echo e($fechaObj->format('d/m/Y H:i')); 
													?>
												</td>
												<td><?php echo e($reserva['user']); ?></td>
												<td><?php echo e($reserva['email']); ?></td>
												<td><?php echo e($reserva['telefono'] ?? '-'); ?></td>
												<td><?php echo e($reserva['num_personas']); ?></td>
												<td>
													<span class="badge bg-info text-dark">
														<?php echo e(ucfirst($reserva['zona'])); ?>
													</span>
												</td>
												<td>
													<?php if ($reserva['id_estado'] == 2): ?>
														<span class="badge bg-secondary">Caducada</span>
													<?php elseif ($reserva['id_estado'] == 3): ?>
														<span class="badge bg-danger">Rechazada</span>
													<?php endif; ?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

