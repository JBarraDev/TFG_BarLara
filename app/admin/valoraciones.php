<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/helpers.php';

requireLogin();

$pageTitle = 'Gestionar Valoraciones - Café-Bar Lara';
$basePath = '../../public/';
$activePage = 'valoraciones';
require_once __DIR__ . '/includes/header.php';

$valoraciones = [];
try {
	$pdo = getPdoConnection();
	$query = "
		SELECT 
			v.id,
			v.valoracion,
			v.comentario,
			v.fecha_creacion,
			v.fecha_actualizacion,
			mi.nombre AS producto_nombre,
			u.user AS cliente_nombre
		FROM valoraciones v
		INNER JOIN menu_items mi ON v.id_menu_item = mi.id
		INNER JOIN usuarios u ON v.id_user = u.id
		ORDER BY v.fecha_creacion DESC
	";
	$stmt = $pdo->query($query);
	$valoraciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	$error = 'Error al cargar las valoraciones: ' . $e->getMessage();
}
?>

<main class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="d-flex justify-content-between align-items-center mb-4">
					<h1 class="mb-0">Gestionar Valoraciones</h1>
					<a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
				</div>
				
				<?php if (isset($error)): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<?php echo e($error); ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				<?php endif; ?>
				
				<?php if (empty($valoraciones)): ?>
					<div class="alert alert-info">
						No hay valoraciones registradas.
					</div>
				<?php else: ?>
					<div class="card shadow-sm">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>ID</th>
											<th>Producto</th>
											<th>Cliente</th>
											<th>Valoración</th>
											<th>Reseña</th>
											<th>Fecha</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($valoraciones as $valoracion): ?>
											<tr>
												<td><?php echo e($valoracion['id']); ?></td>
												<td><?php echo e($valoracion['producto_nombre']); ?></td>
												<td><?php echo e($valoracion['cliente_nombre']); ?></td>
												<td>
													<?php 
													$valoracionNum = (int)$valoracion['valoracion'];
													for ($i = 1; $i <= 5; $i++): 
														$color = $i <= $valoracionNum ? '#ffc107' : '#ddd';
													?>
														<span style="color: <?php echo $color; ?>;">★</span>
													<?php endfor; ?>
													<span class="ms-2">(<?php echo $valoracionNum; ?>/5)</span>
												</td>
												<td>
													<?php if (!empty($valoracion['comentario'])): ?>
														<?php echo nl2br(e($valoracion['comentario'])); ?>
													<?php else: ?>
														<span class="text-muted">Sin comentario</span>
													<?php endif; ?>
												</td>
												<td>
													<?php 
													$fecha = new DateTime($valoracion['fecha_creacion']);
													echo $fecha->format('d/m/Y H:i');
													?>
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

