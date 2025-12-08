<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/session.php';

requireClienteLogin();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$fecha = trim($_POST['fecha'] ?? '');
	$hora = trim($_POST['hora'] ?? '');
	$num_personas = intval($_POST['num_personas'] ?? 0);
	$zona = trim($_POST['zona'] ?? '');

	if (empty($fecha) || empty($hora) || empty($num_personas) || empty($zona)) {
		$error = 'Por favor, completa todos los campos';
	} elseif ($num_personas < 2 || $num_personas > 12) {
		$error = 'El número de personas debe estar entre 2 y 12';
	} elseif (!in_array($zona, ['dentro', 'fuera'])) {
		$error = 'Zona no válida';
	} elseif ($fecha < date('Y-m-d', strtotime('+1 day'))) {
		$error = 'La fecha debe ser a partir de mañana';
	} else {
		try {
			$pdo = getPdoConnection();
			$fechaHora = $fecha . ' ' . $hora . ':00';
			$datetime = DateTime::createFromFormat('Y-m-d H:i:s', $fechaHora);
			
			if (!$datetime) {
				$error = 'Fecha y hora no válidas';
			} else {
				// Crear reserva pendiente (estado 1 = pendiente)
				$stmt = $pdo->prepare('INSERT INTO reservas (id_user, fecha, num_personas, zona, id_estado) VALUES (?, ?, ?, ?, 1)');
				$stmt->execute([getClienteId(), $datetime->format('Y-m-d H:i:s'), $num_personas, $zona]);
				header('Location: ../../public/reservas.php?success=' . urlencode('Reserva solicitada pendiente de confirmar. Recibirá un whatsapp o email cuando se confirme la reserva.'));
				exit;
			}
		} catch (PDOException $e) {
			$error = 'Error al procesar la reserva';
		}
	}
}

header('Location: ../../public/reservas.php' . ($error ? '?error=' . urlencode($error) : ''));
exit;

