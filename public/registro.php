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

$pageTitle = 'Registro - Café-Bar Lara';
$activePage = '';
$basePath = '';
require_once __DIR__ . '/includes/header.php';

// Obtener mensajes de error o éxito de la URL
$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
$redirect = $_GET['redirect'] ?? 'index.php'; // URL de destino después del registro

// Recuperar datos del formulario si hay error (preservar datos)
$formData = $_SESSION['registro_form_data'] ?? [];
// Limpiar datos de sesión después de recuperarlos
unset($_SESSION['registro_form_data']);
?>

<main class="py-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-6 col-lg-5">
				<div class="card shadow-lg border-0">
					<div class="card-body p-5">
						<div class="text-center mb-4">
							<h2 class="fw-bold text-primary">Crear cuenta</h2>
							<p class="text-muted">Únete a Café-Bar Lara</p>
						</div>
						
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
						
						<form method="POST" action="../app/process/process_registro.php" id="formRegistro" novalidate>
							<input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">
							<div class="mb-3">
								<label for="user" class="form-label">Nombre de usuario</label>
								<input type="text" class="form-control" id="user" name="user" 
									   value="<?php echo e($formData['user'] ?? ''); ?>" 
									   required autofocus>
								<div class="invalid-feedback" id="user-error"></div>
							</div>
							
							<div class="mb-3">
								<label for="email" class="form-label">Email</label>
								<input type="email" class="form-control" id="email" name="email" 
									   value="<?php echo e($formData['email'] ?? ''); ?>" 
									   required>
								<div class="invalid-feedback" id="email-error"></div>
							</div>
							
							<div class="mb-3">
								<label for="telefono" class="form-label">Teléfono</label>
								<input type="tel" class="form-control" id="telefono" name="telefono" 
									   value="<?php echo e($formData['telefono'] ?? ''); ?>" 
									   required placeholder="Ej: 612345678" pattern="[0-9]{9}" maxlength="9">
								<small class="form-text text-muted">9 dígitos numéricos</small>
								<div class="invalid-feedback" id="telefono-error"></div>
							</div>
							
							<div class="mb-3">
								<label for="password" class="form-label">Contraseña</label>
								<input type="password" class="form-control" id="password" name="password" required minlength="6">
								<small class="form-text text-muted">Mínimo 6 caracteres</small>
								<div class="invalid-feedback" id="password-error"></div>
							</div>
							
							<div class="mb-4">
								<label for="password_confirm" class="form-label">Confirmar contraseña</label>
								<input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="6">
								<div class="invalid-feedback" id="password_confirm-error"></div>
							</div>
							
							<button type="submit" class="btn btn-primary w-100 mb-3">Registrarse</button>
						</form>
						
						<div class="text-center mt-3">
							<p class="text-muted mb-0">¿Ya tienes cuenta? <a href="login_cliente.php?redirect=<?php echo urlencode($redirect); ?>" class="text-primary text-decoration-none">Inicia sesión</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<script>
(function() {
	'use strict';
	
	var form = document.getElementById('formRegistro');
	if (!form) return;
	
	form.addEventListener('submit', function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		// Limpiar mensajes de error previos
		var inputs = form.querySelectorAll('.form-control');
		inputs.forEach(function(input) {
			input.classList.remove('is-invalid');
		});
		
		var isValid = true;
		var errors = {};
		
		// Obtener valores
		var user = document.getElementById('user').value.trim();
		var email = document.getElementById('email').value.trim();
		var telefono = document.getElementById('telefono').value.trim();
		var password = document.getElementById('password').value;
		var password_confirm = document.getElementById('password_confirm').value;
		
		// Validación 1: Campos vacíos
		if (!user || !email || !telefono || !password || !password_confirm) {
			isValid = false;
			errors.general = 'Por favor, completa todos los campos';
			if (!user) {
				document.getElementById('user').classList.add('is-invalid');
				document.getElementById('user-error').textContent = 'El nombre de usuario es obligatorio';
			}
			if (!email) {
				document.getElementById('email').classList.add('is-invalid');
				document.getElementById('email-error').textContent = 'El email es obligatorio';
			}
			if (!telefono) {
				document.getElementById('telefono').classList.add('is-invalid');
				document.getElementById('telefono-error').textContent = 'El teléfono es obligatorio';
			}
			if (!password) {
				document.getElementById('password').classList.add('is-invalid');
				document.getElementById('password-error').textContent = 'La contraseña es obligatoria';
			}
			if (!password_confirm) {
				document.getElementById('password_confirm').classList.add('is-invalid');
				document.getElementById('password_confirm-error').textContent = 'La confirmación de contraseña es obligatoria';
			}
		}
		
		// Validación 2: Contraseña mínimo 6 caracteres
		if (password && password.length < 6) {
			isValid = false;
			document.getElementById('password').classList.add('is-invalid');
			document.getElementById('password-error').textContent = 'La contraseña debe tener al menos 6 caracteres';
		}
		
		// Validación 3: Contraseñas coinciden
		if (password && password_confirm && password !== password_confirm) {
			isValid = false;
			document.getElementById('password_confirm').classList.add('is-invalid');
			document.getElementById('password_confirm-error').textContent = 'Las contraseñas no coinciden';
		}
		
		// Validación 4: Email válido
		if (email) {
			var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (!emailRegex.test(email)) {
				isValid = false;
				document.getElementById('email').classList.add('is-invalid');
				document.getElementById('email-error').textContent = 'El email no es válido';
			}
		}
		
		// Validación 5: Teléfono 9 dígitos numéricos
		if (telefono) {
			var telefonoRegex = /^[0-9]{9}$/;
			if (!telefonoRegex.test(telefono)) {
				isValid = false;
				document.getElementById('telefono').classList.add('is-invalid');
				document.getElementById('telefono-error').textContent = 'El teléfono debe tener 9 dígitos numéricos';
			}
		}
		
		// Si es válido, enviar el formulario
		if (isValid) {
			form.submit();
		} else {
			// Mostrar mensaje general si hay error
			if (errors.general) {
				var alertDiv = document.createElement('div');
				alertDiv.className = 'alert alert-danger alert-dismissible fade show';
				alertDiv.setAttribute('role', 'alert');
				alertDiv.innerHTML = errors.general + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
				form.insertBefore(alertDiv, form.firstChild);
			}
		}
	});
})();
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>


