<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

requireLogin();

$error = null;

// Procesar aceptar/rechazar pedido (solo admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$pedidoId = intval($_POST['pedido_id'] ?? 0);
	$accion = trim($_POST['accion'] ?? '');
	if ($pedidoId <= 0 || !in_array($accion, ['aceptar', 'rechazar'])) {
		$error = 'Datos inválidos';
	} else {
		try {
			$pdo = getPdoConnection();
			
			// Verificar que el pedido existe y está pendiente (estado 1)
			$stmt = $pdo->prepare('SELECT id, id_estado FROM pedidos WHERE id = ? AND id_estado = 1');
			$stmt->execute([$pedidoId]);
			$pedido = $stmt->fetch();
			
			if (!$pedido) {
				$error = 'Pedido no encontrado o ya procesado';
			} else {
				// Estado 2 = aceptado, 3 = rechazado
				$idEstado = ($accion === 'aceptar') ? 2 : 3;
				
				$stmt = $pdo->prepare('UPDATE pedidos SET id_estado = ? WHERE id = ?');
				$stmt->execute([$idEstado, $pedidoId]);
				
				$mensaje = ($accion === 'aceptar') 
					? 'Pedido aceptado correctamente' 
					: 'Pedido rechazado correctamente';
				
				header('Location: ../admin/pedidos.php?success=' . urlencode($mensaje));
				exit;
			}
		} catch (PDOException $e) {
			$error = 'Error al procesar el pedido';
		}
	}
}

header('Location: ../admin/pedidos.php' . ($error ? '?error=' . urlencode($error) : ''));
exit;

