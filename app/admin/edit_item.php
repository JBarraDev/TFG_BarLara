<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/helpers.php';

requireLogin();

$itemId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success = null;
$error = null;
$item = null;

// Validar ID
if ($itemId <= 0) {
	header('Location: edit_items.php?error=' . urlencode('ID de item no válido'));
	exit;
}

// Obtener datos del item
try {
	$pdo = getPdoConnection();
	$stmt = $pdo->prepare('
		SELECT mi.id, mi.nombre, mi.precio, mi.id_categoria, mi.disponible, mi.imagen_url
		FROM menu_items mi
		WHERE mi.id = ?
	');
	$stmt->execute([$itemId]);
	$item = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if (!$item) {
		header('Location: edit_items.php?error=' . urlencode('Item no encontrado'));
		exit;
	}
	
	// Obtener alérgenos asociados
	$stmt = $pdo->prepare('
		SELECT id_alergeno
		FROM menu_items_alergenos
		WHERE id_menu_item = ?
	');
	$stmt->execute([$itemId]);
	$alergenosItem = $stmt->fetchAll(PDO::FETCH_COLUMN);
	$item['alergenos'] = $alergenosItem;
	
} catch (PDOException $e) {
	header('Location: edit_items.php?error=' . urlencode('Error al cargar el item'));
	exit;
}

// Procesar eliminación del item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
	try {
		$pdo = getPdoConnection();
		$pdo->beginTransaction();

		// Eliminar relaciones de alérgenos
		$stmt = $pdo->prepare('DELETE FROM menu_items_alergenos WHERE id_menu_item = ?');
		$stmt->execute([$itemId]);

		// Eliminar imagen del servidor si existe
		if ($item['imagen_url'] && file_exists(__DIR__ . '/../../public/' . $item['imagen_url'])) {
			unlink(__DIR__ . '/../../public/' . $item['imagen_url']);
		}

		// Eliminar el item
		$stmt = $pdo->prepare('DELETE FROM menu_items WHERE id = ?');
		$stmt->execute([$itemId]);

		$pdo->commit();
		header('Location: edit_items.php?success=' . urlencode('Item eliminado correctamente'));
		exit;
	} catch (PDOException $e) {
		$pdo->rollBack();
		$error = 'Error al eliminar el item: ' . $e->getMessage();
	}
}

// Procesar formulario POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'delete')) {
	$nombre = trim($_POST['nombre'] ?? '');
	$precio = floatval($_POST['precio'] ?? 0);
	$id_categoria = intval($_POST['id_categoria'] ?? 0);
	$disponible = isset($_POST['disponible']) ? 1 : 0;
	$id_alergenos = $_POST['id_alergenos'] ?? [];
	$imagen_url = $item['imagen_url']; // Mantener la imagen actual por defecto
	$imagen_eliminada = isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == '1';

	// Validación
	if (empty($nombre)) {
		$error = 'El nombre es obligatorio';
	} elseif ($precio <= 0) {
		$error = 'El precio debe ser mayor que 0';
	} elseif ($id_categoria <= 0) {
		$error = 'Debes seleccionar una categoría';
	} else {
		// Procesar nueva imagen si se subió
		if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
			$uploadDir = __DIR__ . '/../../public/assets/img/items/';
			
			// Crear directorio si no existe
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0755, true);
			}

			$fileExtension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
			$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
			
			if (!in_array($fileExtension, $allowedExtensions)) {
				$error = 'Formato de imagen no permitido. Use: ' . implode(', ', $allowedExtensions);
			} else {
				$fileName = uniqid('item_', true) . '.' . $fileExtension;
				$uploadPath = $uploadDir . $fileName;
				
				if (move_uploaded_file($_FILES['imagen']['tmp_name'], $uploadPath)) {
					// Eliminar imagen anterior si existe
					if ($item['imagen_url'] && file_exists(__DIR__ . '/../../public/' . $item['imagen_url'])) {
						unlink(__DIR__ . '/../../public/' . $item['imagen_url']);
					}
					$imagen_url = 'assets/img/items/' . $fileName;
				} else {
					$error = 'Error al subir la imagen';
				}
			}
		} elseif ($imagen_eliminada) {
			// Eliminar imagen si se marcó para eliminar
			if ($item['imagen_url'] && file_exists(__DIR__ . '/../../public/' . $item['imagen_url'])) {
				unlink(__DIR__ . '/../../public/' . $item['imagen_url']);
			}
			$imagen_url = null;
		}

		if (!$error) {
			try {
				$pdo = getPdoConnection();
				$pdo->beginTransaction();

				// Actualizar menu_item
				$stmt = $pdo->prepare('
					UPDATE menu_items
					SET nombre = ?, precio = ?, id_categoria = ?, disponible = ?, imagen_url = ?
					WHERE id = ?
				');
				$stmt->execute([$nombre, $precio, $id_categoria, $disponible, $imagen_url, $itemId]);

				// Eliminar alérgenos actuales
				$stmt = $pdo->prepare('DELETE FROM menu_items_alergenos WHERE id_menu_item = ?');
				$stmt->execute([$itemId]);

				// Insertar nuevos alérgenos
				if (!empty($id_alergenos) && is_array($id_alergenos)) {
					$stmt = $pdo->prepare('
						INSERT INTO menu_items_alergenos (id_menu_item, id_alergeno)
						VALUES (?, ?)
					');
					foreach ($id_alergenos as $id_alergeno) {
						$stmt->execute([$itemId, intval($id_alergeno)]);
					}
				}

				$pdo->commit();
				$success = 'Item actualizado correctamente';
				
				// Recargar datos del item
				$stmt = $pdo->prepare('
					SELECT mi.id, mi.nombre, mi.precio, mi.id_categoria, mi.disponible, mi.imagen_url
					FROM menu_items mi
					WHERE mi.id = ?
				');
				$stmt->execute([$itemId]);
				$item = $stmt->fetch(PDO::FETCH_ASSOC);
				
				$stmt = $pdo->prepare('
					SELECT id_alergeno
					FROM menu_items_alergenos
					WHERE id_menu_item = ?
				');
				$stmt->execute([$itemId]);
				$alergenosItem = $stmt->fetchAll(PDO::FETCH_COLUMN);
				$item['alergenos'] = $alergenosItem;
				
			} catch (PDOException $e) {
				$pdo->rollBack();
				$error = 'Error al actualizar el item: ' . $e->getMessage();
			}
		}
	}
}

// Obtener categorías y alergenos para el formulario
try {
	$pdo = getPdoConnection();
	
	$categorias = $pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll();
	$alergenos = $pdo->query('SELECT id, nombre FROM alergenos ORDER BY nombre')->fetchAll();
} catch (PDOException $e) {
	$error = 'Error al cargar datos: ' . $e->getMessage();
	$categorias = [];
	$alergenos = [];
}

$pageTitle = 'Editar Item - Café-Bar Lara';
$basePath = '../../public/';
$activePage = 'edit_items';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-8 mx-auto">
				<div class="card shadow-sm">
					<div class="card-header">
						<h2 class="mb-0">Editar Item del Menú</h2>
					</div>
					<div class="card-body">
						<?php if ($success): ?>
							<div class="alert alert-success alert-dismissible fade show" role="alert">
								<?php echo e($success); ?>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
						<?php endif; ?>
						
						<?php if ($error): ?>
							<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<?php echo e($error); ?>
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>
						<?php endif; ?>
						
						<form method="POST" enctype="multipart/form-data">
							<div class="mb-3">
								<label for="nombre" class="form-label">Nombre del Item <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="nombre" name="nombre" 
									value="<?php echo e($item['nombre'] ?? ''); ?>" 
									   required>
							</div>
							
							<div class="mb-3">
								<label for="precio" class="form-label">Precio (€) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0.01" class="form-control" id="precio" name="precio" 
									value="<?php echo e($item['precio'] ?? ''); ?>" 
									   required>
							</div>
							
							<div class="mb-3">
								<label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
								<select class="form-select" id="id_categoria" name="id_categoria" required>
									<option value="">Selecciona una categoría</option>
									<?php foreach ($categorias as $categoria): ?>
										<option value="<?php echo $categoria['id']; ?>" 
												<?php echo ($item['id_categoria'] == $categoria['id']) ? 'selected' : ''; ?>>
											<?php echo e($categoria['nombre']); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							
							<div class="mb-3">
								<label for="imagen" class="form-label">Imagen</label>
								<?php if ($item['imagen_url']): ?>
									<div class="mb-2">
										<img src="<?php echo e($basePath); ?><?php echo e($item['imagen_url']); ?>" 
											 alt="Imagen actual" 
											 style="max-width: 200px; max-height: 200px; object-fit: cover;" 
											 class="img-thumbnail">
									</div>
									<div class="form-check mb-2">
										<input class="form-check-input" type="checkbox" id="eliminar_imagen" name="eliminar_imagen" value="1">
										<label class="form-check-label" for="eliminar_imagen">
											Eliminar imagen actual
										</label>
									</div>
								<?php endif; ?>
								<input type="file" class="form-control" id="imagen" name="imagen" 
									   accept="image/jpeg,image/png,image/webp,image/gif">
								<small class="form-text text-muted">Formatos permitidos: JPG, PNG, WEBP, GIF. Dejar vacío para mantener la imagen actual.</small>
							</div>
							
							<div class="mb-3">
								<label class="form-label">Alérgenos</label>
								<div class="row">
									<?php foreach ($alergenos as $alergeno): ?>
										<div class="col-md-4 mb-2">
											<div class="form-check">
												<input class="form-check-input" type="checkbox" 
													   id="alergeno_<?php echo $alergeno['id']; ?>" 
													   name="id_alergenos[]" 
													   value="<?php echo $alergeno['id']; ?>"
													   <?php echo (in_array($alergeno['id'], $item['alergenos'] ?? [])) ? 'checked' : ''; ?>>
												<label class="form-check-label" for="alergeno_<?php echo $alergeno['id']; ?>">
													<?php echo e($alergeno['nombre']); ?>
												</label>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
							
							<div class="mb-3 form-check">
								<input type="checkbox" class="form-check-input" id="disponible" name="disponible" 
									   <?php echo ($item['disponible']) ? 'checked' : ''; ?>>
								<label class="form-check-label" for="disponible">
									Disponible
								</label>
							</div>
							
							<div class="d-flex gap-2 justify-content-between">
								<div class="d-flex gap-2">
									<button type="submit" class="btn btn-primary">Actualizar Item</button>
									<a href="edit_items.php" class="btn btn-secondary">Cancelar</a>
								</div>
								<button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
									🗑️ Eliminar Item
								</button>
							</div>
						</form>
						
						<!-- Formulario oculto para eliminar -->
						<form id="formEliminar" method="POST" style="display: none;">
							<input type="hidden" name="action" value="delete">
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<script>
function confirmarEliminacion() {
	if (confirm('¿Estás seguro de que deseas eliminar este item de la carta? Esta acción no se puede deshacer.')) {
		document.getElementById('formEliminar').submit();
	}
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
