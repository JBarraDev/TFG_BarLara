<?php
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/carrito.php';

header('Content-Type: application/json');

if (!isClienteLoggedIn()) {
	echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$itemId = intval($_POST['item_id'] ?? 0);
	$cantidad = intval($_POST['cantidad'] ?? 0);
	
	if ($itemId <= 0 || $cantidad < 0) {
		echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
		exit;
	}
	
	updateCantidadCarrito($itemId, $cantidad);
	echo json_encode(['success' => true]);
	exit;
}

echo json_encode(['success' => false, 'message' => 'Método no permitido']);
exit;

