<?php
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/carrito.php';

// Vaciar el carrito antes de cerrar sesión
clearCarrito();

// Destruir sesión de cliente
if (isset($_SESSION['cliente_id'])) {
	unset($_SESSION['cliente_id']);
	unset($_SESSION['cliente_user']);
	unset($_SESSION['cliente_email']);
}

// Redirigir al inicio
header('Location: index.php');
exit;



