/**
 * Garantiza — comportamiento del sitio.
 * Sin dependencias. Se carga con defer, al final del documento.
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* --------------------------------------------------------------
	 * Cabecera: sombra al separarse del borde superior
	 * ----------------------------------------------------------- */
	var header = document.querySelector('[data-header]');

	if (header) {
		var syncHeader = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};
		syncHeader();
		window.addEventListener('scroll', syncHeader, { passive: true });
	}

	/* --------------------------------------------------------------
	 * Menú móvil
	 * ----------------------------------------------------------- */
	var toggle = document.querySelector('[data-nav-toggle]');
	var nav = document.getElementById('gz-nav');

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', String(open));
		});

		// Cerrar al navegar a un ancla.
		nav.addEventListener('click', function (e) {
			if (e.target.closest('a')) {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
			}
		});

		// Cerrar con Escape, para no dejar al teclado atrapado.
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.focus();
			}
		});
	}

	/* --------------------------------------------------------------
	 * Detalle de cada tarjeta de servicio
	 * Independientes entre sí: se pueden abrir varias a la vez.
	 * ----------------------------------------------------------- */
	var toggles = document.querySelectorAll('[data-accordion]');

	Array.prototype.forEach.call(toggles, function (btn) {
		btn.addEventListener('click', function () {
			var card = btn.closest('.gz-service-card');
			if (!card) { return; }

			var willOpen = !card.classList.contains('is-open');
			card.classList.toggle('is-open', willOpen);
			btn.setAttribute('aria-expanded', String(willOpen));
		});
	});

	/* --------------------------------------------------------------
	 * Contadores
	 * Animan una sola vez, al entrar en pantalla.
	 * ----------------------------------------------------------- */
	var counters = document.querySelectorAll('[data-count]');

	function formatCount(value) {
		return '+' + value.toLocaleString('es-PE');
	}

	function animateCount(el) {
		var target = parseInt(el.getAttribute('data-count'), 10) || 0;

		if (reduceMotion) {
			el.textContent = formatCount(target);
			return;
		}

		var duration = 1200;
		var startTime = null;

		function step(ts) {
			if (!startTime) { startTime = ts; }
			var progress = Math.min((ts - startTime) / duration, 1);
			var eased = 1 - Math.pow(1 - progress, 3);
			el.textContent = formatCount(Math.floor(target * eased));
			if (progress < 1) { requestAnimationFrame(step); }
		}

		requestAnimationFrame(step);
	}

	/* --------------------------------------------------------------
	 * Barras de capacidad mensual
	 * Crecen desde 0 al entrar en pantalla, escalonadas.
	 * ----------------------------------------------------------- */
	var bars = document.querySelectorAll('.gz-capacity-bar__fill');

	function growBar(el, index) {
		var target = el.getAttribute('data-width');
		setTimeout(function () {
			el.style.width = target + '%';
		}, reduceMotion ? 0 : index * 120);
	}

	/* --------------------------------------------------------------
	 * Observadores
	 * Si el navegador no soporta IntersectionObserver, todo se muestra
	 * en su estado final: nunca se queda contenido invisible.
	 * ----------------------------------------------------------- */
	var reveals = document.querySelectorAll('.gz-reveal');

	if (!('IntersectionObserver' in window)) {
		Array.prototype.forEach.call(counters, function (el) {
			el.textContent = formatCount(parseInt(el.getAttribute('data-count'), 10) || 0);
		});
		Array.prototype.forEach.call(bars, function (el) {
			el.style.width = el.getAttribute('data-width') + '%';
		});
		Array.prototype.forEach.call(reveals, function (el) {
			el.classList.add('is-visible');
		});
		return;
	}

	function observeOnce(nodes, threshold, onEnter) {
		if (!nodes.length) { return; }

		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) { return; }
				onEnter(entry.target, Array.prototype.indexOf.call(nodes, entry.target));
				observer.unobserve(entry.target);
			});
		}, { threshold: threshold });

		Array.prototype.forEach.call(nodes, function (el) {
			observer.observe(el);
		});
	}

	observeOnce(counters, 0.5, function (el) { animateCount(el); });
	observeOnce(bars, 0.4, growBar);
	observeOnce(reveals, 0.2, function (el) { el.classList.add('is-visible'); });

	/* --------------------------------------------------------------
	 * Vista previa del formulario
	 * Todavía no hay Fluent Forms conectado: se avisa en lugar de
	 * recargar la página sin hacer nada.
	 * ----------------------------------------------------------- */
	var preview = document.querySelector('[data-form-preview]');

	if (preview) {
		preview.addEventListener('submit', function (e) {
			e.preventDefault();

			var note = preview.querySelector('.gz-form-note');
			if (note) {
				note.textContent = 'Vista previa del formulario: el envío se habilita al conectar Fluent Forms.';
			}
		});
	}

})();
