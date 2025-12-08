<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/helpers.php';
require_once __DIR__ . '/../app/helpers/carrito.php';

// Requiere que el cliente esté logueado
requireClienteLogin();

$pageTitle = 'Carrito - Café-Bar Lara';
$activePage = '';
$basePath = '';
require_once __DIR__ . '/includes/header.php';

$carrito = getCarrito();
$total = getCarritoTotal();
$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
?>

<main class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<h1 class="mb-4">Carrito de Compras</h1>
				
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
				
				<?php if (empty($carrito)): ?>
					<div class="card shadow-sm">
						<div class="card-body text-center py-5">
							<h3 class="text-muted mb-3">Tu carrito está vacío</h3>
							<p class="text-muted mb-4">Añade productos desde la carta para empezar a comprar</p>
							<a href="carta.php" class="btn btn-primary">Ver Carta</a>
						</div>
					</div>
				<?php else: ?>
					<div class="row">
						<div class="col-lg-8 mb-4">
							<div class="card shadow-sm">
								<div class="card-body">
									<h3 class="card-title mb-4">Productos</h3>
									<?php foreach ($carrito as $item): ?>
										<div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom item-carrito" data-item-id="<?php echo e($item['id_menu_item']); ?>">
											<div class="flex-grow-1">
												<h5 class="mb-1"><?php echo e($item['nombre']); ?></h5>
												<p class="text-muted mb-0"><?php echo number_format($item['precio'], 2, ',', '.'); ?> € cada uno</p>
											</div>
											<div class="d-flex align-items-center gap-3">
												<div class="d-flex align-items-center gap-2">
													<button type="button" class="btn btn-sm btn-outline-secondary btn-quantity" data-action="decrease" data-item-id="<?php echo e($item['id_menu_item']); ?>">-</button>
													<span class="quantity-display fw-bold"><?php echo e($item['cantidad']); ?></span>
													<button type="button" class="btn btn-sm btn-outline-secondary btn-quantity" data-action="increase" data-item-id="<?php echo e($item['id_menu_item']); ?>">+</button>
												</div>
												<div class="text-end text-end-min-width">
													<strong class="text-primary"><?php echo number_format($item['precio'] * $item['cantidad'], 2, ',', '.'); ?> €</strong>
												</div>
												<button type="button" class="btn btn-sm btn-outline-danger btn-remove" data-item-id="<?php echo e($item['id_menu_item']); ?>">
													✕
												</button>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						
						<div class="col-lg-4">
							<div class="card shadow-sm sticky-top sticky-top-custom">
								<div class="card-body">
									<h3 class="card-title mb-4">Resumen</h3>
									<div class="d-flex justify-content-between mb-3">
										<span>Total:</span>
										<strong class="text-primary fs-4"><?php echo number_format($total, 2, ',', '.'); ?> €</strong>
									</div>
									
									<form method="POST" action="generar_pedido.php" id="formPedido">
										<div class="mb-3">
											<label for="hora_recogida" class="form-label fw-semibold">Hora de recogida</label>
											<select class="form-select" id="hora_recogida" name="hora_recogida" required>
												<option value="">Seleccione</option>
												<?php
												// Obtener hora actual
												$horaActual = new DateTime();
												$horaActualStr = $horaActual->format('H:i');
												$horaActualMinutos = (int)$horaActual->format('H') * 60 + (int)$horaActual->format('i');
												
												// Generar horas de 9:00 a 23:30 en intervalos de 30 minutos
												for ($h = 9; $h <= 23; $h++) {
													$hora00 = sprintf('%02d:00', $h);
													$hora00Minutos = $h * 60;
													$hora30 = sprintf('%02d:30', $h);
													$hora30Minutos = $h * 60 + 30;
													
													// Solo mostrar horas que no hayan pasado
													if ($hora00Minutos >= $horaActualMinutos) {
														echo '<option value="' . $hora00 . '">' . $hora00 . '</option>';
													}
													if ($h < 23 && $hora30Minutos >= $horaActualMinutos) {
														echo '<option value="' . $hora30 . '">' . $hora30 . '</option>';
													} elseif ($h == 23 && $hora30Minutos >= $horaActualMinutos) {
														// Para las 23:00, también añadir 23:30
														echo '<option value="23:30">23:30</option>';
													}
												}
												?>
											</select>
										</div>
										
										<button type="submit" class="btn btn-primary w-100 btn-lg">
											Generar Pedido
										</button>
									</form>
									
									<div class="mt-3 text-center">
										<a href="carta.php" class="text-decoration-none">← Seguir comprando</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</main>

<script>
// Manejo del carrito con JavaScript
document.addEventListener('DOMContentLoaded', function() {
	// Validación del formulario de pedido
	var formPedido = document.getElementById('formPedido');
	if (formPedido) {
		formPedido.addEventListener('submit', function(e) {
			var horaRecogida = document.getElementById('hora_recogida').value;
			if (horaRecogida) {
				var ahora = new Date();
				var horaActual = ahora.getHours() * 60 + ahora.getMinutes(); // Minutos desde medianoche
				var partesHora = horaRecogida.split(':');
				var horaSeleccionada = parseInt(partesHora[0]) * 60 + parseInt(partesHora[1]);
				
				if (horaSeleccionada < horaActual) {
					e.preventDefault();
					alert('No puedes hacer un pedido con una hora de recogida anterior a la hora actual');
					return false;
				}
			}
		});
	}
	
	// Botones de cantidad
	document.querySelectorAll('.btn-quantity').forEach(function(btn) {
		btn.addEventListener('click', function() {
			var action = this.getAttribute('data-action');
			var itemId = this.getAttribute('data-item-id');
			var cantidadDisplay = this.closest('.item-carrito').querySelector('.quantity-display');
			var currentCantidad = parseInt(cantidadDisplay.textContent);
			
			var newCantidad = action === 'increase' ? currentCantidad + 1 : currentCantidad - 1;
			
			if (newCantidad < 1) {
				removeItem(itemId);
				return;
			}
			
			updateCantidad(itemId, newCantidad);
		});
	});
	
	// Botones de eliminar
	document.querySelectorAll('.btn-remove').forEach(function(btn) {
		btn.addEventListener('click', function() {
			var itemId = this.getAttribute('data-item-id');
			removeItem(itemId);
		});
	});
	
	function updateCantidad(itemId, cantidad) {
		fetch('update_carrito.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: 'item_id=' + itemId + '&cantidad=' + cantidad
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				location.reload();
			}
		});
	}
	
	function removeItem(itemId) {
		fetch('remove_from_carrito.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
			},
			body: 'item_id=' + itemId
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				location.reload();
			}
		});
	}
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

