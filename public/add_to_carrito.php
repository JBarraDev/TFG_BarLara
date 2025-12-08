<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/carrito.php';

header('Content-Type: application/json');

if (!isClienteLoggedIn()) {
	echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
	exit;
}

// Endpoint AJAX para añadir productos al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$itemId = intval($_POST['item_id'] ?? 0);
	$cantidad = intval($_POST['cantidad'] ?? 1);
	
	if ($itemId <= 0) {
		echo json_encode(['success' => false, 'message' => 'ID inválido']);
		exit;
	}
	
	try {
		$pdo = getPdoConnection();
		// Verificar que el producto existe y está disponible
		$stmt = $pdo->prepare('SELECT id, nombre, precio FROM menu_items WHERE id = ? AND disponible = 1');
		$stmt->execute([$itemId]);
		$item = $stmt->fetch();
		
		if (!$item) {
			echo json_encode(['success' => false, 'message' => 'Producto no disponible']);
			exit;
		}
		
		// Añadir al carrito y devolver el nuevo total de items
		addToCarrito($item['id'], $item['precio'], $item['nombre'], $cantidad);
		echo json_encode(['success' => true, 'carrito_count' => getCarritoCount()]);
		exit;
	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => 'Error al añadir producto']);
		exit;
	}
}

echo json_encode(['success' => false, 'message' => 'Método no permitido']);
exit;

