<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

$error = null;

// Procesar login de cliente (no admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user = trim($_POST['user'] ?? '');
	$password = $_POST['password'] ?? '';

	if (empty($user) || empty($password)) {
		$error = 'Por favor, completa todos los campos';
	} else {
		try {
			$pdo = getPdoConnection();
			// Buscar cliente (id_rol != 1 o NULL)
			$stmt = $pdo->prepare('SELECT id, user, password, email FROM usuarios WHERE user = ? AND (id_rol IS NULL OR id_rol != 1)');
			$stmt->execute([$user]);
			$cliente = $stmt->fetch();

			if ($cliente && password_verify($password, $cliente['password'])) {
				// Cerrar sesión de admin si existe
				unset($_SESSION['user_id']);
				unset($_SESSION['user_name']);
				unset($_SESSION['user_role']);
				
				// Crear sesión de cliente
				$_SESSION['cliente_id'] = $cliente['id'];
				$_SESSION['cliente_user'] = $cliente['user'];
				$_SESSION['cliente_email'] = $cliente['email'];
				$_SESSION['LAST_ACTIVITY'] = time();
				
				// Redirigir a la página que intentaba acceder antes del login
				$redirect = urldecode($_POST['redirect'] ?? 'index.php');
				// Si el redirect es una URL completa (http:// o https://), extraer solo el pathname
				if (preg_match('/^https?:\/\//', $redirect)) {
					$parsedUrl = parse_url($redirect);
					$redirect = basename($parsedUrl['path'] ?? 'index.php');
					if (isset($parsedUrl['query'])) {
						$redirect .= '?' . $parsedUrl['query'];
					}
				}
				// Si el redirect es relativo a public/, convertirlo a ruta desde app/process/
				if (strpos($redirect, '../') !== 0 && strpos($redirect, '/') !== 0 && strpos($redirect, 'http') !== 0) {
					$redirect = '../../public/' . $redirect;
				}
				header('Location: ' . $redirect);
				exit;
			} else {
				$error = 'Usuario o contraseña incorrectos';
			}
		} catch (PDOException $e) {
			$error = 'Error al conectar con la base de datos';
		}
	}
}

if ($error && $_SERVER['REQUEST_METHOD'] === 'POST') {
	$_SESSION['login_cliente_form_data'] = ['user' => $_POST['user'] ?? ''];
}

$redirect = $_POST['redirect'] ?? '';
$redirectParam = $redirect ? '&redirect=' . urlencode($redirect) : '';
header('Location: ../../public/login_cliente.php' . ($error ? '?error=' . urlencode($error) . $redirectParam : ($redirectParam ? '?' . ltrim($redirectParam, '&') : '')));
exit;

