<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/helpers.php';
requireLogin();

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nombre = trim($_POST['nombre'] ?? '');
	$precio = floatval($_POST['precio'] ?? 0);
	$id_categoria = intval($_POST['id_categoria'] ?? 0);
	$disponible = isset($_POST['disponible']) ? 1 : 0;
	$id_alergenos = $_POST['id_alergenos'] ?? [];
	$imagen_url = null;

	if (empty($nombre)) {
		$error = 'El nombre es obligatorio';
	} elseif ($precio <= 0) {
		$error = 'El precio debe ser mayor que 0';
	} elseif ($id_categoria <= 0) {
		$error = 'Debes seleccionar una categoría';
	} else {
		if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
			$uploadDir = __DIR__ . '/../../public/assets/img/items/';
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
					$imagen_url = 'assets/img/items/' . $fileName;
				} else {
					$error = 'Error al subir la imagen';
				}
			}
		}

		if (!$error) {
			try {
				$pdo = getPdoConnection();
				$pdo->beginTransaction();
				$stmt = $pdo->prepare('INSERT INTO menu_items (nombre, precio, id_categoria, disponible, imagen_url, fecha_creacion) VALUES (?, ?, ?, ?, ?, NOW())');
				$stmt->execute([$nombre, $precio, $id_categoria, $disponible, $imagen_url]);
				$itemId = $pdo->lastInsertId();

				if (!empty($id_alergenos) && is_array($id_alergenos)) {
					$stmt = $pdo->prepare('INSERT INTO menu_items_alergenos (id_menu_item, id_alergeno) VALUES (?, ?)');
					foreach ($id_alergenos as $id_alergeno) {
						$stmt->execute([$itemId, intval($id_alergeno)]);
					}
				}

				$pdo->commit();
				$success = 'Item añadido correctamente al menú';
				$_POST = [];
			} catch (PDOException $e) {
				$pdo->rollBack();
				$error = 'Error al guardar el item: ' . $e->getMessage();
				if ($imagen_url && file_exists($uploadPath)) {
					unlink($uploadPath);
				}
			}
		}
	}
}

$pdo = getPdoConnection();
$categorias = $pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre')->fetchAll();
$alergenos = $pdo->query('SELECT id, nombre FROM alergenos ORDER BY nombre')->fetchAll();

if ($success) {
	header('Location: dashboard.php?success=' . urlencode($success));
	exit;
}

$pageTitle = 'Añadir Item - Café-Bar Lara';
$activePage = 'add_item';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-12 col-md-8 mx-auto">
				<div class="card shadow-sm">
					<div class="card-header">
						<h2 class="mb-0">Añadir Nuevo Item al Menú</h2>
					</div>
					<div class="card-body">
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
									   value="<?php echo e($_POST['nombre'] ?? ''); ?>" required>
							</div>
							
							<div class="mb-3">
								<label for="precio" class="form-label">Precio (€) <span class="text-danger">*</span></label>
								<input type="number" step="0.01" min="0.01" class="form-control" id="precio" name="precio" 
									   value="<?php echo e($_POST['precio'] ?? ''); ?>" required>
							</div>
							
							<div class="mb-3">
								<label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
								<select class="form-select" id="id_categoria" name="id_categoria" required>
									<option value="">Selecciona una categoría</option>
									<?php foreach ($categorias as $categoria): ?>
										<option value="<?php echo $categoria['id']; ?>" 
												<?php echo (isset($_POST['id_categoria']) && $_POST['id_categoria'] == $categoria['id']) ? 'selected' : ''; ?>>
											<?php echo e($categoria['nombre']); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							
							<div class="mb-3">
								<label for="imagen" class="form-label">Imagen</label>
								<input type="file" class="form-control" id="imagen" name="imagen" 
									   accept="image/jpeg,image/png,image/webp,image/gif">
								<small class="form-text text-muted">Formatos permitidos: JPG, PNG, WEBP, GIF</small>
							</div>
							
							<div class="mb-3">
								<label class="form-label">Alérgenos</label>
								<div class="row">
									<?php foreach ($alergenos as $alergeno): ?>
										<div class="col-md-4 mb-2">
											<div class="form-check">
												<input class="form-check-input" type="checkbox" id="alergeno_<?php echo $alergeno['id']; ?>" 
													   name="id_alergenos[]" value="<?php echo $alergeno['id']; ?>"
													   <?php echo in_array($alergeno['id'], $_POST['id_alergenos'] ?? []) ? 'checked' : ''; ?>>
												<label class="form-check-label" for="alergeno_<?php echo $alergeno['id']; ?>">
													<?php echo e($alergeno['nombre']); ?>
												</label>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
							
							<div class="mb-3 form-check">
								<input type="checkbox" class="form-check-input" id="disponible" name="disponible" checked>
								<label class="form-check-label" for="disponible">Disponible</label>
							</div>
							
							<div class="d-flex gap-2">
								<button type="submit" class="btn btn-primary">Guardar Item</button>
								<a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
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

