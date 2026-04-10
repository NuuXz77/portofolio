<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="simple-icons-cdn" content="{{ $simpleIconsCdn }}">

        <title>{{ $title ?? 'Admin Dashboard' }}</title>

        <script>
            window.__appCdn = Object.assign({}, window.__appCdn || {}, {
                simpleIcons: @js($simpleIconsCdn),
            });
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="premium-bg-root premium-bg-admin min-h-screen bg-base-200 text-base-content">
        <div aria-hidden="true" class="premium-background pointer-events-none">
            <div class="premium-grid"></div>
            <div class="premium-glow premium-glow-top-left"></div>
            <div class="premium-glow premium-glow-bottom-right"></div>
            <div class="premium-glow premium-glow-center"></div>
            <div class="premium-noise"></div>
            <div class="premium-vignette"></div>
        </div>

        <div id="admin-shell" class="relative z-10 min-h-screen">
            <div id="admin-sidebar-backdrop" class="fixed inset-0 z-30 bg-black/55 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden"></div>

            <aside id="admin-sidebar" class="glass-surface fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-base-content/10 bg-base-100 px-4 py-5 shadow-xl transition-all duration-300 lg:translate-x-0">
                @include('components.partials.sidebar')
            </aside>

            <div id="admin-main" class="min-h-screen transition-all duration-300 lg:pl-72">
                @include('components.partials.navbar', ['title' => $title ?? 'Admin'])

                <main class="p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @include('components.partials.toast')

        <script>
            (function () {
                var root = document.documentElement;
                var storageKey = 'admin-theme';
                var desktopMedia = window.matchMedia('(min-width: 1024px)');

                var initAdminLayout = function () {
                    var themeToggle = document.getElementById('admin-theme-toggle');
                    var sidebar = document.getElementById('admin-sidebar');
                    var sidebarToggle = document.getElementById('sidebar-toggle');
                    var shell = document.getElementById('admin-shell');
                    var backdrop = document.getElementById('admin-sidebar-backdrop');
                    var sidebarLinks = document.querySelectorAll('.sidebar-link');

                    if (!shell || !sidebar) {
                        return;
                    }

                    var applyDesktopSidebarState = function (collapsed) {
                        shell.classList.toggle('sidebar-collapsed', collapsed);
                    };

                    var closeMobileSidebar = function () {
                        shell.classList.remove('sidebar-mobile-open');
                        sidebar.classList.add('-translate-x-full');
                        sidebar.classList.remove('translate-x-0');

                        if (backdrop) {
                            backdrop.classList.add('opacity-0', 'pointer-events-none');
                            backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                        }

                        if (sidebarToggle) {
                            sidebarToggle.setAttribute('aria-expanded', 'false');
                        }
                    };

                    var openMobileSidebar = function () {
                        shell.classList.add('sidebar-mobile-open');
                        sidebar.classList.remove('-translate-x-full');
                        sidebar.classList.add('translate-x-0');

                        if (backdrop) {
                            backdrop.classList.remove('opacity-0', 'pointer-events-none');
                            backdrop.classList.add('opacity-100', 'pointer-events-auto');
                        }

                        if (sidebarToggle) {
                            sidebarToggle.setAttribute('aria-expanded', 'true');
                        }
                    };

                    var syncSidebarWithViewport = function () {
                        if (desktopMedia.matches) {
                            sidebar.classList.remove('-translate-x-full', 'translate-x-0');
                            sidebar.classList.add('translate-x-0');

                            if (backdrop) {
                                backdrop.classList.add('opacity-0', 'pointer-events-none');
                                backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                            }
                        } else {
                            applyDesktopSidebarState(false);
                            closeMobileSidebar();
                        }
                    };

                    var applyTheme = function (theme, persist) {
                        root.setAttribute('data-theme', theme);

                        if (themeToggle) {
                            themeToggle.checked = theme === 'light';
                        }

                        if (persist) {
                            localStorage.setItem(storageKey, theme);
                        }
                    };

                    applyTheme(localStorage.getItem(storageKey) || 'dark', false);

                    if (themeToggle && !themeToggle.dataset.bound) {
                        themeToggle.addEventListener('change', function () {
                            applyTheme(this.checked ? 'light' : 'dark', true);
                        });

                        themeToggle.dataset.bound = '1';
                    }

                    if (sidebarToggle && !sidebarToggle.dataset.bound) {
                        sidebarToggle.addEventListener('click', function () {
                            if (desktopMedia.matches) {
                                applyDesktopSidebarState(!shell.classList.contains('sidebar-collapsed'));
                            } else if (shell.classList.contains('sidebar-mobile-open')) {
                                closeMobileSidebar();
                            } else {
                                openMobileSidebar();
                            }
                        });

                        sidebarToggle.dataset.bound = '1';
                    }

                    if (backdrop && !backdrop.dataset.bound) {
                        backdrop.addEventListener('click', closeMobileSidebar);
                        backdrop.dataset.bound = '1';
                    }

                    sidebarLinks.forEach(function (link) {
                        if (link.dataset.boundClose) {
                            return;
                        }

                        link.addEventListener('click', function () {
                            if (!desktopMedia.matches) {
                                closeMobileSidebar();
                            }
                        });

                        link.dataset.boundClose = '1';
                    });

                    syncSidebarWithViewport();
                };

                if (!window.__adminLayoutBooted) {
                    document.addEventListener('DOMContentLoaded', initAdminLayout);
                    document.addEventListener('livewire:navigated', initAdminLayout);

                    if (desktopMedia.addEventListener) {
                        desktopMedia.addEventListener('change', initAdminLayout);
                    } else {
                        desktopMedia.addListener(initAdminLayout);
                    }

                    window.__adminLayoutBooted = true;
                }

                initAdminLayout();
            })();
        </script>

        @livewireScripts
    </body>
</html>
