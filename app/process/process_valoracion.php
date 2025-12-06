<?php
// Archivo para procesar las valoraciones que envían los usuarios
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/valoraciones.php';

// Comprobar que el usuario esté logueado
if (!isClienteLoggedIn()) {
	echo json_encode(['success' => false, 'message' => 'Debes estar registrado para valorar']);
	exit;
}

$clienteId = getClienteId();

// Solo procesar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Obtener los datos del formulario
	$itemId = (int)$_POST['item_id'];
	$valoracion = (int)$_POST['valoracion'];
	$comentario = trim($_POST['comentario'] ?? '');
	
	// Validar que el item sea válido
	if ($itemId <= 0) {
		echo json_encode(['success' => false, 'message' => 'Item no válido']);
		exit;
	}
	
	// Validar que la valoración esté entre 1 y 5
	if ($valoracion < 1 || $valoracion > 5) {
		echo json_encode(['success' => false, 'message' => 'La valoración debe estar entre 1 y 5']);
		exit;
	}
	
	// Guardar la valoración en la base de datos
	$resultado = guardarValoracion($clienteId, $itemId, $valoracion, $comentario);
	
	if ($resultado) {
		// Obtener la nueva media y el total de valoraciones
		$nuevaPromedio = getValoracionPromedio($itemId);
		$nuevoTotal = getTotalValoraciones($itemId);
		
		// Devolver éxito con los nuevos datos
		echo json_encode([
			'success' => true,
			'message' => 'Valoración guardada correctamente',
			'valoracion_promedio' => $nuevaPromedio,
			'total_valoraciones' => $nuevoTotal
		]);
	} else {
		echo json_encode(['success' => false, 'message' => 'Error al guardar la valoración']);
	}
} else {
	echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

