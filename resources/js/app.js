import './bootstrap';

import AOS from 'aos';
import { createIcons, icons } from 'lucide';
import Swiper from 'swiper';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';

import 'aos/dist/aos.css';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

let iconRenderQueued = false;
let iconObserverAttached = false;

const initGlobalIcons = () => {
	try {
		createIcons({
			icons,
			attrs: {
				'stroke-width': '1.9',
			},
		});
	} catch (error) {
		console.warn('Lucide icon initialization warning:', error);
	}
};

const queueIconRender = () => {
	if (iconRenderQueued) {
		return;
	}

	iconRenderQueued = true;
	window.requestAnimationFrame(() => {
		initGlobalIcons();
		iconRenderQueued = false;
	});
};

const attachIconObserver = () => {
	if (iconObserverAttached || !document.body) {
		return;
	}

	const observer = new MutationObserver((mutations) => {
		for (const mutation of mutations) {
			for (const node of mutation.addedNodes) {
				if (!(node instanceof HTMLElement)) {
					continue;
				}

				if (node.matches('[data-lucide]') || node.querySelector('[data-lucide]')) {
					queueIconRender();
					return;
				}
			}
		}
	});

	observer.observe(document.body, {
		childList: true,
		subtree: true,
	});

	iconObserverAttached = true;
};

