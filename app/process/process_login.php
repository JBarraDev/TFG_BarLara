<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

$error = null;

// Procesar login de administrador
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'] ?? '';
	$password = $_POST['password'] ?? '';

	if (empty($username) || empty($password)) {
		$error = 'Por favor, completa todos los campos';
	} else {
		try {
			$pdo = getPdoConnection();
			
			// Buscar usuario admin (id_rol = 1)
			$stmt = $pdo->prepare('
				SELECT u.id, u.user, u.password, u.id_rol
				FROM usuarios u
				WHERE u.user = ? AND u.id_rol = 1
			');
			$stmt->execute([$username]);
			$user = $stmt->fetch();

			// Verificar contraseña y crear sesión
			if ($user && password_verify($password, $user['password'])) {
				// Cerrar sesión de cliente si existe
				unset($_SESSION['cliente_id']);
				unset($_SESSION['cliente_user']);
				unset($_SESSION['cliente_email']);
				
				// Limpiar carrito al cambiar a sesión de admin
				if (isset($_SESSION['carrito'])) {
					require_once __DIR__ . '/../helpers/carrito.php';
					clearCarrito();
				}
				
				// Crear sesión de admin
				$_SESSION['user_id'] = $user['id'];
				$_SESSION['user_name'] = $user['user'];
				$_SESSION['user_role'] = $user['id_rol'];
				$_SESSION['LAST_ACTIVITY'] = time();
				header('Location: ../admin/dashboard.php');
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
	$_SESSION['login_admin_form_data'] = ['username' => $_POST['username'] ?? ''];
}

header('Location: ../auth/login.php' . ($error ? '?error=' . urlencode($error) : ''));
exit;

