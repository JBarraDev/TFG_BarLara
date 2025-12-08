<?php
// Página que muestra la carta del menú con todos los productos
// Los usuarios pueden ver productos, alérgenos y valoraciones

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/helpers.php';
require_once __DIR__ . '/../app/helpers/valoraciones.php';

$databaseConnectionError = null;
$menuItemsByCategory = [];

// Comprobar si hay un usuario logueado
$isClienteLoggedIn = isClienteLoggedIn();
$clienteId = $isClienteLoggedIn ? getClienteId() : null;

try {
	$pdo = getPdoConnection();
	
	// Consulta SQL para obtener todos los productos disponibles con sus categorías y alérgenos
	$query = "
		SELECT 
			mi.id,
			mi.nombre,
			mi.precio,
			mi.imagen_url,
			c.id AS categoria_id,
			c.nombre AS categoria_nombre,
			a.id AS alergeno_id,
			a.nombre AS alergeno_nombre,
			a.icono_url AS alergeno_icono
		FROM menu_items mi
		INNER JOIN categorias c ON mi.id_categoria = c.id
		LEFT JOIN menu_items_alergenos mia ON mi.id = mia.id_menu_item
		LEFT JOIN alergenos a ON mia.id_alergeno = a.id
		WHERE mi.disponible = 1
		ORDER BY c.nombre, mi.nombre
	";
	
	// Ejecutar la consulta y obtener todos los resultados
	$stmt = $pdo->query($query);
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	// Organizar los productos por categorías (la consulta devuelve una fila por cada alérgeno)
	foreach ($results as $row) {
		$categoriaId = $row['categoria_id'];
		$itemId = $row['id'];
		
		// Crear la categoría si no existe
		if (!isset($menuItemsByCategory[$categoriaId])) {
			$menuItemsByCategory[$categoriaId] = [
				'nombre' => $row['categoria_nombre'],
				'items' => []
			];
		}
		
		// Crear el producto si no existe (puede tener múltiples alérgenos = múltiples filas)
		if (!isset($menuItemsByCategory[$categoriaId]['items'][$itemId])) {
			// Obtener valoraciones del producto
			$valoracionPromedio = getValoracionPromedio($itemId);
			$totalValoraciones = getTotalValoraciones($itemId);
			$valoracionUsuario = $clienteId ? getValoracionUsuario($clienteId, $itemId) : null;
			
			// Guardar la información del producto
			$menuItemsByCategory[$categoriaId]['items'][$itemId] = [
				'id' => $itemId,
				'nombre' => $row['nombre'],
				'precio' => $row['precio'],
				'imagen_url' => $row['imagen_url'],
				'alergenos' => [],
				'valoracion_promedio' => $valoracionPromedio,
				'total_valoraciones' => $totalValoraciones,
				'valoracion_usuario' => $valoracionUsuario
			];
		}
		
		// Añadir alérgenos (cada fila puede tener un alérgeno diferente)
		if ($row['alergeno_id'] && $row['alergeno_icono']) {
			$menuItemsByCategory[$categoriaId]['items'][$itemId]['alergenos'][$row['alergeno_id']] = [
				'nombre' => $row['alergeno_nombre'],
				'icono_url' => $row['alergeno_icono']
			];
		}
	}
	
} catch (Throwable $e) {
	$databaseConnectionError = $e->getMessage();
}

