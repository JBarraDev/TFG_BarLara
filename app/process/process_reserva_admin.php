<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

requireLogin();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$reservaId = intval($_POST['reserva_id'] ?? 0);
	$accion = trim($_POST['accion'] ?? '');
	if ($reservaId <= 0 || !in_array($accion, ['aceptar', 'rechazar'])) {
		$error = 'Datos inválidos';
	} else {
		try {
			$pdo = getPdoConnection();
			
			$stmt = $pdo->prepare('SELECT id, id_estado FROM reservas WHERE id = ? AND id_estado = 1');
			$stmt->execute([$reservaId]);
			$reserva = $stmt->fetch();
			
			if (!$reserva) {
				$error = 'Reserva no encontrada o ya procesada';
			} else {
				$idEstado = ($accion === 'aceptar') ? 2 : 3;
				
				$stmt = $pdo->prepare('UPDATE reservas SET id_estado = ? WHERE id = ?');
				$stmt->execute([$idEstado, $reservaId]);
				
				$mensaje = ($accion === 'aceptar') 
					? 'Reserva aceptada correctamente' 
					: 'Reserva rechazada correctamente';
				
				header('Location: ../admin/reservas.php?success=' . urlencode($mensaje));
				exit;
			}
		} catch (PDOException $e) {
			$error = 'Error al procesar la reserva';
		}
	}
}

header('Location: ../admin/reservas.php' . ($error ? '?error=' . urlencode($error) : ''));
exit;

