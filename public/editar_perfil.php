<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/helpers.php';

requireClienteLogin();

$clienteId = getClienteId();
$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

$pdo = getPdoConnection();

// Obtener datos actuales del cliente
$clienteData = null;
try {
	$stmt = $pdo->prepare('SELECT user, email, telefono FROM usuarios WHERE id = ?');
	$stmt->execute([$clienteId]);
	$clienteData = $stmt->fetch();
	
	if (!$clienteData) {
		$error = 'Error al cargar los datos del perfil';
	}
} catch (PDOException $e) {
	$error = 'Error al cargar los datos del perfil';
}

$pageTitle = 'Editar Perfil - Café-Bar Lara';
$basePath = '';
$activePage = 'perfil';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6 col-lg-5">
				<div class="card shadow-lg border-0">
					<div class="card-body p-5">
						<div class="text-center mb-4">
							<h2 class="fw-bold text-primary">Editar Perfil</h2>
							<p class="text-muted">Modifica tus datos personales</p>
						</div>
						
						<?php if ($error): ?>
							<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<?php echo e($error); ?>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
						<?php endif; ?>
						
						<?php if ($success): ?>
							<div class="alert alert-success alert-dismissible fade show" role="alert">
								<?php echo e($success); ?>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
						<?php endif; ?>
						
						<?php if ($clienteData): ?>
							<form method="POST" action="../app/process/process_editar_perfil.php">
								<div class="mb-3">
									<label for="user" class="form-label">Nombre de usuario</label>
									<input type="text" class="form-control" id="user" name="user" 
										   value="<?php echo e($clienteData['user']); ?>" 
										   required autofocus>
								</div>
								
								<div class="mb-3">
									<label for="email" class="form-label">Email</label>
									<input type="email" class="form-control" id="email" name="email" 
										   value="<?php echo e($clienteData['email']); ?>" 
										   required>
								</div>
								
								<div class="mb-3">
									<label for="telefono" class="form-label">Teléfono</label>
									<input type="tel" class="form-control" id="telefono" name="telefono" 
										   value="<?php echo e($clienteData['telefono'] ?? ''); ?>" 
										   required placeholder="Ej: 612345678" pattern="[0-9]{9}" maxlength="9">
									<small class="form-text text-muted">9 dígitos numéricos</small>
								</div>
								
								<hr class="my-4">
								
								<div class="mb-3">
									<label for="password" class="form-label">Nueva contraseña (opcional)</label>
									<input type="password" class="form-control" id="password" name="password" minlength="6">
									<small class="form-text text-muted">Deja en blanco si no quieres cambiar la contraseña. Mínimo 6 caracteres.</small>
								</div>
								
								<div class="mb-4">
									<label for="password_confirm" class="form-label">Confirmar nueva contraseña</label>
									<input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="6">
								</div>
								
								<button type="submit" class="btn btn-primary w-100 mb-3">Guardar Cambios</button>
							</form>
						<?php endif; ?>
						
						<div class="text-center mt-3">
							<a href="mi_historial.php" class="text-primary text-decoration-none">← Volver a Mi Historial</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

