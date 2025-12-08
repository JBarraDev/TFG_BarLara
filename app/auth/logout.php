<?php
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/carrito.php';

// Vaciar el carrito antes de cerrar sesión
clearCarrito();

session_unset(); // Elimina las variables de sesión
session_destroy(); // Destruye la sesión

header('Location: login.php');
exit;

