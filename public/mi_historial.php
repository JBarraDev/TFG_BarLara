<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/helpers.php';

requireClienteLogin();

$clienteId = getClienteId();
$error = null;

$pdo = getPdoConnection();

// Arrays para almacenar los datos
$pedidosPendientes = [];
$pedidosAceptados = [];
$pedidosRechazados = [];
$reservasPendientes = [];
$reservasProximas = [];
$reservasPasadas = [];

try {
	// ========== PEDIDOS ==========
	
	// Pedidos pendientes
	$stmt = $pdo->prepare('
		SELECT p.id, p.hora_pedido, p.hora_recogida, p.id_estado
		FROM pedidos p
		WHERE p.id_user = ? AND p.id_estado = 1
		ORDER BY p.hora_pedido DESC
	');
	$stmt->execute([$clienteId]);
	$pedidosPendientes = $stmt->fetchAll();
	
	// Obtener líneas de cada pedido pendiente
	foreach ($pedidosPendientes as &$pedido) {
		$stmt = $pdo->prepare('
			SELECT lp.id, lp.cantidad, lp.importe,
				   mi.nombre AS item_nombre, mi.precio AS item_precio
			FROM lineas_pedido lp
			INNER JOIN menu_items mi ON lp.id_menu_item = mi.id
			WHERE lp.id_pedido = ?
		');
		$stmt->execute([$pedido['id']]);
		$pedido['lineas'] = $stmt->fetchAll();
		
		// Calcular total
		$pedido['total'] = 0;
		foreach ($pedido['lineas'] as $linea) {
			$pedido['total'] += $linea['importe'];
		}
	}
	unset($pedido);
	
	// Pedidos aceptados
	$stmt = $pdo->prepare('
		SELECT p.id, p.hora_pedido, p.hora_recogida, p.id_estado
		FROM pedidos p
		WHERE p.id_user = ? AND p.id_estado = 2
		ORDER BY p.hora_pedido DESC
	');
	$stmt->execute([$clienteId]);
	$pedidosAceptados = $stmt->fetchAll();
	
	// Obtener líneas de cada pedido aceptado
	foreach ($pedidosAceptados as &$pedido) {
		$stmt = $pdo->prepare('
			SELECT lp.id, lp.cantidad, lp.importe,
				   mi.nombre AS item_nombre, mi.precio AS item_precio
			FROM lineas_pedido lp
			INNER JOIN menu_items mi ON lp.id_menu_item = mi.id
			WHERE lp.id_pedido = ?
		');
		$stmt->execute([$pedido['id']]);
		$pedido['lineas'] = $stmt->fetchAll();
		
		// Calcular total
		$pedido['total'] = 0;
		foreach ($pedido['lineas'] as $linea) {
			$pedido['total'] += $linea['importe'];
		}
	}
	unset($pedido);
	
	// Pedidos rechazados
	$stmt = $pdo->prepare('
		SELECT p.id, p.hora_pedido, p.hora_recogida, p.id_estado
		FROM pedidos p
		WHERE p.id_user = ? AND p.id_estado = 3
		ORDER BY p.hora_pedido DESC
	');
	$stmt->execute([$clienteId]);
	$pedidosRechazados = $stmt->fetchAll();
	
	// Obtener líneas de cada pedido rechazado
	foreach ($pedidosRechazados as &$pedido) {
		$stmt = $pdo->prepare('
			SELECT lp.id, lp.cantidad, lp.importe,
				   mi.nombre AS item_nombre, mi.precio AS item_precio
			FROM lineas_pedido lp
			INNER JOIN menu_items mi ON lp.id_menu_item = mi.id
			WHERE lp.id_pedido = ?
		');
		$stmt->execute([$pedido['id']]);
		$pedido['lineas'] = $stmt->fetchAll();
		
		// Calcular total
		$pedido['total'] = 0;
		foreach ($pedido['lineas'] as $linea) {
			$pedido['total'] += $linea['importe'];
		}
	}
	unset($pedido);
	
	// ========== RESERVAS ==========
	
	// Reservas pendientes
	$stmt = $pdo->prepare('
		SELECT r.id, r.fecha, r.num_personas, r.zona, r.id_estado
		FROM reservas r
		WHERE r.id_user = ? AND r.id_estado = 1
		ORDER BY r.fecha ASC
	');
	$stmt->execute([$clienteId]);
	$reservasPendientes = $stmt->fetchAll();
	
	// Próximas reservas aceptadas (futuras)
	$hoy = date('Y-m-d 00:00:00');
	$stmt = $pdo->prepare('
		SELECT r.id, r.fecha, r.num_personas, r.zona, r.id_estado
		FROM reservas r
		WHERE r.id_user = ? AND r.id_estado = 2 AND r.fecha >= ?
		ORDER BY r.fecha ASC
	');
	$stmt->execute([$clienteId, $hoy]);
	$reservasProximas = $stmt->fetchAll();
	
	// Reservas pasadas (aceptadas pero fecha < hoy) y rechazadas
	$stmt = $pdo->prepare('
		SELECT r.id, r.fecha, r.num_personas, r.zona, r.id_estado
		FROM reservas r
		WHERE r.id_user = ? AND ((r.id_estado = 2 AND r.fecha < ?) OR r.id_estado = 3)
		ORDER BY r.fecha DESC
	');
	$stmt->execute([$clienteId, $hoy]);
	$reservasPasadas = $stmt->fetchAll();
	
} catch (PDOException $e) {
	$error = 'Error al cargar el historial';
}

$pageTitle = 'Mi Historial - Café-Bar Lara';
$basePath = '';
$activePage = 'historial';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<h1 class="mb-4">Mi Historial</h1>
				
				<?php if ($error): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Error:</strong> <?php echo e($error); ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				<?php endif; ?>
				
				<!-- Pestañas para navegar entre pedidos y reservas -->
				<ul class="nav nav-tabs mb-4" id="historialTabs" role="tablist">
					<li class="nav-item" role="presentation">
						<button class="nav-link active" id="pedidos-tab" data-bs-toggle="tab" data-bs-target="#pedidos" type="button" role="tab" aria-controls="pedidos" aria-selected="true">
							Pedidos
						</button>
					</li>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="reservas-tab" data-bs-toggle="tab" data-bs-target="#reservas" type="button" role="tab" aria-controls="reservas" aria-selected="false">
							Reservas
						</button>
					</li>
				</ul>
				
				<div class="tab-content" id="historialTabsContent">
					<!-- TAB: PEDIDOS -->
					<div class="tab-pane fade show active" id="pedidos" role="tabpanel" aria-labelledby="pedidos-tab">
						<!-- Pedidos Pendientes -->
						<h2 class="h4 mb-3">Pedidos Pendientes</h2>
						<?php if (empty($pedidosPendientes)): ?>
							<div class="alert alert-info" role="alert">
								No tienes pedidos pendientes
							</div>
						<?php else: ?>
							<?php foreach ($pedidosPendientes as $pedido): ?>
								<div class="card shadow-sm mb-4">
									<div class="card-body">
										<div class="row mb-3">
											<div class="col-md-6">
												<h5 class="card-title">Pedido #<?php echo e($pedido['id']); ?></h5>
												<p class="text-muted mb-1">
													<strong>Fecha del pedido:</strong> 
													<?php 
													$horaPedido = new DateTime($pedido['hora_pedido']);
													echo e($horaPedido->format('d/m/Y H:i')); 
													?>
												</p>
												<p class="text-muted mb-1">
													<strong>Hora de recogida:</strong> 
													<?php 
													$horaRecogida = new DateTime($pedido['hora_recogida']);
													echo e($horaRecogida->format('d/m/Y H:i')); 
													?>
												</p>
											</div>
											<div class="col-md-6 text-end">
												<span class="badge bg-warning text-dark fs-6">Pendiente</span>
											</div>
										</div>
										
										<div class="table-responsive mb-3">
											<table class="table table-sm">
												<thead>
													<tr>
														<th>Producto</th>
														<th>Cantidad</th>
														<th>Precio unitario</th>
														<th>Importe</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($pedido['lineas'] as $linea): ?>
														<tr>
															<td><?php echo e($linea['item_nombre']); ?></td>
															<td><?php echo e($linea['cantidad']); ?></td>
															<td><?php echo number_format($linea['item_precio'], 2, ',', '.'); ?> €</td>
															<td><?php echo number_format($linea['importe'], 2, ',', '.'); ?> €</td>
														</tr>
													<?php endforeach; ?>
													<tr class="table-active">
														<td colspan="3" class="text-end fw-bold">Total:</td>
														<td class="fw-bold"><?php echo number_format($pedido['total'], 2, ',', '.'); ?> €</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
						
						<!-- Pedidos Aceptados -->
						<h2 class="h4 mb-3 mt-5">Pedidos Aceptados</h2>
						<?php if (empty($pedidosAceptados)): ?>
							<div class="alert alert-info" role="alert">
								No tienes pedidos aceptados
							</div>
						<?php else: ?>
							<?php foreach ($pedidosAceptados as $pedido): ?>
								<div class="card shadow-sm mb-4">
									<div class="card-body">
										<div class="row mb-3">
											<div class="col-md-6">
												<h5 class="card-title">Pedido #<?php echo e($pedido['id']); ?></h5>
												<p class="text-muted mb-1">
													<strong>Fecha del pedido:</strong> 
													<?php 
													$horaPedido = new DateTime($pedido['hora_pedido']);
													echo e($horaPedido->format('d/m/Y H:i')); 
													?>
												</p>
												<p class="text-muted mb-1">
													<strong>Hora de recogida:</strong> 
													<?php 
													$horaRecogida = new DateTime($pedido['hora_recogida']);
													echo e($horaRecogida->format('d/m/Y H:i')); 
													?>
												</p>
											</div>
											<div class="col-md-6 text-end">
												<span class="badge bg-success fs-6">Aceptado</span>
											</div>
										</div>
										
										<div class="table-responsive mb-3">
											<table class="table table-sm">
												<thead>
													<tr>
														<th>Producto</th>
														<th>Cantidad</th>
														<th>Precio unitario</th>
														<th>Importe</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($pedido['lineas'] as $linea): ?>
														<tr>
															<td><?php echo e($linea['item_nombre']); ?></td>
															<td><?php echo e($linea['cantidad']); ?></td>
															<td><?php echo number_format($linea['item_precio'], 2, ',', '.'); ?> €</td>
															<td><?php echo number_format($linea['importe'], 2, ',', '.'); ?> €</td>
														</tr>
													<?php endforeach; ?>
													<tr class="table-active">
														<td colspan="3" class="text-end fw-bold">Total:</td>
														<td class="fw-bold"><?php echo number_format($pedido['total'], 2, ',', '.'); ?> €</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
						
						<!-- Pedidos Rechazados -->
						<h2 class="h4 mb-3 mt-5">Pedidos Rechazados</h2>
						<?php if (empty($pedidosRechazados)): ?>
							<div class="alert alert-info" role="alert">
								No tienes pedidos rechazados
							</div>
						<?php else: ?>
							<?php foreach ($pedidosRechazados as $pedido): ?>
								<div class="card shadow-sm mb-4">
									<div class="card-body">
											<div class="row mb-3">
												<div class="col-md-6">
													<h5 class="card-title">Pedido #<?php echo e($pedido['id']); ?></h5>
													<p class="text-muted mb-1">
														<strong>Fecha del pedido:</strong> 
														<?php 
														$horaPedido = new DateTime($pedido['hora_pedido']);
														echo e($horaPedido->format('d/m/Y H:i')); 
														?>
													</p>
													<p class="text-muted mb-1">
														<strong>Hora de recogida:</strong> 
														<?php 
														$horaRecogida = new DateTime($pedido['hora_recogida']);
														echo e($horaRecogida->format('d/m/Y H:i')); 
														?>
													</p>
												</div>
											<div class="col-md-6 text-end">
												<span class="badge bg-danger fs-6">Rechazado</span>
											</div>
										</div>
										
										<div class="table-responsive mb-3">
											<table class="table table-sm">
												<thead>
													<tr>
														<th>Producto</th>
														<th>Cantidad</th>
														<th>Precio unitario</th>
														<th>Importe</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach ($pedido['lineas'] as $linea): ?>
														<tr>
															<td><?php echo e($linea['item_nombre']); ?></td>
															<td><?php echo e($linea['cantidad']); ?></td>
															<td><?php echo number_format($linea['item_precio'], 2, ',', '.'); ?> €</td>
															<td><?php echo number_format($linea['importe'], 2, ',', '.'); ?> €</td>
														</tr>
													<?php endforeach; ?>
													<tr class="table-active">
														<td colspan="3" class="text-end fw-bold">Total:</td>
														<td class="fw-bold"><?php echo number_format($pedido['total'], 2, ',', '.'); ?> €</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					
					<!-- TAB: RESERVAS -->
					<div class="tab-pane fade" id="reservas" role="tabpanel" aria-labelledby="reservas-tab">
						<!-- Reservas Pendientes -->
						<h2 class="h4 mb-3">Reservas Pendientes</h2>
						<?php if (empty($reservasPendientes)): ?>
							<div class="alert alert-info" role="alert">
								No tienes reservas pendientes
							</div>
						<?php else: ?>
							<div class="row">
								<?php foreach ($reservasPendientes as $reserva): ?>
									<div class="col-md-6 mb-3">
										<div class="card shadow-sm">
											<div class="card-body">
												<div class="d-flex justify-content-between align-items-start mb-2">
													<h5 class="card-title mb-0">Reserva #<?php echo e($reserva['id']); ?></h5>
													<span class="badge bg-warning text-dark">Pendiente</span>
												</div>
												<p class="text-muted mb-1">
													<strong>Fecha y hora:</strong> 
													<?php 
													$fechaObj = new DateTime($reserva['fecha']);
													echo e($fechaObj->format('d/m/Y H:i')); 
													?>
												</p>
												<p class="text-muted mb-1">
													<strong>Personas:</strong> <?php echo e($reserva['num_personas']); ?>
												</p>
												<p class="text-muted mb-0">
													<strong>Zona:</strong> 
													<span class="badge bg-info text-dark">
														<?php echo e(ucfirst($reserva['zona'])); ?>
													</span>
												</p>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						
						<!-- Próximas Reservas -->
						<h2 class="h4 mb-3 mt-5">Próximas Reservas</h2>
						<?php if (empty($reservasProximas)): ?>
							<div class="alert alert-info" role="alert">
								No tienes próximas reservas confirmadas
							</div>
						<?php else: ?>
							<div class="row">
								<?php foreach ($reservasProximas as $reserva): ?>
									<div class="col-md-6 mb-3">
										<div class="card shadow-sm">
											<div class="card-body">
												<div class="d-flex justify-content-between align-items-start mb-2">
													<h5 class="card-title mb-0">Reserva #<?php echo e($reserva['id']); ?></h5>
													<span class="badge bg-success">Aceptada</span>
												</div>
												<p class="text-muted mb-1">
													<strong>Fecha y hora:</strong> 
													<?php 
													$fechaObj = new DateTime($reserva['fecha']);
													echo e($fechaObj->format('d/m/Y H:i')); 
													?>
												</p>
												<p class="text-muted mb-1">
													<strong>Personas:</strong> <?php echo e($reserva['num_personas']); ?>
												</p>
												<p class="text-muted mb-0">
													<strong>Zona:</strong> 
													<span class="badge bg-info text-dark">
														<?php echo e(ucfirst($reserva['zona'])); ?>
													</span>
												</p>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						
						<!-- Reservas Pasadas -->
						<h2 class="h4 mb-3 mt-5">Reservas Pasadas</h2>
						<?php if (empty($reservasPasadas)): ?>
							<div class="alert alert-info" role="alert">
								No tienes reservas pasadas
							</div>
						<?php else: ?>
							<div class="row">
								<?php foreach ($reservasPasadas as $reserva): ?>
									<div class="col-md-6 mb-3">
										<div class="card shadow-sm">
											<div class="card-body">
												<div class="d-flex justify-content-between align-items-start mb-2">
													<h5 class="card-title mb-0">Reserva #<?php echo e($reserva['id']); ?></h5>
													<?php if ($reserva['id_estado'] == 2): ?>
														<span class="badge bg-secondary">Caducada</span>
													<?php elseif ($reserva['id_estado'] == 3): ?>
														<span class="badge bg-danger">Rechazada</span>
													<?php endif; ?>
												</div>
												<p class="text-muted mb-1">
													<strong>Fecha y hora:</strong> 
													<?php 
													$fechaObj = new DateTime($reserva['fecha']);
													echo e($fechaObj->format('d/m/Y H:i')); 
													?>
												</p>
												<p class="text-muted mb-1">
													<strong>Personas:</strong> <?php echo e($reserva['num_personas']); ?>
												</p>
												<p class="text-muted mb-0">
													<strong>Zona:</strong> 
													<span class="badge bg-info text-dark">
														<?php echo e(ucfirst($reserva['zona'])); ?>
													</span>
												</p>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

