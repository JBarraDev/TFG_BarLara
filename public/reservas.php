<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/helpers.php';

// Requiere que el cliente esté logueado
requireClienteLogin();

$pageTitle = 'Reservar Mesa - Café-Bar Lara';
$activePage = 'reservas';
$basePath = '';
require_once __DIR__ . '/includes/header.php';

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

// Obtener fecha mínima (mañana)
$fechaMinima = date('Y-m-d', strtotime('+1 day'));
$fechaMinimaFormato = date('d/m/Y', strtotime('+1 day'));
?>

<main class="py-5" id="reservas">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-10">
				<div class="text-center mb-5">
					<h1 class="display-4 fw-bold text-primary mb-3">Haz tu reserva</h1>
					<h2 class="h4 text-secondary">Reserva tu mesa para una ocasión especial</h2>
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

				<div class="card shadow-lg border-0">
					<div class="card-body p-5">
						<form method="POST" action="../app/process/process_reserva.php" id="formReserva">
							<div class="row g-4">
								<!-- Calendario -->
								<div class="col-md-6">
									<label for="fecha" class="form-label fw-semibold mb-3">Selecciona el día</label>
									<input type="date" 
										   class="form-control form-control-lg" 
										   id="fecha" 
										   name="fecha" 
										   required 
										min="<?php echo e($fechaMinima); ?>">
									<small class="text-muted">Fecha mínima: <?php echo e($fechaMinimaFormato); ?></small>
								</div>

								<!-- Hora -->
								<div class="col-md-6">
									<label for="hora" class="form-label fw-semibold mb-3">Hora</label>
									<select class="form-select form-select-lg" id="hora" name="hora" required>
										<option value="">Seleccione</option>
										<?php
										// Generar horas de 9:00 a 23:00 en intervalos de 30 minutos
										for ($h = 9; $h <= 23; $h++) {
											$horas = [
												sprintf('%02d:00', $h),
												$h < 23 ? sprintf('%02d:30', $h) : null
											];
											foreach ($horas as $hora) {
												if ($hora !== null) {
													echo '<option value="' . e($hora) . '">' . e($hora) . '</option>';
												}
											}
										}
										?>
									</select>
								</div>

								<!-- Número de personas -->
								<div class="col-md-6">
									<label for="num_personas" class="form-label fw-semibold mb-3">Personas</label>
									<select class="form-select form-select-lg" id="num_personas" name="num_personas" required>
										<option value="">Seleccione</option>
										<?php for ($i = 2; $i <= 12; $i++): ?>
											<option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i === 1 ? 'persona' : 'personas'; ?></option>
										<?php endfor; ?>
									</select>
								</div>

								<!-- Zona -->
								<div class="col-md-6">
									<label for="zona" class="form-label fw-semibold mb-3">Zona</label>
									<select class="form-select form-select-lg" id="zona" name="zona" required>
										<option value="">Seleccione la zona</option>
										<option value="dentro">Dentro</option>
										<option value="fuera">Fuera</option>
									</select>
								</div>

								<!-- Botón Reservar -->
								<div class="col-12 mt-4">
									<button type="submit" class="btn btn-primary btn-lg w-100 py-3">
										<span class="fw-semibold">Reservar</span>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>



