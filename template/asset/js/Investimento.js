document.addEventListener('DOMContentLoaded', function () {
	const navLinks = document.querySelectorAll('.nav-links a');
	const STORAGE_KEY = 'nav-active-index';

	function setActive(index) {
		navLinks.forEach((a, i) => {
			if (i === index) {
				a.classList.add('active');
				a.setAttribute('aria-current', 'page');
			} else {
				a.classList.remove('active');
				a.removeAttribute('aria-current');
			}
		});
	}

	// Restore from localStorage
	const saved = localStorage.getItem(STORAGE_KEY);
	if (saved !== null) {
		const idx = parseInt(saved, 10);
		if (!Number.isNaN(idx) && idx >= 0 && idx < navLinks.length) {
			setActive(idx);
		}
	}

	// Click handlers
	navLinks.forEach((link, idx) => {
		link.addEventListener('click', function (e) {
			// If links are anchors to other pages, let navigation happen.
			// For now they are '#', so prevent default to stay on the page and demonstrate selection.
			if (link.getAttribute('href') === '#') e.preventDefault();
			setActive(idx);
			localStorage.setItem(STORAGE_KEY, String(idx));
		});

		// Allow keyboard Enter/Space to activate
		link.addEventListener('keydown', function (e) {
			if (e.key === 'Enter' || e.key === ' ') {
				e.preventDefault();
				link.click();
			}
		});
	});
});