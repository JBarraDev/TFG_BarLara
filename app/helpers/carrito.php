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
function addToCarrito(int $itemId, float $precio, string $nombre, int $cantidad = 1): void {
	$carrito = getCarrito();
	
	if (isset($carrito[$itemId])) {
		$carrito[$itemId]['cantidad'] += $cantidad;
	} else {
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

// Función para actualizar la cantidad de un item en el carrito
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

// Función para obtener el total del carrito
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


