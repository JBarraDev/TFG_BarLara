<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/helpers.php';

requireLogin();

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

$pdo = getPdoConnection();

// Obtener todos los usuarios con sus roles
$usuarios = [];

try {
	$stmt = $pdo->prepare('
		SELECT u.id, u.user, u.email, u.telefono, u.id_rol,
			   r.nombre AS rol_nombre
		FROM usuarios u
		INNER JOIN roles r ON u.id_rol = r.id
		ORDER BY u.id ASC
	');
	$stmt->execute();
	$usuarios = $stmt->fetchAll();
} catch (PDOException $e) {
	$error = 'Error al cargar los usuarios';
}

$pageTitle = 'Gestión de Clientes - Café-Bar Lara';
$activePage = 'usuarios';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<h1 class="mb-4">Gestión de Clientes</h1>
				
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

				<?php if (empty($usuarios)): ?>
					<div class="alert alert-info" role="alert">
						<strong>No hay usuarios registrados</strong>
					</div>
				<?php else: ?>
					<div class="card shadow-sm mb-4">
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>ID</th>
											<th>Usuario</th>
											<th>Email</th>
											<th>Teléfono</th>
											<th>Rol Actual</th>
											<th>Cambiar Rol</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($usuarios as $usuario): ?>
											<tr>
												<td><?php echo e($usuario['id']); ?></td>
												<td><?php echo e($usuario['user']); ?></td>
												<td><?php echo e($usuario['email']); ?></td>
												<td><?php echo e($usuario['telefono'] ?? '-'); ?></td>
												<td>
													<?php if ($usuario['id_rol'] == 1): ?>
														<span class="badge bg-danger">Administrador</span>
													<?php else: ?>
														<span class="badge bg-secondary">Cliente</span>
													<?php endif; ?>
												</td>
												<td>
													<form method="POST" action="../process/process_cambiar_rol.php" class="d-inline">
														<input type="hidden" name="usuario_id" value="<?php echo e($usuario['id']); ?>">
														<?php if ($usuario['id_rol'] == 1): ?>
															<input type="hidden" name="nuevo_rol" value="2">
															<button type="submit" class="btn btn-sm btn-outline-secondary" 
																	onclick="return confirm('¿Cambiar el rol de <?php echo e($usuario['user']); ?> de Administrador a Cliente?');">
																Cambiar a Cliente
															</button>
														<?php else: ?>
															<input type="hidden" name="nuevo_rol" value="1">
															<button type="submit" class="btn btn-sm btn-outline-danger" 
																	onclick="return confirm('¿Cambiar el rol de <?php echo e($usuario['user']); ?> de Cliente a Administrador?');">
																Cambiar a Administrador
															</button>
														<?php endif; ?>
													</form>
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

