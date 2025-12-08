<?php
// Página principal del sitio web
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/database.php';
require_once __DIR__ . '/../app/helpers/helpers.php';
require_once __DIR__ . '/../app/helpers/valoraciones.php';

// Intentar conectar a la base de datos
$databaseConnectionError = null;
try {
	$pdo = getPdoConnection();
} catch (Exception $e) {
	$databaseConnectionError = $e->getMessage();
}

// Obtener valoraciones aleatorias para mostrar en el carrusel. Se pasa por parametro el numero de valoraciones a obtener.
$valoracionesAleatorias = getValoracionesAleatorias(10);

$pageTitle = 'Café-Bar Lara';
$activePage = 'inicio';
$basePath = '';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Main -->
<main class="py-5">
		<section class="hero-section">
			<div class="hero-carousel">
				<div class="hero-slide active" style="background-image: url('assets/img/img/img_1.webp');"></div>
				<div class="hero-slide" style="background-image: url('assets/img/img/img_2.webp');"></div>
				<div class="hero-slide" style="background-image: url('assets/img/img/img_3.webp');"></div>
			</div>
			<div class="container hero-content">
				<div class="row justify-content-center">
					<div class="col-12 col-lg-8 text-center">
						<img src="assets/img/logo/LogoLara_200px_bl.svg" alt="Café Bar Lara" class="hero-logo mb-4">
						<h1 class="display-5 fw-bold">Café Bar Lara</h1>
						<p class="lead text-primary fw-semibold">
							Desayunos, tapas, vermús, copas... Y todo el fútbol.
						</p>
						
						<div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
							<a class="btn btn-menu btn-lg" href="carta.php">
								<span class="btn-text">Ver carta completa</span>
								<img src="assets/img/icon/icon_6.svg" alt="comida" width="22" height="22" class="btn-icon icon-btn">
							</a>
							<a class="btn btn-menu btn-lg" href="reservas.php">
								<span class="btn-text">Reserva una mesa</span>
								<img src="assets/img/icon/icon_7.svg" alt="comida" width="22" height="22" class="btn-icon icon-btn">
							</a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Sección de características con iconos -->
		<div class="container">
			<section class="py-4 feature-icons text-center">
				<h2 class="h1 fw-bold mb-3 text-uppercase">
					Todo lo que quieras en <span class="text-primary">Café Bar Lara</span>
				</h2>
				<p class="lead text-secondary mb-5">
					Tu sitio cercano para quedar y disfrutar de un bar de barrio de los de toda la vida.
				</p>
				<div class="row g-4 justify-content-center">
					<div class="col-6 col-md-3">
						<div class="icon-box mx-auto">
							<img src="assets/img/icon/icon_1.svg" class="img-fluid" alt="Desayunos" width="120" height="120">
						</div>
						<div class="mt-3 fw-semibold text-uppercase">Desayunos</div>
					</div>
					<div class="col-6 col-md-3">
						<div class="icon-box mx-auto">
							<img src="assets/img/icon/icon_2.svg" class="img-fluid" alt="Vermús" width="120" height="120">
						</div>
						<div class="mt-3 fw-semibold text-uppercase">Vermús</div>
					</div>
					<div class="col-6 col-md-3">
						<div class="icon-box mx-auto">
							<img src="assets/img/icon/icon_3.svg" class="img-fluid" alt="Tapas" width="120" height="120">
						</div>
						<div class="mt-3 fw-semibold text-uppercase">Tapas</div>
					</div>
					<div class="col-6 col-md-3">
						<div class="icon-box mx-auto">
							<img src="assets/img/icon/icon_5.svg" class="img-fluid" alt="Copas" width="120" height="120">
						</div>
						<div class="mt-3 fw-semibold text-uppercase">Copas</div>
					</div>
				</div>
			</section>

			<!-- Sección Paellas -->
			<section id="paella" class="py-5 my-5">
				<div class="container">
					<div class="row align-items-center g-4">
						<div class="col-lg-6">
							<div class="position-relative overflow-hidden rounded-4 shadow-lg section-image-container">
								<img src="assets/img/img/img_paella.png" 
									 alt="Paellas por encargo" 
									 class="w-100 h-100 object-fit-cover">
								<div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-overlay"></div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="ps-lg-4">
								<div class="d-inline-block mb-3">
									<span class="badge fs-6 px-3 py-2 badge-primary-custom">Para grupos</span>
								</div>
								<h2 class="display-5 fw-bold mb-4 text-primary">Paellas por encargo</h2>
								<p class="lead text-secondary mb-4">
									¿Planeando una celebración especial o una comida con amigos? Disfruta de nuestras deliciosas paellas caseras preparadas especialmente para tu grupo.
								</p>
								<div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
									<div class="flex-shrink-0">
										<img src="assets/img/icon/icon_14.svg" alt="información" width="32" height="32" class="text-primary">
									</div>
									<div>
										<strong class="text-primary d-block">Mínimo 4 personas</strong>
										<small class="text-muted">Ideal para grupos y celebraciones</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>

		<!-- Sección Canapés -->
		<section id="canapes" class="py-5 my-5 bg-light">
			<div class="container">
				<div class="row align-items-center g-4 flex-row-reverse">
					<div class="col-lg-6">
						<div class="position-relative overflow-hidden rounded-4 shadow-lg section-image-container">
							<img src="assets/img/img/img_canapes.png" 
								 alt="Canapés por encargo" 
								 class="w-100 h-100 object-fit-cover">
							<div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-overlay"></div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="pe-lg-4">
							<div class="d-inline-block mb-3">
								<span class="badge bg-warning text-dark fs-6 px-3 py-2">Eventos especiales</span>
							</div>
							<h2 class="display-5 fw-bold mb-4 text-dark">Canapés por encargo</h2>
							<p class="lead text-secondary mb-4">
								Para tus eventos y celebraciones, ofrecemos una amplia variedad de canapés gourmet elaborados con ingredientes frescos y de calidad. Perfectos para cualquier ocasión especial.
							</p>
							<ul class="list-unstyled d-flex flex-column gap-2">
								<li class="d-flex align-items-center gap-2">
									<svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="text-primary">
										<path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
									</svg>
									<span class="text-secondary">Ingredientes frescos y de calidad</span>
								</li>
								<li class="d-flex align-items-center gap-2">
									<svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="text-primary">
										<path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
									</svg>
									<span class="text-secondary">Variedad gourmet para todos los gustos</span>
								</li>
								<li class="d-flex align-items-center gap-2">
									<svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="text-primary">
										<path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
									</svg>
									<span class="text-secondary">Perfectos para cualquier ocasión</span>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Carrusel de Valoraciones -->
		<?php if (!empty($valoracionesAleatorias)): ?>
		<section id="valoraciones" class="py-5 bg-light">
			<div class="container">
				<h2 class="h3 fw-semibold mb-4 text-center">Lo que dicen nuestros clientes</h2>
				<div id="carouselValoraciones" class="carousel slide" data-bs-ride="carousel">
					<div class="carousel-inner">
						<?php foreach ($valoracionesAleatorias as $index => $valoracion): ?>
							<div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
								<div class="row justify-content-center">
									<div class="col-12 col-md-8 col-lg-6">
										<div class="card shadow-sm border-0 text-center p-4">
											<div class="card-body">
												<h5 class="card-title text-primary mb-3">
													<?php echo e($valoracion['item_nombre']); ?>
												</h5>
												
												<!-- Estrellas de valoración -->
												<div class="mb-3">
													<?php 
													$valoracionNum = (int)$valoracion['valoracion'];
													for ($i = 1; $i <= 5; $i++): 
														$clase = $i <= $valoracionNum ? 'estrella-llena' : 'estrella-vacia';
													?>
														<span class="estrella <?php echo $clase; ?>" style="font-size: 1.5rem; color: <?php echo $i <= $valoracionNum ? '#ffc107' : '#ddd'; ?>;">★</span>
													<?php endfor; ?>
												</div>
												
												<!-- Comentario -->
												<blockquote class="blockquote mb-3">
													<p class="mb-0"><?php echo nl2br(e($valoracion['comentario'])); ?></p>
												</blockquote>
												
												<!-- Usuario -->
												<footer class="blockquote-footer mt-3">
													<cite title="Usuario"><?php echo e($valoracion['usuario']); ?></cite>
												</footer>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					
					<?php if (count($valoracionesAleatorias) > 1): ?>
						<button class="carousel-control-prev" type="button" data-bs-target="#carouselValoraciones" data-bs-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Anterior</span>
						</button>
						<button class="carousel-control-next" type="button" data-bs-target="#carouselValoraciones" data-bs-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="visually-hidden">Siguiente</span>
						</button>
						
						<!-- Indicadores -->
						<div class="carousel-indicators">
							<?php foreach ($valoracionesAleatorias as $index => $valoracion): ?>
								<button type="button" data-bs-target="#carouselValoraciones" data-bs-slide-to="<?php echo $index; ?>" 
										<?php echo $index === 0 ? 'class="active" aria-current="true"' : ''; ?> 
										aria-label="Slide <?php echo $index + 1; ?>"></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
		<?php endif; ?>

		<section id="donde_estamos" class="py-4">
			<h2 class="h3 fw-semibold mb-3">Dónde estamos</h2>
			
			<div class="mb-4">
				<p class="text-secondary mb-0">
					El Café Bar Lara se encuentra en la calle Caamaño 76, en la ciudad de Valladolid. Zona de bares de toda la vida, por la que salir a tomar algo y disfrutar de un buen rato sin pasar sed ni hambre. Anímate a visitarnos y disfruta de un buen café o una buena tapa.
				</p>
			</div>

			<!-- Mapa de Google Maps -->
			<div class="mb-4">
				<iframe 
					src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2993.3355145293934!2d-4.724782199999999!3d41.6317777!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd4712d2515b9679%3A0x1fa30fa7d4662188!2sCafe-Bar%20Lara!5e0!3m2!1ses!2ses!4v1737820800000!5m2!1ses!2ses" 
					width="100%" 
					height="450" 
					allowfullscreen="" 
					loading="lazy" 
					referrerpolicy="no-referrer-when-downgrade"
					class="rounded shadow-sm iframe-maps">
				</iframe>
			</div>

			<!-- Información de ubicación -->
			<div class="d-flex flex-column gap-2">
				<div class="d-flex align-items-center gap-2">
					<svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="text-primary">
						<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
					</svg>
					<span class="text-secondary">Calle Caamaño 76, 47013 Valladolid</span>
				</div>
				<div class="d-flex align-items-center gap-2">
					<svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="text-primary">
						<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
					</svg>
					<span class="text-secondary">Latitud: 41° 37' 54,400" N</span>
				</div>
				<div class="d-flex align-items-center gap-2">
					<svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="text-primary">
						<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
					</svg>
					<span class="text-secondary">Longitud: -4° 43' 29,217" O</span>
				</div>
			</div>
		</section>
		</div>
	</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>