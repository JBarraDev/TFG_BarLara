<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/session.php';
require_once __DIR__ . '/../app/helpers/carrito.php';

requireClienteLogin();

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$horaRecogida = trim($_POST['hora_recogida'] ?? '');
	$carrito = getCarrito();
	
	if (empty($carrito)) {
		$error = 'El carrito está vacío';
	} elseif (empty($horaRecogida)) {
		$error = 'Debes seleccionar una hora de recogida';
	} elseif (!preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $horaRecogida)) {
		$error = 'Hora de recogida no válida';
	} else {
		try {
			$pdo = getPdoConnection();
			$clienteId = getClienteId();
			$horaPedido = date('Y-m-d H:i:s');
			$horaRecogidaCompleta = date('Y-m-d') . ' ' . $horaRecogida . ':00';
			$horaActual = new DateTime();
			$horaRecogidaObj = DateTime::createFromFormat('Y-m-d H:i:s', $horaRecogidaCompleta);
			
			if ($horaRecogidaObj < $horaActual) {
				$error = 'No puedes hacer un pedido con una hora de recogida anterior a la hora actual';
			} else {
				$pdo->beginTransaction();
				try {
					$stmt = $pdo->prepare('INSERT INTO pedidos (id_user, hora_pedido, hora_recogida, id_estado) VALUES (?, ?, ?, 1)');
					$stmt->execute([$clienteId, $horaPedido, $horaRecogidaCompleta]);
					$pedidoId = $pdo->lastInsertId();
					
					$stmt = $pdo->prepare('INSERT INTO lineas_pedido (id_pedido, id_menu_item, cantidad, importe) VALUES (?, ?, ?, ?)');
					foreach ($carrito as $item) {
						$stmt->execute([$pedidoId, $item['id_menu_item'], $item['cantidad'], $item['precio'] * $item['cantidad']]);
					}
					
					$pdo->commit();
					clearCarrito();
					header('Location: carrito.php?success=' . urlencode('Pedido generado. Recibirá un whatsapp o email de confirmación cuando se procese.'));
					exit;
				} catch (Exception $e) {
					$pdo->rollBack();
					throw $e;
				}
			}
		} catch (PDOException $e) {
			$error = 'Error al generar el pedido';
		}
	}
}

header('Location: carrito.php' . ($error ? '?error=' . urlencode($error) : ''));
exit;

