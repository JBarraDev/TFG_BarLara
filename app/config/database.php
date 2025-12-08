<?php
// Función para obtener conexión PDO a la base de datos
function getPdoConnection(): PDO {
	try {
		$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devolver arrays asociativos
			PDO::ATTR_EMULATE_PREPARES => false, // Usar prepared statements reales (más seguro)
		];
		return new PDO($dsn, DB_USER, DB_PASS, $options);
	} catch (PDOException $e) {
		die('Error de conexión: ' . $e->getMessage());
	}
}

