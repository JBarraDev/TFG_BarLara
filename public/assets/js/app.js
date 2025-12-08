(function () {
	'use strict';

	// Ejemplo: activar tooltips de Bootstrap si se usan
	var tooltipTriggerList = Array.prototype.slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
	tooltipTriggerList.forEach(function (tooltipTriggerEl) {
		new bootstrap.Tooltip(tooltipTriggerEl);
	});

	// Carrusel infinito del hero
	var heroCarousel = (function() {
		var slides = document.querySelectorAll('.hero-slide');
		var currentSlide = 0;
		var interval = null;

		function nextSlide() {
			if (slides.length === 0) return;
			
			slides[currentSlide].classList.remove('active');
			currentSlide = (currentSlide + 1) % slides.length;
			slides[currentSlide].classList.add('active');
		}

		function startCarousel() {
			if (slides.length === 0) return;
			
			interval = setInterval(nextSlide, 3000);
		}

		function stopCarousel() {
			if (interval) {
				clearInterval(interval);
				interval = null;
			}
		}

		// Iniciar cuando el DOM esté listo
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', startCarousel);
		} else {
			startCarousel();
		}

		// Pausar en hover (opcional)
		var heroSection = document.querySelector('.hero-section');
		if (heroSection) {
			heroSection.addEventListener('mouseenter', stopCarousel);
			heroSection.addEventListener('mouseleave', startCarousel);
		}
	})();

	// Navbar sticky con transparencia al hacer scroll
	var navbarScroll = (function() {
		var navbar = document.querySelector('.navbar-custom');
		if (!navbar) return;

		function handleScroll() {
			var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
			
			if (scrollTop > 30) {
				navbar.classList.add('scrolled');
			} else {
				navbar.classList.remove('scrolled');
			}
		}

		// Detectar scroll
		window.addEventListener('scroll', handleScroll);
		
		// Verificar estado inicial
		handleScroll();
	})();

	// Manejo de acordeones de categorías en carta.php
	var categoriaButtons = document.querySelectorAll('[data-bs-toggle="collapse"][data-bs-target^="#categoria-"]');
	
	categoriaButtons.forEach(function(button) {
		var target = document.querySelector(button.getAttribute('data-bs-target'));
		
		if (target) {
			// Actualizar aria-expanded cuando se abre/cierra
			target.addEventListener('show.bs.collapse', function() {
				button.setAttribute('aria-expanded', 'true');
			});
			
			target.addEventListener('hide.bs.collapse', function() {
				button.setAttribute('aria-expanded', 'false');
			});
		}
	});
	
	// Añadir productos al carrito mediante AJAX al hacer clic en "Pedir"
	var pedirButtons = document.querySelectorAll('.btn-pedir');
	
	pedirButtons.forEach(function(btn) {
		btn.addEventListener('click', function() {
			var itemId = this.getAttribute('data-item-id');
			
			if (!itemId) return;
			
			// Llamada AJAX para añadir al carrito
			fetch('add_to_carrito.php', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
				},
				body: 'item_id=' + itemId + '&cantidad=1'
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					showNotification('Producto añadido al carrito', 'success');
					updateCarritoBadge(data.carrito_count || 0);
				} else {
					// Si no está logueado, redirigir al login
					if (data.message && data.message.includes('sesión')) {
						var pathname = window.location.pathname;
						var pathParts = pathname.split('/').filter(function(p) { return p; });
						var redirectPath = pathParts.length > 0 ? pathParts[pathParts.length - 1] : 'index.php';
						if (window.location.search) {
							redirectPath += window.location.search;
						}
						window.location.href = 'login_cliente.php?redirect=' + encodeURIComponent(redirectPath);
					} else {
						showNotification(data.message || 'Error al añadir producto', 'danger');
					}
				}
			})
			.catch(error => {
				showNotification('Error al añadir producto al carrito', 'danger');
			});
		});
	});
	
	function showNotification(message, type) {
		var alertDiv = document.createElement('div');
		alertDiv.className = 'alert alert-' + type + ' alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
		alertDiv.style.zIndex = '9999';
		alertDiv.setAttribute('role', 'alert');
		alertDiv.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
		document.body.appendChild(alertDiv);
		
		setTimeout(function() {
			alertDiv.remove();
		}, 3000);
	}
	
	function updateCarritoBadge(count) {
		var badge = document.getElementById('carrito-badge');
		if (!badge) {
			var carritoLink = document.querySelector('a[href*="carrito.php"]');
			if (carritoLink) {
				badge = document.createElement('span');
				badge.id = 'carrito-badge';
				badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
				carritoLink.appendChild(badge);
			}
		}
		if (badge) {
			badge.textContent = count;
			badge.style.display = count > 0 ? 'inline' : 'none';
		}
	}
})();
