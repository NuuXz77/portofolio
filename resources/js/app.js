import './bootstrap';

import AOS from 'aos';
import ApexCharts from 'apexcharts';
import { createIcons, icons } from 'lucide';
import Quill from 'quill';
import Swiper from 'swiper';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';

import 'aos/dist/aos.css';
import 'quill/dist/quill.snow.css';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

let iconRenderQueued = false;
let iconMutationObserver = null;
let iconObserverRoot = null;
let typingRoleTimeoutId = null;
let visitorHeartbeatIntervalId = null;
let liveUsersPollIntervalId = null;
let liveUsersCounterAnimationId = null;
let liveUsersChart = null;
let liveUsersRoot = null;
let skillProgressObserver = null;
let appToastLivewireBound = false;
let dropdownActionsGlobalBound = false;

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
	if (!document.body) {
		return;
	}

	if (iconMutationObserver && iconObserverRoot === document.body) {
		return;
	}

	if (iconMutationObserver) {
		iconMutationObserver.disconnect();
		iconMutationObserver = null;
		iconObserverRoot = null;
	}

	iconMutationObserver = new MutationObserver((mutations) => {
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

	iconMutationObserver.observe(document.body, {
		childList: true,
		subtree: true,
	});

	iconObserverRoot = document.body;
};

const refreshIconRuntime = () => {
	attachIconObserver();
	queueIconRender();

	window.setTimeout(() => {
		queueIconRender();
	}, 0);
};

const scrollToCurrentHashTarget = () => {
	const hash = window.location.hash;

	if (!hash || hash === '#') {
		return;
	}

	const targetId = window.decodeURIComponent(hash.slice(1));

	if (!targetId) {
		return;
	}

	const target = document.getElementById(targetId);

	if (!(target instanceof HTMLElement)) {
		return;
	}

	const topOffset = 110;
	const top = Math.max(target.getBoundingClientRect().top + window.scrollY - topOffset, 0);

	window.scrollTo({
		top,
		behavior: 'smooth',
	});
};

const syncHashScroll = () => {
	window.requestAnimationFrame(() => {
		scrollToCurrentHashTarget();

		window.setTimeout(() => {
			scrollToCurrentHashTarget();
		}, 80);
	});
};

const closeDropdownAction = (dropdown) => {
	if (!(dropdown instanceof HTMLElement)) {
		return;
	}

	dropdown.classList.remove('dropdown-open');

	const trigger = dropdown.querySelector('[data-dropdown-trigger]');

	if (trigger instanceof HTMLElement) {
		trigger.setAttribute('aria-expanded', 'false');
	}
};

const closeAllDropdownActions = () => {
	document.querySelectorAll('[data-dropdown-action].dropdown-open').forEach((dropdown) => {
		closeDropdownAction(dropdown);
	});
};

const initDropdownActions = () => {
	if (dropdownActionsGlobalBound) {
		return;
	}

	document.addEventListener('click', (event) => {
		if (!(event.target instanceof Element)) {
			return;
		}

		const trigger = event.target.closest('[data-dropdown-trigger]');

		if (trigger instanceof HTMLElement) {
			const dropdown = trigger.closest('[data-dropdown-action]');

			if (!(dropdown instanceof HTMLElement)) {
				return;
			}

			event.preventDefault();
			event.stopPropagation();

			const isOpen = dropdown.classList.contains('dropdown-open');
			closeAllDropdownActions();

			if (!isOpen) {
				dropdown.classList.add('dropdown-open');
				trigger.setAttribute('aria-expanded', 'true');
			}

			return;
		}

		const insideDropdown = event.target.closest('[data-dropdown-action]');

		if (insideDropdown instanceof HTMLElement) {
			const menuItem = event.target.closest('[data-dropdown-menu] a, [data-dropdown-menu] button');

			if (menuItem instanceof HTMLElement) {
				window.setTimeout(() => {
					closeDropdownAction(insideDropdown);
				}, 0);
			}

			return;
		}

		closeAllDropdownActions();
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeAllDropdownActions();
		}
	});

	document.addEventListener('livewire:navigating', closeAllDropdownActions);
	document.addEventListener('livewire:navigated', closeAllDropdownActions);

	dropdownActionsGlobalBound = true;
};