const initPortfolioPage = () => {
	const pageRoot = document.querySelector('[data-portfolio-root]');

	if (!pageRoot) {
		return;
	}

	const root = document.documentElement;
	root.classList.add('aos-active');
	const themeStorageKey = 'portfolio-theme';
	const darkTheme = 'dark';
	const lightTheme = 'light';
	const themeToggle = document.getElementById('theme-toggle');
	const themeToggleMobile = document.getElementById('theme-toggle-mobile');

	const applyTheme = (theme, persist = false) => {
		root.setAttribute('data-theme', theme);

		const isDark = theme === darkTheme;

		if (themeToggle) {
			themeToggle.checked = !isDark;
		}

		if (themeToggleMobile) {
			themeToggleMobile.checked = !isDark;
		}

		if (persist) {
			localStorage.setItem(themeStorageKey, theme);
		}
	};

	const savedTheme = localStorage.getItem(themeStorageKey);
	applyTheme(savedTheme || root.getAttribute('data-theme') || darkTheme);

	[themeToggle, themeToggleMobile].forEach((toggle) => {
		if (!toggle) {
			return;
		}

		toggle.addEventListener('change', (event) => {
			const nextTheme = event.target.checked ? lightTheme : darkTheme;
			applyTheme(nextTheme, true);
		});
	});

	const menuToggleButton = document.getElementById('mobile-menu-toggle');
	const menuPanel = document.getElementById('mobile-menu-panel');
	const mobileMenuLinks = document.querySelectorAll('.mobile-nav-link');

	const closeMobileMenu = () => {
		if (!menuToggleButton || !menuPanel) {
			return;
		}

		menuPanel.classList.remove('is-open');
		menuToggleButton.setAttribute('aria-expanded', 'false');
	};

	if (menuToggleButton && menuPanel) {
		menuToggleButton.addEventListener('click', () => {
			const isOpen = menuPanel.classList.toggle('is-open');
			menuToggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});

		mobileMenuLinks.forEach((link) => {
			link.addEventListener('click', closeMobileMenu);
		});

		window.addEventListener('resize', () => {
			if (window.innerWidth >= 1024) {
				closeMobileMenu();
			}
		});
	}

	const scrollProgress = document.getElementById('scroll-progress');
	const backToTopButton = document.getElementById('back-to-top');

	const handleScrollEffects = () => {
		const totalHeight = document.documentElement.scrollHeight - window.innerHeight;
		const progress = totalHeight > 0 ? (window.scrollY / totalHeight) * 100 : 0;

		if (scrollProgress) {
			scrollProgress.style.width = `${Math.min(progress, 100)}%`;
		}

		if (backToTopButton) {
			backToTopButton.classList.toggle('is-visible', window.scrollY > 500);
		}
	};

	handleScrollEffects();
	window.addEventListener('scroll', handleScrollEffects, { passive: true });

	if (backToTopButton) {
		backToTopButton.addEventListener('click', () => {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});
	}

	const roleElement = document.getElementById('typing-role');

	if (roleElement) {
		let roleIndex = 0;
		let characterIndex = 0;
		let isDeleting = false;
		let typingDelay = 120;

		let roles = [];

		try {
			roles = JSON.parse(roleElement.dataset.roles || '[]');
		} catch {
			roles = [];
		}

		const typeRole = () => {
			if (!roles.length) {
				return;
			}

			const current = roles[roleIndex];

			if (isDeleting) {
				characterIndex -= 1;
			} else {
				characterIndex += 1;
			}

			roleElement.textContent = current.slice(0, characterIndex);

			if (!isDeleting && characterIndex === current.length) {
				typingDelay = 1250;
				isDeleting = true;
			} else if (isDeleting && characterIndex === 0) {
				isDeleting = false;
				roleIndex = (roleIndex + 1) % roles.length;
				typingDelay = 260;
			} else {
				typingDelay = isDeleting ? 55 : 100;
			}

			window.setTimeout(typeRole, typingDelay);
		};

		typeRole();
	}

	const filterButtons = document.querySelectorAll('.project-filter');
	const projectCards = document.querySelectorAll('.project-card');

	// Defensive reset in case previous transitions left items hidden.
	projectCards.forEach((card) => card.classList.remove('project-hidden'));

	if (filterButtons.length && projectCards.length) {
		filterButtons.forEach((button) => {
			button.addEventListener('click', () => {
				const filter = button.dataset.filter;

				filterButtons.forEach((item) => {
					item.classList.remove('is-active');
				});

				button.classList.add('is-active');

				projectCards.forEach((card) => {
					const category = card.dataset.category;
					const isMatch = filter === 'all' || category === filter;
					card.classList.toggle('project-hidden', !isMatch);
				});
			});
		});
	}

	const featuredSlidesCount = document.querySelectorAll('.featured-swiper .swiper-wrapper > .swiper-slide').length;
	const testimonialSlidesCount = document.querySelectorAll('.testimonial-swiper .swiper-wrapper > .swiper-slide').length;

	if (featuredSlidesCount > 0) {
		new Swiper('.featured-swiper', {
			modules: [Navigation, Pagination, Autoplay],
			slidesPerView: 1,
			spaceBetween: 14,
			speed: 650,
			autoHeight: true,
			grabCursor: true,
			loop: featuredSlidesCount > 1,
			autoplay: featuredSlidesCount > 1
				? {
					  delay: 5200,
					  disableOnInteraction: false,
				  }
				: false,
			navigation: {
				nextEl: '.featured-next',
				prevEl: '.featured-prev',
			},
			pagination: {
				el: '.featured-pagination',
				clickable: true,
			},
			breakpoints: {
				640: {
					spaceBetween: 16,
				},
				1024: {
					spaceBetween: 20,
				},
			},
		});
	}

	if (testimonialSlidesCount > 0) {
		new Swiper('.testimonial-swiper', {
			modules: [Navigation, Pagination, Autoplay],
			slidesPerView: 1,
			spaceBetween: 20,
			speed: 650,
			loop: testimonialSlidesCount > 2,
			autoplay: testimonialSlidesCount > 1
				? {
					  delay: 5200,
					  disableOnInteraction: false,
				  }
				: false,
			navigation: {
				nextEl: '.testimonial-next',
				prevEl: '.testimonial-prev',
			},
			pagination: {
				el: '.testimonial-pagination',
				clickable: true,
			},
			breakpoints: {
				768: {
					slidesPerView: testimonialSlidesCount > 1 ? 2 : 1,
				},
			},
		});
	}

	AOS.init({
		duration: 800,
		once: true,
		easing: 'ease-out-cubic',
		offset: 30,
	});

	window.setTimeout(() => {
		AOS.refreshHard();
	}, 100);

	const contactForm = document.getElementById('contact-form');
	const submitButton = document.getElementById('contact-submit');
	const submitLabel = submitButton?.querySelector('.submit-label');
	const submitLoading = submitButton?.querySelector('.loading');
	const contactToast = document.getElementById('contact-toast');
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

	if (contactForm && submitButton && submitLabel && submitLoading) {
		contactForm.addEventListener('submit', async (event) => {
			event.preventDefault();

			if (!contactForm.checkValidity()) {
				contactForm.reportValidity();

				return;
			}

			submitButton.setAttribute('disabled', 'true');
			submitLabel.textContent = 'Sending...';
			submitLoading.classList.remove('hidden');

			try {
				const response = await fetch('/contact-message', {
					method: 'POST',
					headers: {
						'X-CSRF-TOKEN': csrfToken,
						Accept: 'application/json',
					},
					body: new FormData(contactForm),
				});

				if (!response.ok) {
					throw new Error('Unable to send message.');
				}

				contactForm.reset();

				if (contactToast) {
					contactToast.classList.remove('hidden');
					window.setTimeout(() => {
						contactToast.classList.add('hidden');
					}, 2800);
				}
			} catch (error) {
				window.alert(error instanceof Error ? error.message : 'Unable to send message.');
			} finally {
				submitButton.removeAttribute('disabled');
				submitLabel.textContent = 'Send Message';
				submitLoading.classList.add('hidden');
			}
		});
	}
};

const initApp = () => {
	initGlobalIcons();
	attachIconObserver();
	initPortfolioPage();
};

document.addEventListener('DOMContentLoaded', initApp);
document.addEventListener('livewire:navigated', queueIconRender);
document.addEventListener('livewire:init', queueIconRender);
