<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

requireLogin();

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;

// Obtener todos los items del menú
$items = [];
try {
	$pdo = getPdoConnection();
	$stmt = $pdo->query('
		SELECT mi.id, mi.nombre, mi.precio, mi.disponible, mi.imagen_url,
			   c.nombre AS categoria_nombre
		FROM menu_items mi
		INNER JOIN categorias c ON mi.id_categoria = c.id
		ORDER BY c.nombre, mi.nombre
	');
	$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
	$error = 'Error al cargar los items: ' . $e->getMessage();
}

$pageTitle = 'Editar Items - Café-Bar Lara';
$basePath = '../../public/';
$activePage = 'edit_items';
require_once __DIR__ . '/includes/header.php';
?>

<main class="py-5">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<h1 class="mb-4">Editar Items del Menú</h1>
				
				<?php if ($success): ?>
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				<?php endif; ?>
				
				<?php if ($error): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				<?php endif; ?>
				
				<?php if (empty($items)): ?>
					<div class="alert alert-info">
						No hay items en el menú. <a href="add_item.php">Añade el primero</a>.
					</div>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>ID</th>
									<th>Nombre</th>
									<th>Categoría</th>
									<th>Precio</th>
									<th>Imagen</th>
									<th>Disponible</th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($items as $item): ?>
									<tr>
										<td><?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo htmlspecialchars($item['categoria_nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
										<td><?php echo number_format($item['precio'], 2, ',', '.'); ?> €</td>
										<td>
											<?php if ($item['imagen_url']): ?>
												<img src="../../public/<?php echo htmlspecialchars($item['imagen_url'], ENT_QUOTES, 'UTF-8'); ?>" 
													 alt="<?php echo htmlspecialchars($item['nombre'], ENT_QUOTES, 'UTF-8'); ?>" 
													 style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
											<?php else: ?>
												<span class="text-muted">Sin imagen</span>
											<?php endif; ?>
										</td>
										<td>
											<?php if ($item['disponible']): ?>
												<span class="badge bg-success">Sí</span>
											<?php else: ?>
												<span class="badge bg-secondary">No</span>
											<?php endif; ?>
										</td>
										<td>
											<a href="edit_item.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">
												✏️ Editar
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
				
				<div class="mt-4">
					<a href="dashboard.php" class="btn btn-secondary">Volver al Dashboard</a>
					<a href="add_item.php" class="btn btn-primary">Añadir Nuevo Item</a>
				</div>
			</div>
		</div>
	</div>
</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