const ensureToastStack = () => {
	let stack = document.getElementById('app-toast-stack');

	if (stack) {
		return stack;
	}

	stack = document.createElement('div');
	stack.id = 'app-toast-stack';
	stack.className = 'app-toast-stack';
	stack.setAttribute('aria-live', 'polite');
	stack.setAttribute('aria-atomic', 'true');
	document.body.appendChild(stack);

	return stack;
};

const normalizeToastType = (value) => {
	const type = typeof value === 'string' ? value.toLowerCase().trim() : '';

	if (['success', 'error', 'warning', 'info'].includes(type)) {
		return type;
	}

	return 'info';
};

const createToastSignature = (type, message) => {
	const resolvedType = normalizeToastType(type);
	const normalizedMessage = typeof message === 'string'
		? message.trim().replace(/\s+/g, ' ').toLowerCase()
		: '';

	if (!normalizedMessage) {
		return '';
	}

	return `${resolvedType}|${normalizedMessage}`;
};

const hasVisibleToastWithSignature = (signature) => {
	if (!signature) {
		return false;
	}

	const stack = document.getElementById('app-toast-stack');

	if (!(stack instanceof HTMLElement)) {
		return false;
	}

	const toasts = stack.querySelectorAll('[data-app-toast]');

	for (const toast of toasts) {
		if (!(toast instanceof HTMLElement)) {
			continue;
		}

		if (toast.dataset.leaving === '1') {
			continue;
		}

		if ((toast.dataset.toastSignature || '') === signature) {
			return true;
		}
	}

	return false;
};

const removeToastElement = (toastElement) => {
	if (!(toastElement instanceof HTMLElement) || toastElement.dataset.leaving === '1') {
		return;
	}

	toastElement.dataset.leaving = '1';
	toastElement.classList.add('is-leaving');

	window.setTimeout(() => {
		toastElement.remove();
	}, 260);
};

const mountToastElement = (toastElement) => {
	if (!(toastElement instanceof HTMLElement) || toastElement.dataset.boundToast === '1') {
		return;
	}

	const closeButton = toastElement.querySelector('[data-toast-close]');

	if (closeButton instanceof HTMLElement) {
		closeButton.addEventListener('click', () => {
			removeToastElement(toastElement);
		});
	}

	toastElement.dataset.boundToast = '1';

	window.requestAnimationFrame(() => {
		toastElement.classList.add('is-visible');
	});

	window.setTimeout(() => {
		removeToastElement(toastElement);
	}, 4200);
};

const buildToastElement = ({ type = 'info', message = '' } = {}) => {
	const resolvedType = normalizeToastType(type);
	const safeMessage = typeof message === 'string' ? message.trim() : '';

	if (!safeMessage) {
		return null;
	}

	const iconByType = {
		success: 'check-circle-2',
		error: 'circle-alert',
		warning: 'triangle-alert',
		info: 'info',
	};

	const titleByType = {
		success: 'Success',
		error: 'Error',
		warning: 'Warning',
		info: 'Info',
	};

	const toast = document.createElement('div');
	const signature = createToastSignature(resolvedType, safeMessage);
	toast.className = `app-toast app-toast--${resolvedType}`;
	toast.setAttribute('data-app-toast', '');
	toast.dataset.toastSignature = signature;
	toast.setAttribute('role', 'status');
	toast.setAttribute('aria-live', 'polite');
	toast.setAttribute('aria-atomic', 'true');
	toast.innerHTML = `
		<i data-lucide="${iconByType[resolvedType]}" class="app-toast-icon h-5 w-5"></i>
		<div class="app-toast-content">
			<p class="app-toast-title">${titleByType[resolvedType]}</p>
			<p class="app-toast-message"></p>
		</div>
		<button type="button" class="app-toast-close" data-toast-close aria-label="Dismiss notification">
			<i data-lucide="x" class="h-4 w-4"></i>
		</button>
	`;

	const messageElement = toast.querySelector('.app-toast-message');

	if (messageElement instanceof HTMLElement) {
		messageElement.textContent = safeMessage;
	}

	return toast;
};

