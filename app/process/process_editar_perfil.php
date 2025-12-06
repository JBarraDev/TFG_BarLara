<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

requireClienteLogin();

$clienteId = getClienteId();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$user = trim($_POST['user'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$telefono = trim($_POST['telefono'] ?? '');
	$password = $_POST['password'] ?? '';
	$password_confirm = $_POST['password_confirm'] ?? '';
	
	// Validaciones básicas
	if (empty($user) || empty($email) || empty($telefono)) {
		$error = 'Por favor, completa todos los campos obligatorios';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = 'El email no es válido';
	} elseif (!preg_match('/^[0-9]{9}$/', $telefono)) {
		$error = 'El teléfono debe tener 9 dígitos numéricos';
	} elseif (!empty($password) && strlen($password) < 6) {
		$error = 'La contraseña debe tener al menos 6 caracteres';
	} elseif (!empty($password) && $password !== $password_confirm) {
		$error = 'Las contraseñas no coinciden';
	} else {
		try {
			$pdo = getPdoConnection();
			
			// Verificar si el nuevo nombre de usuario o email ya están en uso por otro usuario
			$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE (user = ? OR email = ?) AND id != ?');
			$stmt->execute([$user, $email, $clienteId]);
			$existingUser = $stmt->fetch();
			
			if ($existingUser) {
				$error = 'El nombre de usuario o email ya está en uso por otro usuario';
			} else {
				// Actualizar datos del usuario
				if (!empty($password)) {
					// Si se proporciona una nueva contraseña, actualizarla
					$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
					$stmt = $pdo->prepare('UPDATE usuarios SET user = ?, email = ?, telefono = ?, password = ? WHERE id = ?');
					$stmt->execute([$user, $email, $telefono, $hashedPassword, $clienteId]);
				} else {
					// Si no se proporciona contraseña, no actualizarla
					$stmt = $pdo->prepare('UPDATE usuarios SET user = ?, email = ?, telefono = ? WHERE id = ?');
					$stmt->execute([$user, $email, $telefono, $clienteId]);
				}
				
				// Actualizar datos en la sesión
				$_SESSION['cliente_user'] = $user;
				$_SESSION['cliente_email'] = $email;
				
				header('Location: ../../public/editar_perfil.php?success=' . urlencode('Perfil actualizado correctamente'));
				exit;
			}
		} catch (PDOException $e) {
			$error = 'Error al actualizar el perfil';
			if (defined('APP_DEBUG') && APP_DEBUG) {
				$error .= ': ' . $e->getMessage();
			}
		}
	}
}

// Si hay error, redirigir de vuelta al formulario
header('Location: ../../public/editar_perfil.php' . ($error ? '?error=' . urlencode($error) : ''));
exit;

