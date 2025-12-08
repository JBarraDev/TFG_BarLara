<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/helpers.php';

requireLogin();

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

$pdo = getPdoConnection();

// Obtener pedidos pendientes (id_estado = 1)
$pedidos = [];
// Obtener historial de pedidos (aceptados y rechazados)
$pedidosHistorial = [];

try {
	// Pedidos pendientes
	$stmt = $pdo->prepare('
		SELECT p.id, p.hora_pedido, p.hora_recogida, p.id_estado,
			   u.user, u.email, u.telefono
		FROM pedidos p
		INNER JOIN usuarios u ON p.id_user = u.id
		WHERE p.id_estado = 1
		ORDER BY p.hora_pedido ASC
	');
	$stmt->execute();
	$pedidos = $stmt->fetchAll();
	
	// Obtener líneas de cada pedido pendiente
	foreach ($pedidos as &$pedido) {
		$stmt = $pdo->prepare('
			SELECT lp.id, lp.cantidad, lp.importe,
				   mi.nombre AS item_nombre, mi.precio AS item_precio
			FROM lineas_pedido lp
			INNER JOIN menu_items mi ON lp.id_menu_item = mi.id
			WHERE lp.id_pedido = ?
		');
		$stmt->execute([$pedido['id']]);
		$pedido['lineas'] = $stmt->fetchAll();
	}
	unset($pedido);
	
	// Historial de pedidos (aceptados y rechazados)
	$stmt = $pdo->prepare('
		SELECT p.id, p.hora_pedido, p.hora_recogida, p.id_estado,
			   u.user, u.email, u.telefono
		FROM pedidos p
		INNER JOIN usuarios u ON p.id_user = u.id
		WHERE p.id_estado IN (2, 3)
		ORDER BY p.hora_pedido DESC
	');
	$stmt->execute();
	$pedidosHistorial = $stmt->fetchAll();
	
	// Obtener líneas de cada pedido del historial
	foreach ($pedidosHistorial as &$pedido) {
		$stmt = $pdo->prepare('
			SELECT lp.id, lp.cantidad, lp.importe,
				   mi.nombre AS item_nombre, mi.precio AS item_precio
			FROM lineas_pedido lp
			INNER JOIN menu_items mi ON lp.id_menu_item = mi.id
			WHERE lp.id_pedido = ?
		');
		$stmt->execute([$pedido['id']]);
		$pedido['lineas'] = $stmt->fetchAll();
		
		// Calcular total del pedido
		$pedido['total'] = 0;
		foreach ($pedido['lineas'] as $linea) {
			$pedido['total'] += $linea['importe'];
		}
	}
	unset($pedido);
} catch (PDOException $e) {
    $error = 'Error al cargar los pedidos';
}

$pageTitle = 'Gestionar Pedidos - Café-Bar Lara';
$basePath = '../../public/';
$activePage = 'pedidos';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<h1 class="mb-4">Gestionar Pedidos</h1>
				
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

				<?php if (empty($pedidos)): ?>
					<div class="alert alert-info" role="alert">
						<strong>No hay pedidos pendientes</strong>
					</div>
				<?php else: ?>
					<?php foreach ($pedidos as $pedido): ?>
						<div class="card shadow-sm mb-4">
							<div class="card-body">
								<div class="row mb-3">
									<div class="col-md-6">
										<h5 class="card-title">Pedido #<?php echo e($pedido['id']); ?></h5>
										<p class="text-muted mb-1">
											<strong>Cliente:</strong> <?php echo e($pedido['user']); ?>
										</p>
										<p class="text-muted mb-1">
											<strong>Email:</strong> <?php echo e($pedido['email']); ?>
										</p>
										<?php if ($pedido['telefono']): ?>
											<p class="text-muted mb-1">
												<strong>Teléfono:</strong> <?php echo e($pedido['telefono']); ?>
											</p>
										<?php endif; ?>
									</div>
									<div class="col-md-6">
										<?php 
										$horaPedido = new DateTime($pedido['hora_pedido']);
										$horaRecogida = new DateTime($pedido['hora_recogida']);
										?>
										<p class="text-muted mb-1">
											<strong>Hora del pedido:</strong> <?php echo e($horaPedido->format('d/m/Y H:i')); ?>
										</p>
										<p class="text-muted mb-1">
											<strong>Hora de recogida:</strong> <?php echo e($horaRecogida->format('d/m/Y H:i')); ?>
										</p>
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
											<?php 
											$totalPedido = 0;
											foreach ($pedido['lineas'] as $linea): 
												$totalPedido += $linea['importe'];
											?>
												<tr>
													<td><?php echo e($linea['item_nombre']); ?></td>
													<td><?php echo e($linea['cantidad']); ?></td>
													<td><?php echo number_format($linea['item_precio'], 2, ',', '.'); ?> €</td>
													<td><?php echo number_format($linea['importe'], 2, ',', '.'); ?> €</td>
												</tr>
											<?php endforeach; ?>
											<tr class="table-active">
												<td colspan="3" class="text-end fw-bold">Total:</td>
												<td class="fw-bold"><?php echo number_format($totalPedido, 2, ',', '.'); ?> €</td>
											</tr>
										</tbody>
									</table>
								</div>
								
								<div class="d-flex gap-2">
									<form method="POST" action="../process/process_pedido.php" class="d-inline">
										<input type="hidden" name="pedido_id" value="<?php echo e($pedido['id']); ?>">
										<input type="hidden" name="accion" value="aceptar">
										<button type="submit" class="btn btn-success" onclick="return confirm('¿Confirmar este pedido?');">
											✓ Aceptar
										</button>
									</form>
									<form method="POST" action="../process/process_pedido.php" class="d-inline">
										<input type="hidden" name="pedido_id" value="<?php echo e($pedido['id']); ?>">
										<input type="hidden" name="accion" value="rechazar">
										<button type="submit" class="btn btn-danger" onclick="return confirm('¿Rechazar este pedido?');">
											✗ Rechazar
										</button>
									</form>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>

				<!-- Sección: Historial de Pedidos -->
				<h2 class="h4 mb-3 mt-5">Historial de Pedidos</h2>
				<?php if (empty($pedidosHistorial)): ?>
					<div class="alert alert-info" role="alert">
						<strong>No hay pedidos en el historial</strong>
					</div>
				<?php else: ?>
					<?php foreach ($pedidosHistorial as $pedido): ?>
						<div class="card shadow-sm mb-4">
							<div class="card-body">
								<div class="row mb-3">
									<div class="col-md-6">
										<h5 class="card-title">
											Pedido #<?php echo e($pedido['id']); ?>
											<?php if ($pedido['id_estado'] == 2): ?>
												<span class="badge bg-success ms-2">Aceptado</span>
											<?php elseif ($pedido['id_estado'] == 3): ?>
												<span class="badge bg-danger ms-2">Rechazado</span>
											<?php endif; ?>
										</h5>
										<p class="text-muted mb-1">
											<strong>Cliente:</strong> <?php echo e($pedido['user']); ?>
										</p>
										<p class="text-muted mb-1">
											<strong>Email:</strong> <?php echo e($pedido['email']); ?>
										</p>
										<?php if ($pedido['telefono']): ?>
											<p class="text-muted mb-1">
												<strong>Teléfono:</strong> <?php echo e($pedido['telefono']); ?>
											</p>
										<?php endif; ?>
									</div>
									<div class="col-md-6">
										<?php 
										$horaPedido = new DateTime($pedido['hora_pedido']);
										$horaRecogida = new DateTime($pedido['hora_recogida']);
										?>
											<p class="text-muted mb-1">
												<strong>Hora del pedido:</strong> <?php echo e($horaPedido->format('d/m/Y H:i')); ?>
											</p>
											<p class="text-muted mb-1">
												<strong>Hora de recogida:</strong> <?php echo e($horaRecogida->format('d/m/Y H:i')); ?>
											</p>
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
											<?php 
											$totalPedido = 0;
											foreach ($pedido['lineas'] as $linea): 
												$totalPedido += $linea['importe'];
											?>
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
		</div>
	</div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>


