<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/helpers.php';

// Si ya está logueado, redirigir
if (isClienteLoggedIn()) {
	$redirect = $_GET['redirect'] ?? 'index.php';
	header('Location: ' . urldecode($redirect));
	exit;
}

$pageTitle = 'Iniciar sesión - Café-Bar Lara';
$activePage = '';
$basePath = '';
require_once __DIR__ . '/includes/header.php';

// Obtener mensajes de error o éxito de la URL
$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
$timeout = isset($_GET['timeout']) && $_GET['timeout'] == '1';
$redirect = $_GET['redirect'] ?? 'index.php'; // URL de destino después del login

// Recuperar datos del formulario si hay error (preservar nombre de usuario)
$formData = $_SESSION['login_cliente_form_data'] ?? [];
// Limpiar datos de sesión después de recuperarlos
unset($_SESSION['login_cliente_form_data']);
?>

<main class="py-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6 col-lg-5">
				<div class="card shadow-lg border-0">
					<div class="card-body p-5">
						<div class="text-center mb-4">
							<h2 class="fw-bold text-primary">Iniciar sesión</h2>
							<p class="text-muted">Accede a tu cuenta</p>
						</div>
						
						<?php if ($timeout): ?>
							<div class="alert alert-warning" role="alert">
								Tu sesión ha expirado por inactividad. Por favor, inicia sesión nuevamente.
							</div>
						<?php endif; ?>
						
						<?php if ($error): ?>
							<div class="alert alert-danger" role="alert">
								<?php echo e($error); ?>
							</div>
						<?php endif; ?>
						
						<?php if ($success): ?>
							<div class="alert alert-success" role="alert">
								<?php echo e($success); ?>
							</div>
						<?php endif; ?>
						
						<form method="POST" action="../app/process/process_login_cliente.php">
							<input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">
							<div class="mb-3">
								<label for="user" class="form-label">Nombre de usuario</label>
								<input type="text" class="form-control" id="user" name="user" 
									   value="<?php echo e($formData['user'] ?? ''); ?>" 
									   required autofocus>
							</div>
							
							<div class="mb-4">
								<label for="password" class="form-label">Contraseña</label>
								<input type="password" class="form-control" id="password" name="password" required>
							</div>
							
							<button type="submit" class="btn btn-primary w-100 mb-3">Iniciar sesión</button>
						</form>
						
						<div class="text-center mt-3">
							<p class="text-muted mb-0">¿No tienes cuenta? <a href="registro.php?redirect=<?php echo urlencode($redirect); ?>" class="text-primary text-decoration-none">Regístrate</a></p>
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