const showAppToast = (payload = {}) => {
	const signature = createToastSignature(payload.type, payload.message);

	if (hasVisibleToastWithSignature(signature)) {
		return;
	}

	const toastElement = buildToastElement(payload);

	if (!toastElement) {
		return;
	}

	const stack = ensureToastStack();
	stack.prepend(toastElement);
	mountToastElement(toastElement);
	queueIconRender();
};

const bindLivewireToast = () => {
	if (appToastLivewireBound || !window.Livewire || typeof window.Livewire.on !== 'function') {
		return;
	}

	window.Livewire.on('app-toast', (payload) => {
		showAppToast(payload || {});
	});

	appToastLivewireBound = true;
};

const initAppToasts = () => {
	const serverToasts = document.querySelectorAll('[data-app-toast]');

	if (serverToasts.length) {
		const stack = ensureToastStack();

		serverToasts.forEach((toastElement) => {
			if (toastElement instanceof HTMLElement && !toastElement.dataset.toastSignature) {
				const message = toastElement.querySelector('.app-toast-message')?.textContent || '';
				const type = Array.from(toastElement.classList).find((className) => className.startsWith('app-toast--'))?.replace('app-toast--', '') || 'info';
				toastElement.dataset.toastSignature = createToastSignature(type, message);
			}

			if (!stack.contains(toastElement)) {
				stack.appendChild(toastElement);
			}

			mountToastElement(toastElement);
		});

		queueIconRender();
	}

	if (!window.__appToastWindowBound) {
		window.addEventListener('app-toast', (event) => {
			showAppToast(event.detail || {});
		});

		window.__appToastWindowBound = true;
	}

	bindLivewireToast();
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

		if (toggle.dataset.boundTheme === '1') {
			return;
		}

		toggle.addEventListener('change', (event) => {
			const nextTheme = event.target.checked ? lightTheme : darkTheme;
			applyTheme(nextTheme, true);
		});

		toggle.dataset.boundTheme = '1';
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
		if (menuToggleButton.dataset.boundMenu !== '1') {
			menuToggleButton.addEventListener('click', () => {
				const isOpen = menuPanel.classList.toggle('is-open');
				menuToggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
			});

			menuToggleButton.dataset.boundMenu = '1';
		}

		mobileMenuLinks.forEach((link) => {
			if (link.dataset.boundClose === '1') {
				return;
			}

			link.addEventListener('click', closeMobileMenu);
			link.dataset.boundClose = '1';
		});

		if (!window.__portfolioResizeBound) {
			window.addEventListener('resize', () => {
				if (window.innerWidth >= 1024) {
					const menuButton = document.getElementById('mobile-menu-toggle');
					const menuContainer = document.getElementById('mobile-menu-panel');

					if (menuButton && menuContainer) {
						menuContainer.classList.remove('is-open');
						menuButton.setAttribute('aria-expanded', 'false');
					}
				}
			});

			window.__portfolioResizeBound = true;
		}
	}

	const handleScrollEffects = () => {
		const scrollProgress = document.getElementById('scroll-progress');
		const backToTopButton = document.getElementById('back-to-top');
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

	if (!window.__portfolioScrollBound) {
		window.addEventListener('scroll', handleScrollEffects, { passive: true });
		window.__portfolioScrollBound = true;
	}

	const backToTopButton = document.getElementById('back-to-top');

	if (backToTopButton) {
		if (backToTopButton.dataset.boundBackTop !== '1') {
			backToTopButton.addEventListener('click', () => {
				window.scrollTo({ top: 0, behavior: 'smooth' });
			});

			backToTopButton.dataset.boundBackTop = '1';
		}
	}

	const roleElement = document.getElementById('typing-role');

	if (typingRoleTimeoutId) {
		window.clearTimeout(typingRoleTimeoutId);
		typingRoleTimeoutId = null;
	}

	if (roleElement) {
		let roleIndex = 0;
		let characterIndex = 0;
		let isDeleting = false;
		let typingDelay = 120;

		let roles = [];
		let rolesRaw = [];

		try {
			rolesRaw = JSON.parse(roleElement.dataset.roles || '[]');
		} catch {
			rolesRaw = roleElement.dataset.roles || '';
		}

		if (Array.isArray(rolesRaw)) {
			roles = rolesRaw
				.map((role) => String(role || '').trim())
				.filter(Boolean);
		} else if (typeof rolesRaw === 'string') {
			roles = rolesRaw
				.split(/[\n,|]+/)
				.map((role) => role.trim())
				.filter(Boolean);
		}

		if (!roles.length) {
			roles = ['Web Developer', 'DevOps Engineer'];
		}

		roleElement.textContent = '';

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

			typingRoleTimeoutId = window.setTimeout(typeRole, typingDelay);
		};

		typeRole();
	}

	if (skillProgressObserver) {
		skillProgressObserver.disconnect();
		skillProgressObserver = null;
	}

	const skillProgressBars = document.querySelectorAll('[data-skill-progress]');

	if (skillProgressBars.length) {
		skillProgressObserver = new IntersectionObserver(
			(entries, observer) => {
				entries.forEach((entry) => {
					if (!entry.isIntersecting) {
						return;
					}

					const target = entry.target;

					if (!(target instanceof HTMLElement)) {
						observer.unobserve(target);

						return;
					}

					const value = Number.parseInt(target.dataset.skillLevel || '0', 10);
					const nextWidth = Math.max(0, Math.min(100, value));

					window.requestAnimationFrame(() => {
						target.style.width = `${nextWidth}%`;
					});

					observer.unobserve(target);
				});
			},
			{ threshold: 0.3, rootMargin: '0px 0px -8% 0px' }
		);

		skillProgressBars.forEach((bar) => {
			if (!(bar instanceof HTMLElement)) {
				return;
			}

			bar.style.width = '0%';
			skillProgressObserver.observe(bar);
		});
	}

	const canUsePointerGlow = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

	if (canUsePointerGlow) {
		const skillCards = document.querySelectorAll('.skill-category-card, .premium-hover-card');

		skillCards.forEach((card) => {
			if (!(card instanceof HTMLElement) || card.dataset.boundSpotlight === '1') {
				return;
			}

			let frameId = null;

			const updateSpotlightPosition = (event) => {
				const bounds = card.getBoundingClientRect();
				const x = event.clientX - bounds.left;
				const y = event.clientY - bounds.top;

				if (frameId !== null) {
					return;
				}

				frameId = window.requestAnimationFrame(() => {
					card.style.setProperty('--mouse-x', `${x}px`);
					card.style.setProperty('--mouse-y', `${y}px`);
					frameId = null;
				});
			};

			card.addEventListener('pointerenter', () => {
				card.style.setProperty('--spotlight-opacity', '1');
			});

			card.addEventListener('pointermove', updateSpotlightPosition);

			card.addEventListener('pointerleave', () => {
				card.style.setProperty('--spotlight-opacity', '0');
			});

			card.dataset.boundSpotlight = '1';
		});
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

	const featuredSwiperElement = document.querySelector('.featured-swiper');

	if (featuredSwiperElement?.swiper) {
		featuredSwiperElement.swiper.destroy(true, true);
	}

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

	const testimonialSwiperElement = document.querySelector('.testimonial-swiper');

	if (testimonialSwiperElement?.swiper) {
		testimonialSwiperElement.swiper.destroy(true, true);
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

	if (contactForm && submitButton && submitLabel && submitLoading && contactForm.dataset.boundSubmit !== '1') {
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

		contactForm.dataset.boundSubmit = '1';
	}
};

const initJournalEditors = () => {
	const wrappers = document.querySelectorAll('[data-journal-editor]');

	if (!wrappers.length) {
		return;
	}

	wrappers.forEach((wrapper) => {
		if (!(wrapper instanceof HTMLElement) || wrapper.dataset.initialized === '1') {
			return;
		}

		const editor = wrapper.querySelector('[data-journal-editor-area]');
		const modelInputId = wrapper.getAttribute('data-model-input');
		const modelInput = modelInputId ? document.getElementById(modelInputId) : null;

		if (!(editor instanceof HTMLElement) || !(modelInput instanceof HTMLTextAreaElement)) {
			return;
		}

		const quill = new Quill(editor, {
			theme: 'snow',
			modules: {
				toolbar: [
					[{ header: [2, 3, false] }],
					['bold', 'italic', 'underline', 'strike'],
					[{ list: 'ordered' }, { list: 'bullet' }],
					['blockquote', 'code-block'],
					['link', 'image'],
					['clean'],
				],
			},
		});

		const initialContent = (modelInput.value || '').trim();

		if (initialContent !== '') {
			quill.root.innerHTML = initialContent;
		}

		quill.on('text-change', () => {
			modelInput.value = quill.root.innerHTML;
			modelInput.dispatchEvent(new Event('input', { bubbles: true }));
			modelInput.dispatchEvent(new Event('change', { bubbles: true }));
		});

		wrapper.dataset.initialized = '1';
	});
};

const destroyLiveUsersChart = () => {
	if (liveUsersPollIntervalId) {
		window.clearInterval(liveUsersPollIntervalId);
		liveUsersPollIntervalId = null;
	}

	if (liveUsersCounterAnimationId) {
		window.cancelAnimationFrame(liveUsersCounterAnimationId);
		liveUsersCounterAnimationId = null;
	}

	if (liveUsersChart) {
		liveUsersChart.destroy();
		liveUsersChart = null;
	}

	liveUsersRoot = null;
};

const formatInteger = (value) => {
	const safe = Number.isFinite(value) ? Math.max(0, Math.round(value)) : 0;

	return new Intl.NumberFormat().format(safe);
};

const animateCounterValue = (element, nextValue) => {
	if (!(element instanceof HTMLElement)) {
		return;
	}

	const to = Number.isFinite(nextValue) ? Math.max(0, Math.round(nextValue)) : 0;
	const from = Number.parseInt(element.dataset.value || '0', 10) || 0;

	if (from === to) {
		element.textContent = formatInteger(to);
		element.dataset.value = String(to);

		return;
	}

	if (liveUsersCounterAnimationId) {
		window.cancelAnimationFrame(liveUsersCounterAnimationId);
	}

	const duration = 300;
	const startedAt = performance.now();

	const run = (now) => {
		const progress = Math.min((now - startedAt) / duration, 1);
		const value = Math.round(from + (to - from) * progress);

		element.textContent = formatInteger(value);

		if (progress < 1) {
			liveUsersCounterAnimationId = window.requestAnimationFrame(run);
		} else {
			element.dataset.value = String(to);
			liveUsersCounterAnimationId = null;
		}
	};

	liveUsersCounterAnimationId = window.requestAnimationFrame(run);
};

const initVisitorHeartbeat = () => {
	const pageRoot = document.querySelector('[data-portfolio-root]');

	if (!pageRoot || window.location.pathname.startsWith('/admin')) {
		return;
	}

	if (visitorHeartbeatIntervalId) {
		return;
	}

	const generateUuid = () => (typeof crypto.randomUUID === 'function'
		? crypto.randomUUID()
		: 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (char) => {
			const random = Math.floor(Math.random() * 16);
			const value = char === 'x' ? random : (random & 0x3) | 0x8;

			return value.toString(16);
		}));

	const storageKey = 'portfolio_visitor_session_id';
	let sessionId = '';

	try {
		sessionId = localStorage.getItem(storageKey) || '';

		if (!sessionId) {
			sessionId = generateUuid();

			localStorage.setItem(storageKey, sessionId);
		}
	} catch {
		sessionId = generateUuid();
	}

	const sendHeartbeat = async () => {
		try {
			await fetch('/api/heartbeat', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Accept: 'application/json',
				},
				body: JSON.stringify({
					session_id: sessionId,
				}),
				keepalive: true,
			});
		} catch {
			// Silent fail to avoid interrupting UX.
		}
	};

	sendHeartbeat();
	visitorHeartbeatIntervalId = window.setInterval(sendHeartbeat, 5000);
};