$pageTitle = 'Carta - Café-Bar Lara';
$activePage = 'carta';
$basePath = '';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Main -->
<main class="py-5">
		<div class="container">
			<?php if ($databaseConnectionError): ?>
				<div class="alert alert-danger" role="alert">
					Error de conexión: <?php echo e($databaseConnectionError); ?>
				</div>
			<?php else: ?>
				
				<?php if (empty($menuItemsByCategory)): ?>
					<div class="text-center py-5">
						<h2 class="text-muted">No hay items disponibles en este momento</h2>
					</div>
				<?php else: ?>
					
					<?php foreach ($menuItemsByCategory as $categoriaId => $categoria): ?>
						<!-- Sección de Categoría con Acordeón -->
						<div class="mb-4">
							<button class="btn btn-link w-100 text-start text-decoration-none p-0 mb-3" 
									type="button" 
									data-bs-toggle="collapse" 
									data-bs-target="#categoria-<?php echo e($categoriaId); ?>" 
									aria-expanded="true" 
									aria-controls="categoria-<?php echo e($categoriaId); ?>">
								<h2 class="mb-0 text-primary border-bottom pb-2 d-flex justify-content-between align-items-center">
									<span><?php echo e($categoria['nombre']); ?></span>
									<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-down collapse-icon" viewBox="0 0 16 16">
										<path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
									</svg>
								</h2>
							</button>
							
							<div class="collapse show" id="categoria-<?php echo e($categoriaId); ?>">
								<div class="row g-4">
									<?php foreach ($categoria['items'] as $item): ?>
										<div class="col-md-6 col-lg-4">
											<div class="card h-100 shadow-sm border-0 card-item">
												<!-- Imagen -->
												<?php if ($item['imagen_url']): ?>
													<img src="<?php echo e($item['imagen_url']); ?>" 
														 class="card-img-top card-img-fixed-height" 
														 alt="<?php echo e($item['nombre']); ?>">
												<?php else: ?>
													<img src="assets/img/items/no_image.jpg" 
														 class="card-img-top card-img-fixed-height" 
														 alt="<?php echo e($item['nombre']); ?>">
												<?php endif; ?>
												
												<div class="card-body d-flex flex-column">
													<!-- Nombre -->
													<h5 class="card-title mb-3">
														<?php echo e($item['nombre']); ?>
													</h5>
													
													<!-- Alérgenos -->
													<?php if (!empty($item['alergenos'])): ?>
														<div class="mb-3">
															<div class="d-flex flex-wrap gap-2">
																<?php foreach ($item['alergenos'] as $alergeno): ?>
																	<img src="<?php echo e($alergeno['icono_url']); ?>" 
																		 alt="<?php echo e($alergeno['nombre']); ?>" 
																		 title="<?php echo e($alergeno['nombre']); ?>"
																		 class="alergeno-icon alergeno-icon-size">
																<?php endforeach; ?>
															</div>
														</div>
													<?php endif; ?>
													
													<!-- Valoraciones -->
													<div class="mb-3">
														<?php if ($item['valoracion_promedio'] !== null): ?>
															<div class="d-flex align-items-center gap-2">
																<div class="valoracion-estrellas" data-valoracion="<?php echo e($item['valoracion_promedio']); ?>">
																	<?php 
																	$valoracion = $item['valoracion_promedio'];
																	for ($i = 1; $i <= 5; $i++): 
																		$clase = $i <= $valoracion ? 'estrella-llena' : ($i - 0.5 <= $valoracion ? 'estrella-media' : 'estrella-vacia');
																	?>
																		<span class="estrella <?php echo $clase; ?>">★</span>
																	<?php endfor; ?>
																</div>
																<small class="text-muted">
																	<?php echo number_format($item['valoracion_promedio'], 1, ',', '.'); ?> 
																	(<?php echo $item['total_valoraciones']; ?> <?php echo $item['total_valoraciones'] == 1 ? 'valoración' : 'valoraciones'; ?>)
																</small>
															</div>
														<?php else: ?>
															<small class="text-muted">Sin valoraciones aún</small>
														<?php endif; ?>
														
														<?php if ($isClienteLoggedIn): ?>
															<button type="button" 
																	class="btn btn-sm btn-outline-primary mt-2 btn-valorar" 
																	data-item-id="<?php echo e($item['id']); ?>"
																	data-item-nombre="<?php echo e($item['nombre']); ?>"
																	data-valoracion-actual="<?php echo $item['valoracion_usuario'] ? e($item['valoracion_usuario']['valoracion']) : ''; ?>"
																	data-comentario-actual="<?php echo $item['valoracion_usuario'] ? e($item['valoracion_usuario']['comentario'] ?? '') : ''; ?>">
																<?php echo $item['valoracion_usuario'] ? 'Modificar valoración' : 'Valorar'; ?>
															</button>
														<?php endif; ?>
													</div>
													
													<!-- Precio -->
													<div class="mt-auto pt-3 border-top">
														<div class="d-flex justify-content-between align-items-center mb-3">
															<span class="text-muted">Precio:</span>
															<h4 class="mb-0 text-primary fw-bold">
																<?php echo number_format($item['precio'], 2, ',', '.'); ?> €
															</h4>
														</div>
														<!-- Botón Pedir -->
														<button type="button" class="btn btn-primary w-100 btn-pedir" data-item-id="<?php echo e($item['id']); ?>">
															Pedir
														</button>
													</div>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
					
				<?php endif; ?>
				
			<?php endif; ?>
		</div>
	</main>

	<!-- Módulo de Valoración -->
	<?php if ($isClienteLoggedIn): ?>
	<div class="modal fade" id="modalValoracion" tabindex="-1" aria-labelledby="modalValoracionLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalValoracionLabel">Valorar producto</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="formValoracion">
						<input type="hidden" id="valoracion_item_id" name="item_id">
						<input type="hidden" id="valoracion_valoracion" name="valoracion" value="0">
						
						<div class="mb-3">
							<label class="form-label">Producto</label>
							<p class="fw-bold" id="valoracion_item_nombre"></p>
						</div>
						
						<div class="mb-3">
							<label class="form-label">Valoración</label>
							<div class="valoracion-estrellas-interactiva" id="valoracionEstrellas">
								<span class="estrella-interactiva" data-rating="1">★</span>
								<span class="estrella-interactiva" data-rating="2">★</span>
								<span class="estrella-interactiva" data-rating="3">★</span>
								<span class="estrella-interactiva" data-rating="4">★</span>
								<span class="estrella-interactiva" data-rating="5">★</span>
							</div>
							<small class="text-muted d-block mt-2">Selecciona de 1 a 5 estrellas</small>
						</div>
						
						<div class="mb-3">
							<label for="valoracion_comentario" class="form-label">Comentario (opcional)</label>
							<textarea class="form-control" id="valoracion_comentario" name="comentario" rows="3" placeholder="Escribe tu opinión sobre este producto..."></textarea>
						</div>
						
						<div class="alert alert-danger d-none" id="valoracion_error"></div>
						<div class="alert alert-success d-none" id="valoracion_success"></div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" id="btnGuardarValoracion">Guardar valoración</button>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

