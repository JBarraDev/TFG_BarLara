<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/session.php';
require_once __DIR__ . '/../../helpers/helpers.php';

requireLogin();

$adminUser = getUserName();
$basePath = '../../public/';
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo e($pageTitle ?? 'Panel de Administración - Café-Bar Lara'); ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="<?php echo e($basePath); ?>assets/css/styles.css">
</head>
<body>
	<!-- Navbar Admin -->
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom">
		<div class="container">
			<a class="navbar-brand" href="dashboard.php">
				<img src="<?php echo e($basePath); ?>assets/img/logo/LogoLara_200px.svg" alt="Café Bar Lara" class="navbar-logo" style="filter: brightness(0) invert(1);">
			</a>
			<div class="collapse navbar-collapse" id="navbarAdminContent">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item d-flex align-items-center">
						<span class="nav-link text-light fw-semibold me-2 mb-0">Hola, <?php echo e($adminUser); ?></span>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="../auth/logout.php">Salir</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="../auth/ver_sitio.php" onclick="return confirm('Si sales al sitio se cerrará la sesión. ¿Deseas continuar?');">🌐 Ver Sitio</a>
					</li>
				</ul>
			</div>
		</div>
	</nav>

