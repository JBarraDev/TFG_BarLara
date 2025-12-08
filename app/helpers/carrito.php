<?php
require_once __DIR__ . '/session.php';

// Si no existe el carrito, se crea un array vacío para asegurar el array.
if (!isset($_SESSION['carrito'])) {
	$_SESSION['carrito'] = [];
}

function getCarrito(): array {
	return $_SESSION['carrito'] ?? [];
}

// Función para añadir un item al carrito
// Añade un producto al carrito. Si ya existe, suma la cantidad
function addToCarrito(int $itemId, float $precio, string $nombre, int $cantidad = 1): void {
	$carrito = getCarrito();
	
	// Si el producto ya está en el carrito, incrementar cantidad
	if (isset($carrito[$itemId])) {
		$carrito[$itemId]['cantidad'] += $cantidad;
	} else {
		// Si no existe, crear nueva entrada
		$carrito[$itemId] = [
			'id_menu_item' => $itemId,
			'nombre' => $nombre,
			'precio' => $precio,
			'cantidad' => $cantidad
		];
	}
	
	$_SESSION['carrito'] = $carrito;
}

function removeFromCarrito(int $itemId): void {
	$carrito = getCarrito();
	unset($carrito[$itemId]);
	$_SESSION['carrito'] = $carrito;
}

// Actualiza la cantidad de un producto. Si es 0 o menos, lo elimina
function updateCantidadCarrito(int $itemId, int $cantidad): void {
	if ($cantidad <= 0) {
		removeFromCarrito($itemId);
		return;
	}
	
	$carrito = getCarrito();
	if (isset($carrito[$itemId])) {
		$carrito[$itemId]['cantidad'] = $cantidad;
		$_SESSION['carrito'] = $carrito;
	}
}

function clearCarrito(): void {
	$_SESSION['carrito'] = [];
}

// Calcula el total sumando precio * cantidad de cada producto
function getCarritoTotal(): float {
	$carrito = getCarrito();
	$total = 0;
	foreach ($carrito as $item) {
		$total += $item['precio'] * $item['cantidad'];
	}
	return $total;
}

// Función para obtener el número de items totales en el carrito
function getCarritoCount(): int {
	$carrito = getCarrito();
	$count = 0;
	foreach ($carrito as $item) {
		$count += $item['cantidad'];
	}
	return $count;
}