const initAdminLiveUsersChart = () => {
	const root = document.querySelector('[data-admin-live-users]');

	if (!(root instanceof HTMLElement)) {
		destroyLiveUsersChart();

		return;
	}

	if (liveUsersRoot === root && liveUsersChart) {
		return;
	}

	destroyLiveUsersChart();
	liveUsersRoot = root;

	const chartElement = root.querySelector('[data-live-chart]');
	const chartWrapper = root.querySelector('[data-live-chart-wrapper]');
	const skeleton = root.querySelector('[data-live-skeleton]');
	const status = root.querySelector('[data-live-status]');
	const currentCounter = root.querySelector('[data-live-current]');
	const statCurrent = root.querySelector('[data-live-stat-current]');
	const statPeak = root.querySelector('[data-live-stat-peak]');
	const statAvg = root.querySelector('[data-live-stat-avg]');

	if (!(chartElement instanceof HTMLElement)) {
		return;
	}

	const endpoint = root.dataset.endpoint || '/api/active-users';
	const maxPoints = Number.parseInt(root.dataset.window || '60', 10) || 60;
	const isLightTheme = document.documentElement.getAttribute('data-theme') === 'light';
	const lineColor = isLightTheme ? '#0369a1' : '#00c8ff';
	const markerStrokeColor = isLightTheme ? '#ecfeff' : '#082f49';
	const labelColor = isLightTheme ? 'rgba(51, 65, 85, 0.86)' : 'rgba(148, 163, 184, 0.9)';
	const gridColor = isLightTheme ? 'rgba(71, 85, 105, 0.18)' : 'rgba(148, 163, 184, 0.24)';
	const tooltipTheme = isLightTheme ? 'light' : 'dark';
	let points = [];
	let hasRenderedFirstResult = false;

	liveUsersChart = new ApexCharts(chartElement, {
		chart: {
			type: 'line',
			height: 280,
			background: 'transparent',
			toolbar: { show: false },
			zoom: { enabled: false },
			dropShadow: {
				enabled: true,
				color: lineColor,
				top: 8,
				left: 0,
				blur: 16,
				opacity: isLightTheme ? 0.34 : 0.62,
			},
			animations: {
				enabled: true,
				easing: 'linear',
				dynamicAnimation: {
					speed: 360,
				},
			},
		},
		series: [{ name: 'Active users', data: [] }],
		stroke: {
			curve: 'smooth',
			lineCap: 'round',
			width: 6,
		},
		colors: [lineColor],
		markers: {
			size: 4.5,
			colors: [lineColor],
			strokeWidth: 2.4,
			strokeColors: markerStrokeColor,
			hover: {
				size: 10.5,
				sizeOffset: 2,
			},
		},
		fill: {
			type: 'gradient',
			gradient: {
				shadeIntensity: 1,
				opacityFrom: isLightTheme ? 0.2 : 0.24,
				opacityTo: isLightTheme ? 0.02 : 0.01,
				stops: [0, 90, 100],
			},
		},
		dataLabels: { enabled: false },
		grid: {
			borderColor: gridColor,
			strokeDashArray: 3,
			padding: {
				left: 8,
				right: 12,
				top: 8,
				bottom: 10,
			},
		},
		xaxis: {
			type: 'datetime',
			crosshairs: {
				show: true,
				stroke: {
					color: lineColor,
					width: 1.4,
					dashArray: 4,
				},
			},
			labels: {
				datetimeUTC: false,
				style: { colors: labelColor },
			},
			axisBorder: { show: false },
			axisTicks: { show: false },
		},
		yaxis: {
			min: 0,
			forceNiceScale: true,
			tickAmount: 4,
			labels: {
				style: { colors: labelColor },
				formatter: (value) => `${Math.round(value)}`,
			},
		},
		tooltip: {
			theme: tooltipTheme,
			intersect: false,
			shared: false,
			marker: {
				show: true,
			},
			y: {
				formatter: (value) => `${Math.round(value)} active users`,
			},
			x: {
				format: 'HH:mm:ss',
			},
		},
		noData: {
			text: 'Waiting for real-time data...',
		},
	});

	const updateDerivedStats = () => {
		const values = points.map((point) => point.y);

		if (!values.length) {
			return;
		}

		const current = values[values.length - 1];
		const peak = Math.max(...values);
		const average = values.reduce((sum, value) => sum + value, 0) / values.length;

		animateCounterValue(currentCounter, current);

		if (statCurrent instanceof HTMLElement) {
			statCurrent.textContent = formatInteger(current);
		}

		if (statPeak instanceof HTMLElement) {
			statPeak.textContent = formatInteger(peak);
		}

		if (statAvg instanceof HTMLElement) {
			statAvg.textContent = formatInteger(average);
		}
	};

	const fetchLiveUsers = async () => {
		if (!liveUsersChart) {
			return;
		}

		const revealChartArea = () => {
			if (skeleton instanceof HTMLElement) {
				skeleton.classList.add('hidden');
			}

			if (chartWrapper instanceof HTMLElement) {
				chartWrapper.classList.remove('hidden');
			}

			hasRenderedFirstResult = true;
		};

		try {
			const response = await fetch(endpoint, {
				headers: {
					Accept: 'application/json',
				},
				cache: 'no-store',
			});

			if (!response.ok) {
				if (response.status >= 500) {
					throw new Error('Server analytics belum siap. Jalankan migrate lalu reload.');
				}

				throw new Error(`Request failed with status ${response.status}`);
			}

			const payload = await response.json();
			const activeUsers = Number.parseInt(payload.active_users, 10) || 0;
			const timestamp = payload.timestamp ? new Date(payload.timestamp).getTime() : Date.now();

			points.push({ x: timestamp, y: activeUsers });

			if (points.length > maxPoints) {
				points = points.slice(-maxPoints);
			}

			await liveUsersChart.updateSeries(
				[{ name: 'Active users', data: points }],
				true
			);

			updateDerivedStats();
			revealChartArea();

			if (status instanceof HTMLElement) {
				status.textContent = 'Updated every 1 second. Active window: last 10 seconds.';
			}
		} catch (error) {
			if (!hasRenderedFirstResult) {
				revealChartArea();
			}

			if (status instanceof HTMLElement) {
				status.textContent = error instanceof Error
					? `${error.message} Retrying...`
					: 'Unable to load realtime data. Retrying...';
			}
		}
	};

	liveUsersChart.render().then(() => {
		fetchLiveUsers();
		liveUsersPollIntervalId = window.setInterval(fetchLiveUsers, 1000);
	});
};

const initApp = () => {
	refreshIconRuntime();
	initDropdownActions();
	initAppToasts();
	initPortfolioPage();
	syncHashScroll();
	initJournalEditors();
	initVisitorHeartbeat();
	initAdminLiveUsersChart();
};

document.addEventListener('DOMContentLoaded', initApp);
document.addEventListener('livewire:navigated', () => {
	refreshIconRuntime();
	initDropdownActions();
	initAppToasts();
	initPortfolioPage();
	syncHashScroll();
	initJournalEditors();
	initVisitorHeartbeat();
	initAdminLiveUsersChart();
});
document.addEventListener('livewire:init', () => {
	refreshIconRuntime();
	initDropdownActions();
	initAppToasts();
	initPortfolioPage();
	syncHashScroll();
	initJournalEditors();
	initVisitorHeartbeat();
	initAdminLiveUsersChart();
});
