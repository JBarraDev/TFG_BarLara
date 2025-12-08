<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../helpers/helpers.php';

requireLogin();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$usuarioId = intval($_POST['usuario_id'] ?? 0);
	$nuevoRol = intval($_POST['nuevo_rol'] ?? 0);
	
	if ($usuarioId <= 0 || !in_array($nuevoRol, [1, 2])) {
		$error = 'Datos inválidos';
	} else {
		try {
			$pdo = getPdoConnection();
			
			// Verificar que el usuario existe
			$stmt = $pdo->prepare('SELECT id, user, id_rol FROM usuarios WHERE id = ?');
			$stmt->execute([$usuarioId]);
			$usuario = $stmt->fetch();
			
			if (!$usuario) {
				$error = 'Usuario no encontrado';
			} else {
				// Verificar que no se está cambiando el rol del mismo usuario que está logueado
				// (opcional: puedes permitir esto o no según tus necesidades)
				if ($usuario['id'] == $_SESSION['user_id'] && $nuevoRol != 1) {
					$error = 'No puedes cambiar tu propio rol de administrador';
				} else {
					// Verificar que el rol existe
					$stmt = $pdo->prepare('SELECT id FROM roles WHERE id = ?');
					$stmt->execute([$nuevoRol]);
					$rol = $stmt->fetch();
					
					if (!$rol) {
						$error = 'Rol no válido';
					} else {
						// Actualizar el rol del usuario
						$stmt = $pdo->prepare('UPDATE usuarios SET id_rol = ? WHERE id = ?');
						$stmt->execute([$nuevoRol, $usuarioId]);
						
						$rolNombre = ($nuevoRol == 1) ? 'Administrador' : 'Cliente';
						$mensaje = 'Rol de ' . e($usuario['user']) . ' cambiado a ' . $rolNombre . ' correctamente';
						
						header('Location: ../admin/usuarios.php?success=' . urlencode($mensaje));
						exit;
					}
				}
			}
		} catch (PDOException $e) {
			$error = 'Error al cambiar el rol del usuario';
		}
	}
}

header('Location: ../admin/usuarios.php' . ($error ? '?error=' . urlencode($error) : ''));
exit;

