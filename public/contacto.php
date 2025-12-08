<?php
// Página de contacto donde los usuarios pueden enviar mensajes

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/helpers.php';

// Obtener mensajes de éxito o error desde la URL
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;

// Si hubo un error, recuperar los datos del formulario de la sesión
$formData = [];
if (isset($_SESSION['contacto_form_data'])) {
	$formData = $_SESSION['contacto_form_data'];
	unset($_SESSION['contacto_form_data']);
}

$pageTitle = 'Contacto - Café-Bar Lara';
$activePage = 'contacto';
$basePath = '';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-lg-8">
				<div class="text-center mb-5">
					<h1 class="display-4 fw-bold text-primary mb-3">Contacta con nosotros</h1>
					<p class="lead text-secondary">
						¿Tienes alguna pregunta, sugerencia o quieres hacer una consulta? Estamos aquí para ayudarte.
					</p>
				</div>

				<?php if ($success): ?>
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<strong>¡Mensaje enviado!</strong> <?php echo e($success); ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				<?php endif; ?>

				<?php if ($error): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<strong>Error:</strong> <?php echo e($error); ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				<?php endif; ?>

				<div class="card shadow-lg border-0 rounded-4 overflow-hidden">
					<div class="row g-0">
						<!-- Información de contacto -->
						<div class="col-lg-4 bg-light p-4 d-flex flex-column justify-content-center">
							<div class="mb-4">
								<h3 class="h4 fw-bold mb-4 text-dark">Información de contacto</h3>
								
								<div class="mb-4">
									<div class="d-flex align-items-start gap-3 mb-3">
										<div class="flex-shrink-0">
											<svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16" class="text-primary">
												<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
											</svg>
										</div>
										<div>
											<h6 class="fw-semibold mb-1 text-dark">Dirección</h6>
											<p class="mb-0 small text-secondary">Calle Caamaño 76<br>47013 Valladolid</p>
										</div>
									</div>
									
									<div class="d-flex align-items-start gap-3 mb-3">
										<div class="flex-shrink-0">
											<svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16" class="text-primary">
												<path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
											</svg>
										</div>
										<div>
											<h6 class="fw-semibold mb-1 text-dark">Email</h6>
											<a href="mailto:info@cafebarlara.es" class="text-secondary text-decoration-none small">
												info@cafebarlara.es
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Formulario -->
						<div class="col-lg-8">
							<div class="card-body p-4 p-lg-5">
								<form method="POST" action="../app/process/process_contacto.php" id="formContacto">
									<div class="mb-4">
										<label for="nombre" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
										<input type="text" 
											   class="form-control form-control-lg" 
											   id="nombre" 
											   name="nombre" 
											   placeholder="Tu nombre completo"
											   required
											   value="<?php echo e($formData['nombre'] ?? ''); ?>">
									</div>

									<div class="mb-4">
										<label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
										<input type="email" 
											   class="form-control form-control-lg" 
											   id="email" 
											   name="email" 
											   placeholder="tu@email.com"
											   required
											   value="<?php echo e($formData['email'] ?? ''); ?>">
									</div>

									<div class="mb-4">
										<label for="asunto" class="form-label fw-semibold">Asunto <span class="text-danger">*</span></label>
										<input type="text" 
											   class="form-control form-control-lg" 
											   id="asunto" 
											   name="asunto" 
											   placeholder="¿Sobre qué quieres contactarnos?"
											   required
											   value="<?php echo e($formData['asunto'] ?? ''); ?>">
									</div>

									<div class="mb-4">
										<label for="mensaje" class="form-label fw-semibold">Mensaje <span class="text-danger">*</span></label>
										<textarea class="form-control form-control-lg" 
												  id="mensaje" 
												  name="mensaje" 
												  rows="6" 
												  placeholder="Escribe tu mensaje aquí..."
												  required><?php echo e($formData['mensaje'] ?? ''); ?></textarea>
									</div>

									<div class="d-grid">
										<button type="submit" class="btn btn-primary btn-lg px-5 py-3">
											<span>Enviar mensaje</span>
											<svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="ms-2">
												<path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm-1.138-1.138L13.229 1.5 5.498 8.932Z"/>
											</svg>
										</button>
									</div>
								</form>
							</div>
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

