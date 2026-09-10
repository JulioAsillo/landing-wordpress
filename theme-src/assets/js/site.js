/**
 * Garantiza — comportamiento del sitio.
 * Sin dependencias. Se carga con defer, al final del documento.
 */
(function () {
	'use strict';

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* --------------------------------------------------------------
	 * Menú móvil
	 * ----------------------------------------------------------- */
	var toggle = document.querySelector('[data-nav-toggle]');
	var nav = document.getElementById('gz-nav');

	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = toggle.getAttribute('aria-expanded') === 'true';
			toggle.setAttribute('aria-expanded', String(!open));
			nav.setAttribute('data-open', String(!open));
		});

		// Cerrar al navegar a un ancla.
		nav.addEventListener('click', function (e) {
			if (e.target.tagName === 'A') {
				toggle.setAttribute('aria-expanded', 'false');
				nav.setAttribute('data-open', 'false');
			}
		});
	}

	/* --------------------------------------------------------------
	 * Acordeón de servicios
	 * Un solo panel abierto a la vez.
	 * ----------------------------------------------------------- */
	var heads = document.querySelectorAll('[data-accordion]');

	Array.prototype.forEach.call(heads, function (head) {
		head.addEventListener('click', function () {
			var panel = document.getElementById(head.getAttribute('aria-controls'));
			var open = head.getAttribute('aria-expanded') === 'true';

			Array.prototype.forEach.call(heads, function (other) {
				if (other === head) { return; }
				other.setAttribute('aria-expanded', 'false');
				var p = document.getElementById(other.getAttribute('aria-controls'));
				if (p) { p.hidden = true; }
			});

			head.setAttribute('aria-expanded', String(!open));
			if (panel) { panel.hidden = open; }
		});
	});

	/* --------------------------------------------------------------
	 * Contadores
	 * Solo animan cuando entran en pantalla, una sola vez.
	 * ----------------------------------------------------------- */
	var counters = document.querySelectorAll('[data-counter]');

	function paint(el, value) {
		var suffix = el.getAttribute('data-suffix') || '';
		el.textContent = value.toLocaleString('es-PE') + suffix;
	}

	function run(el) {
		var target = parseInt(el.getAttribute('data-counter'), 10) || 0;

		if (reduceMotion || target === 0) {
			paint(el, target);
			return;
		}

		var duration = 1400;
		var start = null;

		function step(now) {
			if (start === null) { start = now; }
			var progress = Math.min((now - start) / duration, 1);
			// easeOutCubic: arranca rápido y frena al final.
			var eased = 1 - Math.pow(1 - progress, 3);
			paint(el, Math.round(target * eased));
			if (progress < 1) { requestAnimationFrame(step); }
		}

		requestAnimationFrame(step);
	}

	if ('IntersectionObserver' in window) {
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					run(entry.target);
					observer.unobserve(entry.target);
				}
			});
		}, { threshold: 0.4 });

		Array.prototype.forEach.call(counters, function (el) {
			observer.observe(el);
		});
	} else {
		Array.prototype.forEach.call(counters, run);
	}
})();
