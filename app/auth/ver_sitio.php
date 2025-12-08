<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/session.php';

// Cerrar sesión del administrador
if (isset($_SESSION['user_id'])) {
	
	// Destruir sesión de administrador
	session_unset();
	session_destroy();
}

// Redirigir al sitio público
header('Location: ../../public/index.php');
exit;

