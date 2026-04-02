<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Page Title' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            #site-navbar {
                pointer-events: none;
            }

            #site-navbar-shell {
                pointer-events: auto;
                max-width: 100%;
                margin: 0 auto;
                border-radius: 0;
                border-bottom: 1px solid color-mix(in srgb, oklch(var(--bc) / 0.22), transparent);
                background:
                    linear-gradient(120deg, oklch(var(--b1) / 0.82), oklch(var(--b2) / 0.66));
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                transition:
                    max-width 0.45s ease,
                    margin-top 0.45s ease,
                    border-radius 0.45s ease,
                    box-shadow 0.45s ease,
                    border 0.45s ease,
                    background 0.45s ease;
            }

            #site-navbar-shell.is-scrolled {
                max-width: min(1120px, calc(100% - 1.75rem));
                margin-top: 0.75rem;
                border-radius: 1rem;
                border: 1px solid color-mix(in srgb, oklch(var(--bc) / 0.2), transparent);
                box-shadow: 0 12px 32px color-mix(in srgb, oklch(var(--bc) / 0.24), transparent);
                background:
                    linear-gradient(120deg, oklch(var(--b1) / 0.74), oklch(var(--b2) / 0.56));
            }

            #site-navbar-shell .menu a {
                transition: all 0.2s ease;
            }

            #site-navbar-shell .menu a:hover {
                transform: translateY(-1px);
            }
        </style>
        @livewireStyles
    </head>
    <body class="min-h-screen bg-base-100">

        <nav id="site-navbar" class="fixed inset-x-0 top-0 z-50 transition-all duration-500 ease-out">
            <div id="site-navbar-shell" data-scrolled="false" class="group navbar relative isolate w-full overflow-hidden px-4 py-3 md:px-8">
                <span aria-hidden="true" class="pointer-events-none absolute inset-0 z-1 rounded-[inherit] p-px opacity-0 transition-opacity duration-300 group-data-[scrolled=true]:opacity-100">
                    <span class="absolute -inset-[130%] rotate-45 scale-110 [animation-play-state:paused] transition-transform duration-500 ease-out group-data-[scrolled=true]:rotate-0 group-data-[scrolled=true]:scale-100 group-data-[scrolled=true]:animate-[spin_4.5s_linear_infinite] group-data-[scrolled=true]:[animation-play-state:running] bg-[conic-gradient(from_0deg,transparent_0deg,oklch(var(--p))_45deg,transparent_95deg,oklch(var(--s))_165deg,transparent_225deg,oklch(var(--a))_295deg,transparent_360deg)]"></span>
                    <span class="absolute inset-px rounded-[calc(var(--radius-2xl)-1px)] bg-base-100/70 dark:bg-base-300/60"></span>
                </span>

                <div class="relative z-10 flex-1">
                    <a href="#home" class="btn btn-ghost text-lg font-semibold tracking-wide normal-case">Portfolio</a>
                </div>

                <div class="relative z-10 flex-none">
                    <label class="swap swap-rotate btn btn-ghost btn-circle" aria-label="Toggle theme">
                        <input id="theme-toggle" type="checkbox" />

                        <svg
                            class="swap-off h-6 w-6 fill-current"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24">
                            <path
                                d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
                        </svg>

                        <svg
                            class="swap-on h-6 w-6 fill-current"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24">
                            <path
                                d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
                        </svg>
                    </label>
                </div>

                <div class="relative z-10 flex-none md:hidden">
                    <div class="dropdown dropdown-end">
                        <button tabindex="0" class="btn btn-ghost" aria-label="Open navigation menu">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <ul tabindex="0" class="menu dropdown-content z-1 mt-3 w-56 rounded-box border border-base-content/10 bg-base-100/80 p-2 shadow-xl backdrop-blur">
                            <li><a href="#home">Home</a></li>
                            <li><a href="#about">About</a></li>
                            <li><a href="#skills">Skills</a></li>
                            <li><a href="#projects">Projects</a></li>
                            <li><a href="#experience">Experience</a></li>
                            <li><a href="#contact">Contact</a></li>
                        </ul>
                    </div>
                </div>

                <div class="relative z-10 hidden flex-none md:block">
                    <ul class="menu menu-horizontal gap-1 px-1">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="#skills">Skills</a></li>
                        <li><a href="#projects">Projects</a></li>
                        <li><a href="#experience">Experience</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container relative z-10 mx-auto py-4">
            {{ $slot }}
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var navbarShell = document.getElementById('site-navbar-shell');
                var themeToggle = document.getElementById('theme-toggle');
                var root = document.documentElement;
                var themeStorageKey = 'theme';
                var lightTheme = 'light';
                var darkTheme = 'dark';

                if (!navbarShell) {
                    return;
                }

                var updateNavbarState = function () {
                    if (window.scrollY > 28) {
                        navbarShell.classList.add('is-scrolled');
                        navbarShell.dataset.scrolled = 'true';
                    } else {
                        navbarShell.classList.remove('is-scrolled');
                        navbarShell.dataset.scrolled = 'false';
                    }
                };

                updateNavbarState();
                window.addEventListener('scroll', updateNavbarState, { passive: true });

                var applyTheme = function (theme, persist) {
                    root.setAttribute('data-theme', theme);

                    if (themeToggle) {
                        themeToggle.checked = theme === darkTheme;
                    }

                    if (persist) {
                        localStorage.setItem(themeStorageKey, theme);
                    }
                };

                var savedTheme = localStorage.getItem(themeStorageKey);
                var initialTheme = savedTheme || root.getAttribute('data-theme') || lightTheme;
                applyTheme(initialTheme, false);

                if (themeToggle) {
                    themeToggle.addEventListener('change', function () {
                        applyTheme(this.checked ? darkTheme : lightTheme, true);
                    });
                }
            });
        </script>
        @livewireScripts
    </body>
</html>
