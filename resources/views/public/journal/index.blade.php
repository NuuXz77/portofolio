@php
    $resolveAsset = function (?string $path, string $fallback = ''): string {
        if (! $path) {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    };

    $logoText = (string) ($logoText ?? 'Wisnu.dev');
    $brandMode = in_array(($brandMode ?? 'text'), ['text', 'logo', 'combo'], true)
        ? (string) $brandMode
        : 'text';
    $brandLogoType = in_array(($brandLogoType ?? 'image'), ['image', 'icon'], true)
        ? (string) $brandLogoType
        : 'image';
    $brandLogoImage = isset($brandLogoImage) ? trim((string) $brandLogoImage) : null;
    $brandLogoIcon = trim((string) ($brandLogoIcon ?? 'sparkles'));
    $navItems = is_array($navItems ?? null) ? $navItems : [];
    $ctaText = (string) ($ctaText ?? 'Hire Me');
    $ctaLink = (string) ($ctaLink ?? route('home').'#contact');
@endphp

<div>
    <x-partials.public-navbar
        :logoText="$logoText"
        :brandMode="$brandMode"
        :brandLogoType="$brandLogoType"
        :brandLogoImage="$brandLogoImage"
        :brandLogoIcon="$brandLogoIcon"
        :brandHref="route('home')"
        :navItems="$navItems"
        :ctaText="$ctaText"
        :ctaLink="$ctaLink"
    />

    <main class="px-4 pb-20 pt-14 sm:px-8 sm:pt-18">
        <section class="mx-auto max-w-7xl">
            <div class="text-center" wire:ignore>
                <p class="text-sm uppercase tracking-[0.24em] text-info">Journal</p>
                <h1 class="mt-3 text-4xl font-semibold text-base-content sm:text-5xl">My Journal</h1>
                <p class="mx-auto mt-4 max-w-2xl text-base text-base-content/70 sm:text-lg">Thoughts, experiences, and daily activities</p>
            </div>

            <div class="portfolio-glass mx-auto mt-10 max-w-4xl rounded-2xl border border-white/10 p-4 shadow-xl sm:p-5">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <p class="text-xs uppercase tracking-[0.22em] text-base-content/60">Live Filter</p>
                    <span wire:loading.delay class="text-xs text-info">Updating results...</span>
                </div>

                <div class="grid gap-3 md:grid-cols-[1fr,230px,auto] md:items-end">
                    <x-ui.input-field
                        name="search"
                        wire:model.live.debounce.350ms="search"
                        placeholder="Search article..."
                        icon="search"
                        inputClass="bg-base-100/60 border-white/15"
                    />

                    <x-ui.select-field
                        name="category"
                        wire:model.live="activeCategory"
                        placeholder="All Category"
                        selectClass="bg-base-100/60 border-white/15"
                    >
                        <option value="">All Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}">
                                {{ $category->name }} ({{ $category->public_articles_count }})
                            </option>
                        @endforeach
                    </x-ui.select-field>

                    <button
                        type="button"
                        wire:click="clearFilters"
                        @disabled($search === '' && $activeCategory === '')
                        class="btn btn-outline rounded-xl md:mb-1"
                    >
                        Reset
                    </button>
                </div>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($articles as $article)
                    <article class="skill-category-card overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-xl backdrop-blur-lg transition duration-300 hover:-translate-y-1 hover:shadow-info/20">
                        <span aria-hidden="true" class="skill-card-spotlight"></span>
                        <a href="{{ route('journal.show', $article->slug) }}" wire:navigate class="block">
                            <img
                                loading="lazy"
                                src="{{ $resolveAsset($article->thumbnail_path, 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80') }}"
                                alt="{{ $article->title }}"
                                class="h-44 w-full object-cover"
                            >
                        </a>

                        <div class="p-5">
                            <div class="mb-3 flex items-center gap-2 text-xs text-base-content/60">
                                <span class="badge badge-outline badge-info rounded-full">{{ $article->category?->name ?? 'General' }}</span>
                                <span>{{ optional($article->published_at ?: $article->created_at)->format('d M Y') }}</span>
                                <span>•</span>
                                <span>{{ $article->read_time ?? 1 }} min read</span>
                            </div>

                            <h2 class="line-clamp-2 text-xl font-semibold text-base-content">
                                <a href="{{ route('journal.show', $article->slug) }}" wire:navigate class="hover:text-info">{{ $article->title }}</a>
                            </h2>

                            <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-base-content/72">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 140) }}</p>

                            <div class="mt-4">
                                <a href="{{ route('journal.show', $article->slug) }}" wire:navigate class="btn btn-outline btn-sm rounded-xl">Read Article</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <article class="col-span-full rounded-2xl border border-dashed border-white/20 bg-base-100/40 p-10 text-center">
                        <p class="text-lg font-semibold text-base-content">No articles found</p>
                        <p class="mt-1 text-sm text-base-content/65">Try a different keyword or category filter.</p>
                    </article>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        </section>
    </main>
</div>
