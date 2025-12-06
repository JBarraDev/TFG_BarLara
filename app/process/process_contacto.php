<?php
require_once __DIR__ . '/../config/config.php';

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nombre = trim($_POST['nombre'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$asunto = trim($_POST['asunto'] ?? '');
	$mensaje = trim($_POST['mensaje'] ?? '');
	
	if (empty($nombre)) {
		$error = 'El nombre es obligatorio';
	} elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error = 'El email no es válido';
	} elseif (empty($asunto)) {
		$error = 'El asunto es obligatorio';
	} elseif (empty($mensaje) || strlen($mensaje) < 10) {
		$error = 'El mensaje debe tener al menos 10 caracteres';
	} else {
		$to = 'jbarra84@icloud.com';
		$subject = 'Contacto desde web: ' . $asunto;
		$emailMessage = "Has recibido un mensaje desde el formulario de contacto:\n\n";
		$emailMessage .= "Nombre: $nombre\nEmail: $email\nAsunto: $asunto\n\nMensaje:\n$mensaje\n";
		$headers = "From: $email\r\nReply-To: $email\r\n";
		
		mail($to, $subject, $emailMessage, $headers);
		$success = 'Tu mensaje ha sido recibido. Te responderemos lo antes posible.';
	}
}

if ($success) {
	header('Location: ../../public/contacto.php?success=' . urlencode($success));
} else {
	require_once __DIR__ . '/../helpers/session.php';
	$_SESSION['contacto_form_data'] = $_POST;
	header('Location: ../../public/contacto.php?error=' . urlencode($error ?: 'Error al enviar el mensaje'));
}
exit;

