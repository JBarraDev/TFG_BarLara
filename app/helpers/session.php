<?php
if (session_status() === PHP_SESSION_NONE) { // Si no hay sesión, se crea una nueva
	ini_set('session.gc_maxlifetime', 300);
	ini_set('session.cookie_lifetime', 0);
	session_set_cookie_params([
		'lifetime' => 0,
		'path' => '/',
		'domain' => '',
		'secure' => false, // No se requiere HTTPS para el desarrollo local
		'httponly' => true, // Evita que el script (JavaScript) pueda acceder a la cookie
		'samesite' => 'Lax' // Evita que la cookie se envíe en solicitudes entre sitios (CSRF)
	]);
	session_start();
}

define('SESSION_TIMEOUT', 300);

function checkSessionTimeout() {
	if (isset($_SESSION['LAST_ACTIVITY'])) {
		if (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT) {
			$isCliente = isset($_SESSION['cliente_id']);
			$isAdmin = isset($_SESSION['user_id']);
			
			// Vaciar el carrito antes de destruir la sesión
			if (isset($_SESSION['carrito'])) {
				require_once __DIR__ . '/carrito.php';
				clearCarrito();
			}
			
			session_unset();
			session_destroy();
			
			$redirectUrl = 'login_cliente.php?timeout=1';
			if ($isAdmin) {
				$redirectUrl = '../auth/login.php?timeout=1';
			}
			
			if (!headers_sent()) { // Si no se han enviado encabezados, se redirige a la página de login
				header('Location: ' . $redirectUrl);
				exit;
			}
			return false;
		}
	}
	
	$_SESSION['LAST_ACTIVITY'] = time();
	return true;
}

if (!isset($_SESSION['LAST_ACTIVITY'])) {
	$_SESSION['LAST_ACTIVITY'] = time();
}
checkSessionTimeout();

// Función para verificar si el usuario está logueado y si es administrador
function isLoggedIn(): bool {
	return isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 1;
}

function getUserId(): ?int {
	return $_SESSION['user_id'] ?? null;
}

function getUserName(): ?string {
	return $_SESSION['user_name'] ?? null;
}

// Función para verificar si el usuario está logueado y si es administrador
function requireLogin(): void {
	if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 1) {
		session_unset();
		session_destroy();
		header('Location: ../auth/login.php?error=' . urlencode('No tienes permisos'));
		exit;
	}
	
	try {
		require_once __DIR__ . '/../config/database.php';
		$pdo = getPdoConnection();
		$stmt = $pdo->prepare('SELECT id_rol FROM usuarios WHERE id = ?');
		$stmt->execute([$_SESSION['user_id']]);
		$user = $stmt->fetch();
		
		if (!$user || $user['id_rol'] != 1) {
			session_unset();
			session_destroy();
			header('Location: ../auth/login.php?error=' . urlencode('No tienes permisos'));
			exit;
		}
	} catch (Exception $e) {
		session_unset();
		session_destroy();
		header('Location: ../auth/login.php?error=' . urlencode('Error de verificación'));
		exit;
	}
}

// Función para verificar si el usuario está logueado y si es cliente
function isClienteLoggedIn(): bool {
	return isset($_SESSION['cliente_id']) && isset($_SESSION['cliente_user']);
}

function getClienteId(): ?int {
	return $_SESSION['cliente_id'] ?? null;
}

function getClienteUser(): ?string {
	return $_SESSION['cliente_user'] ?? null;
}

function getClienteEmail(): ?string {
	return $_SESSION['cliente_email'] ?? null;
}

// Función para verificar si el usuario está logueado y si es cliente
function requireClienteLogin(): void {
	if (!isClienteLoggedIn()) {
		$pathInfo = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
		$queryString = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
		$redirect = basename($pathInfo) . ($queryString ? '?' . $queryString : '');
		
		header('Location: login_cliente.php?redirect=' . urlencode($redirect));
		exit;
	}
}



