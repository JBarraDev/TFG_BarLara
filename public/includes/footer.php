<footer class="footer-elegante">
	<div class="container py-5">
		<div class="row g-4">
			<!-- Logo y descripción -->
			<div class="col-lg-4 col-md-6">
				<div class="mb-4">
					<img src="<?php echo e($basePath ?? ''); ?>assets/img/logo/LogoLara_200px_bl.svg" 
						 alt="Café Bar Lara" 
						 class="img-fluid mb-3 footer-logo">
					<p class="mb-0 text-white-50">
						Desayunos, tapas, vermús, copas... Y todo el fútbol. Tu bar de barrio de toda la vida en Valladolid.
					</p>
				</div>
			</div>
			
			<!-- Enlaces rápidos -->
			<div class="col-lg-2 col-md-6">
				<h5 class="text-white mb-3 fw-semibold">Enlaces</h5>
				<ul class="list-unstyled">
					<li class="mb-2">
						<a href="<?php echo e($basePath ?? ''); ?>index.php" 
						   class="text-white-50 text-decoration-none hover-white">
							Inicio
						</a>
					</li>
					<li class="mb-2">
						<a href="<?php echo e($basePath ?? ''); ?>carta.php" 
						   class="text-white-50 text-decoration-none hover-white">
							Carta
						</a>
					</li>
					<li class="mb-2">
						<a href="<?php echo e($basePath ?? ''); ?>contacto.php" 
						   class="text-white-50 text-decoration-none hover-white">
							Contacto
						</a>
					</li>
					<li class="mb-2">
						<a href="<?php echo e($basePath ?? ''); ?>reservas.php" 
						   class="text-white-50 text-decoration-none hover-white">
							Reservas
						</a>
					</li>
				</ul>
			</div>
			
			<!-- Contacto -->
			<div class="col-lg-3 col-md-6">
				<h5 class="text-white mb-3 fw-semibold">Contacto</h5>
				<ul class="list-unstyled">
					<li class="mb-2 d-flex align-items-center gap-2">
						<svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" class="text-white-50">
							<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
						</svg>
						<span class="text-white-50">Calle Caamaño 76, 47013 Valladolid</span>
					</li>
					<li class="mb-2 d-flex align-items-center gap-2">
						<svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16" class="text-white-50">
							<path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
						</svg>
						<a href="mailto:info@cafebarlara.es" class="text-white-50 text-decoration-none hover-white">
							info@cafebarlara.es
						</a>
					</li>
				</ul>
			</div>
			
			<!-- Redes sociales -->
			<div class="col-lg-3 col-md-6">
				<h5 class="text-white mb-3 fw-semibold">Síguenos</h5>
				<div class="d-flex gap-3 mb-4">
					<a href="https://www.instagram.com/cafebarlara.valladolid/" 
					   target="_blank" 
					   rel="noopener noreferrer"
					   class="social-icon text-white text-decoration-none"
					   aria-label="Instagram">
						<svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
							<path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
						</svg>
					</a>
					<a href="#" 
					   target="_blank" 
					   rel="noopener noreferrer"
					   class="social-icon text-white text-decoration-none"
					   aria-label="YouTube">
						<svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
							<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
						</svg>
					</a>
					<a href="#" 
					   target="_blank" 
					   rel="noopener noreferrer"
					   class="social-icon text-white text-decoration-none"
					   aria-label="X (Twitter)">
						<svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
							<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
						</svg>
					</a>
					<a href="mailto:info@cafebarlara.es" 
					   class="social-icon text-white text-decoration-none"
					   aria-label="Email">
						<svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
							<path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
						</svg>
					</a>
				</div>
			</div>
		</div>
		
		<!-- Patrocinadores -->
		<div class="row mt-4 pt-4 border-top border-white border-opacity-25">
			<div class="col-12">
				<p class="text-white-50 text-center mb-3 small">Patrocinado por</p>
				<div class="d-flex justify-content-center align-items-center gap-4 flex-wrap">
					<img src="<?php echo e($basePath ?? ''); ?>assets/img/logo/LogoAguila.png" 
						 alt="Patrocinador Águila" 
						 class="img-fluid footer-sponsor-logo">
					<img src="<?php echo e($basePath ?? ''); ?>assets/img/logo/LogoCandelas.png" 
						 alt="Patrocinador Candelas" 
						 class="img-fluid footer-sponsor-logo">
				</div>
			</div>
		</div>
	</div>
	
	<!-- Copyright -->
	<div class="border-top border-white border-opacity-25 py-3">
		<div class="container">
			<div class="row">
				<div class="col-12 text-center">
					<p class="mb-0 text-white-50 small">
						© <?php echo date('Y'); ?> Café Bar Lara. Todos los derechos reservados. | 
						Desarrollado por <span class="text-white">Javier Barra</span>
					</p>
				</div>
			</div>
		</div>
	</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="<?php echo e($basePath ?? ''); ?>assets/js/app.js"></script>
</body>
</html>



