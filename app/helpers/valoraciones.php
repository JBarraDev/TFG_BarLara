<?php
// Archivo para gestionar las valoraciones de los productos del menú
// Los usuarios pueden valorar del 1 al 5 y dejar comentarios

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/session.php';

// Función para obtener la nota media de un producto
function getValoracionPromedio($itemId) {
	// Conectar a la base de datos
	$pdo = getPdoConnection();
	
	// Calcular la media de todas las valoraciones de este producto
	$stmt = $pdo->prepare('SELECT AVG(valoracion) as promedio, COUNT(*) as total FROM valoraciones WHERE id_menu_item = ?');
	$stmt->execute([$itemId]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	
	// Si hay valoraciones, devolver la media redondeada a 1 decimal
	if ($result && $result['total'] > 0) {
		return round($result['promedio'], 1);
	}
	
	// Si no hay valoraciones, devolver null
	return null;
}

// Función para contar cuántas valoraciones tiene un producto
function getTotalValoraciones($itemId) {
	$pdo = getPdoConnection();
	$stmt = $pdo->prepare('SELECT COUNT(*) as total FROM valoraciones WHERE id_menu_item = ?');
	$stmt->execute([$itemId]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	return $result['total'] ?? 0;
}

// Función para obtener la valoración que hizo un usuario de un producto
function getValoracionUsuario($userId, $itemId) {
	$pdo = getPdoConnection();
	$stmt = $pdo->prepare('SELECT valoracion, comentario FROM valoraciones WHERE id_user = ? AND id_menu_item = ?');
	$stmt->execute([$userId, $itemId]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	return $result ? $result : null;
}

// Guarda o actualiza una valoración. Si el usuario ya valoró antes, actualiza; si no, crea nueva
function guardarValoracion($userId, $itemId, $valoracion, $comentario = null) {
	// Validar que la valoración esté entre 1 y 5
	if ($valoracion < 1 || $valoracion > 5) {
		return false;
	}
	
	$pdo = getPdoConnection();
	
	// Comprobar si el usuario ya valoró este producto antes
	$stmt = $pdo->prepare('SELECT id FROM valoraciones WHERE id_user = ? AND id_menu_item = ?');
	$stmt->execute([$userId, $itemId]);
	$existe = $stmt->fetch();
	
	if ($existe) {
		// Si ya existe, actualizar la valoración
		$stmt = $pdo->prepare('UPDATE valoraciones SET valoracion = ?, comentario = ?, fecha_actualizacion = NOW() WHERE id_user = ? AND id_menu_item = ?');
		$stmt->execute([$valoracion, $comentario, $userId, $itemId]);
	} else {
		// Si no existe, crear una nueva
		$stmt = $pdo->prepare('INSERT INTO valoraciones (id_user, id_menu_item, valoracion, comentario, fecha_creacion, fecha_actualizacion) VALUES (?, ?, ?, ?, NOW(), NOW())');
		$stmt->execute([$userId, $itemId, $valoracion, $comentario]);
	}
	
	return true;
}

// Función para obtener todas las valoraciones de un producto
function getValoracionesItem($itemId, $limit = 10) {
	$pdo = getPdoConnection();
	$stmt = $pdo->prepare('SELECT v.valoracion, v.comentario, v.fecha_creacion, u.user FROM valoraciones v INNER JOIN usuarios u ON v.id_user = u.id WHERE v.id_menu_item = ? ORDER BY v.fecha_creacion DESC LIMIT ?');
	$stmt->execute([$itemId, $limit]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para obtener valoraciones aleatorias para mostrar en el carrusel
// Solo devuelve valoraciones que tengan comentario
function getValoracionesAleatorias($limit = 5) {
	$pdo = getPdoConnection();
	$stmt = $pdo->prepare('SELECT v.valoracion, v.comentario, v.fecha_creacion, u.user AS usuario, mi.nombre AS item_nombre FROM valoraciones v INNER JOIN usuarios u ON v.id_user = u.id INNER JOIN menu_items mi ON v.id_menu_item = mi.id WHERE v.comentario IS NOT NULL AND v.comentario != "" ORDER BY RAND() LIMIT ?');
	$stmt->execute([$limit]);
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

