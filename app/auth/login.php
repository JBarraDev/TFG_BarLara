<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/helpers.php';

// Si ya está logueado como admin, redirigir al dashboard
if (isLoggedIn()) {
	header('Location: ../admin/dashboard.php');
	exit;
}

$error = $_GET['error'] ?? null;
$timeout = isset($_GET['timeout']) && $_GET['timeout'] == '1';

// Recuperar datos del formulario si hubo error (para no perder el username escrito)
$formData = $_SESSION['login_admin_form_data'] ?? [];
unset($_SESSION['login_admin_form_data']); // Limpiar después de usar
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login - Café-Bar Lara</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
	<div class="d-flex align-items-center justify-content-center min-vh-100 bg-light p-3">
		<div style="max-width: 400px; width: 100%;">
			<div class="card shadow-lg" style="width: 100%;">
				<div class="card-body p-5">
					<div class="text-center mb-4">
						<h2 class="fw-bold">Panel de Administración</h2>
						<p class="text-muted">Café-Bar Lara</p>
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
					
					<form method="POST" action="../process/process_login.php">
						<div class="mb-3">
							<label for="username" class="form-label">Usuario</label>
							<input type="text" class="form-control" id="username" name="username" 
								value="<?php echo e($formData['username'] ?? ''); ?>" 
								   required autofocus>
						</div>
						
						<div class="mb-4">
							<label for="password" class="form-label">Contraseña</label>
							<input type="password" class="form-control" id="password" name="password" required>
						</div>
						
						<button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
					</form>
					
					<div class="text-center mt-3">
						<a href="../../index.php" class="text-decoration-none">Volver a la página principal</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

