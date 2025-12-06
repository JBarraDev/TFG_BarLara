<?php
function getPdoConnection(): PDO {
	try {
		$dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		];
		return new PDO($dsn, DB_USER, DB_PASS, $options);
	} catch (PDOException $e) {
		die('Error de conexión: ' . $e->getMessage());
	}
}

