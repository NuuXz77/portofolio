@php
    $items = [
        ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
        ['route' => 'admin.navbar', 'label' => 'Navbar Management', 'icon' => 'menu'],
        ['route' => 'admin.hero', 'label' => 'Hero Section', 'icon' => 'zap'],
        ['route' => 'admin.about', 'label' => 'About Section', 'icon' => 'user'],
        ['route' => 'admin.skills', 'label' => 'Skills Management', 'icon' => 'cpu'],
        ['route' => 'admin.projects', 'label' => 'Projects Management', 'icon' => 'folder'],
        ['route' => 'admin.experiences', 'label' => 'Experience Management', 'icon' => 'clock'],
        ['route' => 'admin.services', 'label' => 'Services Management', 'icon' => 'briefcase'],
        ['route' => 'admin.testimonials', 'label' => 'Testimonials', 'icon' => 'message-square'],
        ['route' => 'admin.contact', 'label' => 'Contact Info', 'icon' => 'mail'],
        ['route' => 'admin.footer', 'label' => 'Footer Management', 'icon' => 'layout'],
        ['route' => 'admin.settings', 'label' => 'Settings', 'icon' => 'settings'],
    ];
@endphp

<div class="flex h-full flex-col">
    <a href="{{ route('admin.dashboard') }}" wire:navigate class="sidebar-brand mb-6 inline-flex items-center gap-2 rounded-2xl px-3 py-2 text-lg font-semibold text-white transition">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-info/20 text-info">
            <i data-lucide="code-xml" class="h-4 w-4"></i>
        </span>
        <span class="sidebar-label">Wisnu CMS</span>
    </a>

    <div class="custom-scrollbar flex-1 space-y-1 overflow-y-auto pr-1">
        @foreach ($items as $item)
            @php($active = request()->routeIs($item['route']))
            <a href="{{ route($item['route']) }}" wire:navigate class="sidebar-link group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition {{ $active ? 'bg-info/15 text-info' : 'text-base-content/75 hover:bg-base-200 hover:text-white' }}">
                <span class="inline-flex h-5 w-5 items-center justify-center shrink-0">
                    <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4"></i>
                </span>
                <span class="sidebar-label">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>

    <form action="{{ route('admin.logout') }}" method="POST" class="mt-4 border-t border-base-content/10 pt-4">
        @csrf
        <button type="submit" class="sidebar-logout-btn btn btn-outline btn-error w-full rounded-xl">
            <i data-lucide="log-out" class="h-4 w-4"></i>
            <span class="sidebar-label">Logout</span>
        </button>
    </form>
</div>