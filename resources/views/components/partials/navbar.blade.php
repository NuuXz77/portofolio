<header class="glass-surface sticky top-0 z-30 border-b border-base-content/10 bg-base-100/85 px-4 py-3 backdrop-blur sm:px-6 lg:px-8">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <button id="sidebar-toggle" class="btn btn-ghost btn-sm btn-circle" type="button" aria-label="Toggle sidebar">
                <i data-lucide="menu" class="h-4 w-4"></i>
            </button>
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-base-content/55">Admin Panel</p>
                <h1 class="text-lg font-semibold text-base-content">{{ $title ?? 'Dashboard' }}</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <label class="swap swap-rotate btn btn-ghost btn-circle" aria-label="Toggle theme">
                <input id="admin-theme-toggle" type="checkbox">
                <i data-lucide="sun" class="swap-on h-4 w-4"></i>
                <i data-lucide="moon" class="swap-off h-4 w-4"></i>
            </label>

            <div class="dropdown dropdown-end">
                <button tabindex="0" class="btn btn-ghost rounded-xl">
                    <span class="hidden text-sm sm:inline">{{ auth()->user()->name }}</span>
                    <div class="avatar placeholder">
                        <div class="h-8 w-8 rounded-full bg-info/20 text-info">
                            <span class="text-xs font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                    </div>
                </button>
                <ul tabindex="0" class="glass-surface-soft menu dropdown-content z-20 mt-2 w-52 rounded-box border border-base-content/10 bg-base-100 p-2 shadow-xl">
                    <li>
                        <a href="{{ route('admin.settings') }}" wire:navigate>
                            <i data-lucide="settings" class="h-4 w-4"></i>
                            Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/') }}" target="_blank">
                            <i data-lucide="external-link" class="h-4 w-4"></i>
                            View Landing Page
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>