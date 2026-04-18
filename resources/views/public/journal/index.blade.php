@php
    $localeText = static fn (mixed $value, string $default = ''): string => \App\Support\LocalizedContent::resolve($value, default: $default);

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
    $ctaText = (string) ($ctaText ?? __('navigation.hire_me'));
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
                <p class="text-sm uppercase tracking-[0.24em] text-info">{{ __('article.journal') }}</p>
                <h1 class="mt-3 text-4xl font-semibold text-base-content sm:text-5xl">{{ __('article.my_journal') }}</h1>
                <p class="mx-auto mt-4 max-w-2xl text-base text-base-content/70 sm:text-lg">{{ __('article.journal_intro') }}</p>
            </div>

            <div class="portfolio-glass mx-auto mt-10 max-w-4xl rounded-2xl border border-white/10 p-4 shadow-xl sm:p-5">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <p class="text-xs uppercase tracking-[0.22em] text-base-content/60">{{ __('article.live_filter') }}</p>
                    <span wire:loading.delay class="text-xs text-info">{{ __('article.updating_results') }}</span>
                </div>

                <div class="grid gap-3 md:grid-cols-[1fr,230px,auto] md:items-end">
                    <x-ui.input-field
                        name="search"
                        wire:model.live.debounce.350ms="search"
                        placeholder="{{ __('article.search_article') }}"
                        icon="search"
                        inputClass="bg-base-100/60 border-white/15"
                    />

                    <x-ui.select-field
                        name="category"
                        wire:model.live="activeCategory"
                        placeholder="{{ __('article.all_category') }}"
                        selectClass="bg-base-100/60 border-white/15"
                    >
                        <option value="">{{ __('article.all_category') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}">
                                {{ $localeText($category->name_translations ?? $category->name, $category->name) }} ({{ $category->public_articles_count }})
                            </option>
                        @endforeach
                    </x-ui.select-field>

                    <button
                        type="button"
                        wire:click="clearFilters"
                        @disabled($search === '' && $activeCategory === '')
                        class="btn btn-outline rounded-xl md:mb-1"
                    >
                        {{ __('common.reset') }}
                    </button>
                </div>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @forelse ($articles as $article)
                    @php
                        $articleTitle = $localeText($article->title_translations ?? $article->title, $article->title);
                        $articleContent = $localeText($article->content_translations ?? $article->content, $article->content);
                        $articleExcerpt = $localeText($article->excerpt_translations ?? $article->excerpt, $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($articleContent), 140));
                        $articleCategory = $localeText($article->category?->name_translations ?? $article->category?->name ?? __('article.general'), __('article.general'));
                    @endphp
                    <article class="skill-category-card overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-xl backdrop-blur-lg transition duration-300 hover:-translate-y-1 hover:shadow-info/20">
                        <span aria-hidden="true" class="skill-card-spotlight"></span>
                        <a href="{{ route('journal.show', $article->slug) }}" wire:navigate class="block">
                            <img
                                loading="lazy"
                                src="{{ $resolveAsset($article->thumbnail_path, 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80') }}"
                                alt="{{ $articleTitle }}"
                                class="h-44 w-full object-cover"
                            >
                        </a>

                        <div class="p-5">
                            <div class="mb-3 flex items-center gap-2 text-xs text-base-content/60">
                                <span class="badge badge-outline badge-info rounded-full">{{ $articleCategory }}</span>
                                <span>{{ optional($article->published_at ?: $article->created_at)->format('d M Y') }}</span>
                                <span>•</span>
                                <span>{{ $article->read_time ?? 1 }} {{ __('article.min_read') }}</span>
                            </div>

                            <h2 class="line-clamp-2 text-xl font-semibold text-base-content">
                                <a href="{{ route('journal.show', $article->slug) }}" wire:navigate class="hover:text-info">{{ $articleTitle }}</a>
                            </h2>

                            <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-base-content/72">{{ $articleExcerpt }}</p>

                            <div class="mt-4">
                                <a href="{{ route('journal.show', $article->slug) }}" wire:navigate class="btn btn-outline btn-sm rounded-xl">{{ __('article.read_article') }}</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <article class="col-span-full rounded-2xl border border-dashed border-white/20 bg-base-100/40 p-10 text-center">
                        <p class="text-lg font-semibold text-base-content">{{ __('article.no_articles_found') }}</p>
                        <p class="mt-1 text-sm text-base-content/65">{{ __('article.try_other_filter') }}</p>
                    </article>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $articles->links() }}
            </div>
        </section>
    </main>
</div>
