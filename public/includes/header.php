<?php
require_once __DIR__ . '/../../app/helpers/session.php';
require_once __DIR__ . '/../../app/helpers/carrito.php';
require_once __DIR__ . '/../../app/helpers/helpers.php';

$basePath = $basePath ?? '';
$activePage = $activePage ?? '';
$isClienteLoggedIn = isClienteLoggedIn();
$clienteUser = $isClienteLoggedIn ? getClienteUser() : null;
$carritoCount = $isClienteLoggedIn ? getCarritoCount() : 0;
$isAdminLoggedIn = isLoggedIn();
$adminUser = $isAdminLoggedIn ? getUserName() : null;

$adminLogoutPath = ($basePath === '../../public/') ? '../auth/logout.php' : '../app/auth/logout.php';
?>
<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo e($pageTitle ?? 'Café-Bar Lara'); ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="<?php echo e($basePath); ?>assets/css/styles.css">
</head>
<body>
    <!-- Navbar -->
	<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom navbar-custom">
		<div class="container">
			<a class="navbar-brand" href="<?php echo e($basePath); ?>index.php">
				<img src="<?php echo e($basePath); ?>assets/img/logo/LogoLara_200px.svg" alt="Café Bar Lara" class="navbar-logo">
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
					<li class="nav-item">
						<a class="nav-link <?php echo $activePage === 'inicio' ? 'active' : ''; ?>" 
						   <?php echo $activePage === 'inicio' ? 'aria-current="page"' : ''; ?> 
						   href="<?php echo e($basePath); ?>index.php">Inicio</a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo $activePage === 'carta' ? 'active' : ''; ?>" 
						   <?php echo $activePage === 'carta' ? 'aria-current="page"' : ''; ?> 
						   href="<?php echo e($basePath); ?>carta.php">Carta</a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo $activePage === 'reservas' ? 'active' : ''; ?>" 
						   <?php echo $activePage === 'reservas' ? 'aria-current="page"' : ''; ?> 
						   href="<?php echo e($basePath); ?>reservas.php">Reservas</a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php echo $activePage === 'contacto' ? 'active' : ''; ?>" 
						   href="<?php echo e($basePath); ?>contacto.php">Contacto</a>
					</li>
					<?php if ($isAdminLoggedIn): ?>
						<li class="nav-item d-flex align-items-center">
							<span class="nav-link text-primary fw-semibold me-2 mb-0">Hola, <?php echo e($adminUser); ?> (Admin)</span>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo e($adminLogoutPath); ?>">Salir</a>
						</li>
					<?php elseif ($isClienteLoggedIn): ?>
						<li class="nav-item d-flex align-items-center">
							<span class="nav-link text-primary fw-semibold me-2 mb-0">Hola, <?php echo e($clienteUser); ?></span>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo $activePage === 'historial' ? 'active' : ''; ?>" 
							   <?php echo $activePage === 'historial' ? 'aria-current="page"' : ''; ?> 
							   href="<?php echo e($basePath); ?>mi_historial.php">Mi Historial</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php echo $activePage === 'perfil' ? 'active' : ''; ?>" 
							   <?php echo $activePage === 'perfil' ? 'aria-current="page"' : ''; ?> 
							   href="<?php echo e($basePath); ?>editar_perfil.php">Mi Perfil</a>
						</li>
						<li class="nav-item">
							<a class="nav-link position-relative" href="<?php echo e($basePath); ?>carrito.php">
								🛒 Carrito
								<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="carrito-badge" style="display: <?php echo $carritoCount > 0 ? 'inline' : 'none'; ?>;"><?php echo $carritoCount; ?></span>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo e($basePath); ?>logout.php">Salir</a>
						</li>
					<?php else: ?>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo e($basePath); ?>registro.php">Regístrate</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="<?php echo e($basePath); ?>login_cliente.php">Inicia sesión</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</nav>


