<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user = trim($_POST['user'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$telefono = trim($_POST['telefono'] ?? '');
	$password = $_POST['password'] ?? '';
	$password_confirm = $_POST['password_confirm'] ?? '';
	$redirect = $_POST['redirect'] ?? 'index.php';

	if (empty($user) || empty($email) || empty($telefono) || empty($password) || empty($password_confirm)) {
		$error = 'Por favor, completa todos los campos';
	} elseif (strlen($password) < 6) {
		$error = 'La contraseña debe tener al menos 6 caracteres';
	} elseif ($password !== $password_confirm) {
		$error = 'Las contraseñas no coinciden';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = 'El email no es válido';
	} elseif (!preg_match('/^[0-9]{9}$/', $telefono)) {
		$error = 'El teléfono debe tener 9 dígitos numéricos';
	} else {
		try {
			$pdo = getPdoConnection();
			// Verificar que el usuario o email no existan ya
			$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE user = ? OR email = ?');
			$stmt->execute([$user, $email]);
			
			if ($stmt->fetch()) {
				$error = 'El nombre de usuario o email ya está en uso';
			} else {
				// Crear nuevo usuario cliente (id_rol = 2)
				$stmt = $pdo->prepare('INSERT INTO usuarios (user, email, telefono, password, id_rol) VALUES (?, ?, ?, ?, 2)');
				$stmt->execute([$user, $email, $telefono, password_hash($password, PASSWORD_DEFAULT)]);
				header('Location: ../../public/login_cliente.php?success=' . urlencode('Registro exitoso. Por favor, inicia sesión.') . '&redirect=' . urlencode($redirect));
				exit;
			}
		} catch (PDOException $e) {
			$error = 'Error al registrar el usuario';
		}
	}
}

if ($error && $_SERVER['REQUEST_METHOD'] === 'POST') {
	$_SESSION['registro_form_data'] = [
		'user' => $_POST['user'] ?? '',
		'email' => $_POST['email'] ?? '',
		'telefono' => $_POST['telefono'] ?? ''
	];
}

$redirect = $_POST['redirect'] ?? '';
$redirectParam = $redirect ? '&redirect=' . urlencode($redirect) : '';
header('Location: ../../public/registro.php' . ($error ? '?error=' . urlencode($error) . $redirectParam : ($redirectParam ? '?' . ltrim($redirectParam, '&') : '')));
exit;