<?php
require_once __DIR__ . '/includes/footer.php';
?>


<script>
document.addEventListener('DOMContentLoaded', function() {
	// Manejar botones de valorar
	const botonesValorar = document.querySelectorAll('.btn-valorar');
	const modalElement = document.getElementById('modalValoracion');
	if (!modalElement) return;
	
	const modalValoracion = new bootstrap.Modal(modalElement);
	
	botonesValorar.forEach(btn => {
		btn.addEventListener('click', function() {
			const itemId = this.getAttribute('data-item-id');
			const itemNombre = this.getAttribute('data-item-nombre');
			const valoracionActual = this.getAttribute('data-valoracion-actual') || '0';
			const comentarioActual = this.getAttribute('data-comentario-actual') || '';
			
			// Llenar el modal
			document.getElementById('valoracion_item_id').value = itemId;
			document.getElementById('valoracion_item_nombre').textContent = itemNombre;
			document.getElementById('valoracion_comentario').value = comentarioActual;
			document.getElementById('valoracion_valoracion').value = valoracionActual;
			
			// Resetear estrellas
			resetearEstrellas();
			if (valoracionActual > 0) {
				marcarEstrellas(parseInt(valoracionActual));
			}
			
			// Limpiar mensajes
			document.getElementById('valoracion_error').classList.add('d-none');
			document.getElementById('valoracion_success').classList.add('d-none');
			
			modalValoracion.show();
		});
	});
	
	// Sistema de estrellas interactivo
	const estrellasInteractivas = document.querySelectorAll('.estrella-interactiva');
	let valoracionSeleccionada = 0;
	
	estrellasInteractivas.forEach((estrella, index) => {
		estrella.addEventListener('click', function() {
			valoracionSeleccionada = parseInt(this.getAttribute('data-rating'));
			document.getElementById('valoracion_valoracion').value = valoracionSeleccionada;
			marcarEstrellas(valoracionSeleccionada);
		});
		
		estrella.addEventListener('mouseenter', function() {
			const rating = parseInt(this.getAttribute('data-rating'));
			marcarEstrellas(rating);
		});
	});
	
	document.getElementById('valoracionEstrellas').addEventListener('mouseleave', function() {
		marcarEstrellas(valoracionSeleccionada);
	});
	
	function marcarEstrellas(rating) {
		estrellasInteractivas.forEach((estrella, index) => {
			if (index < rating) {
				estrella.classList.add('activa');
			} else {
				estrella.classList.remove('activa');
			}
		});
	}
	
	function resetearEstrellas() {
		valoracionSeleccionada = 0;
		estrellasInteractivas.forEach(estrella => {
			estrella.classList.remove('activa');
		});
	}
	
	// Guardar valoración
	document.getElementById('btnGuardarValoracion').addEventListener('click', function() {
		const formData = new FormData(document.getElementById('formValoracion'));
		const valoracion = parseInt(document.getElementById('valoracion_valoracion').value);
		
		if (valoracion < 1 || valoracion > 5) {
			mostrarError('Por favor, selecciona una valoración de 1 a 5 estrellas');
			return;
		}
		
		fetch('../app/process/process_valoracion.php', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				mostrarExito(data.message);
				setTimeout(() => {
					modalValoracion.hide();
					location.reload();
				}, 1000);
			} else {
				mostrarError(data.message);
			}
		})
		.catch(error => {
			mostrarError('Error al guardar la valoración');
		});
	});
	
	function mostrarError(mensaje) {
		const errorDiv = document.getElementById('valoracion_error');
		errorDiv.textContent = mensaje;
		errorDiv.classList.remove('d-none');
		document.getElementById('valoracion_success').classList.add('d-none');
	}
	
	function mostrarExito(mensaje) {
		const successDiv = document.getElementById('valoracion_success');
		successDiv.textContent = mensaje;
		successDiv.classList.remove('d-none');
		document.getElementById('valoracion_error').classList.add('d-none');
	}
});
</script>
