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

    $shareUrl = request()->fullUrl();

    $logoText = 'Wisnu.dev';
    $navItems = [
        ['href' => route('home'), 'label' => 'Home'],
        ['href' => route('journal.index'), 'label' => 'Journal'],
    ];
@endphp

<div id="portfolio" data-portfolio-root class="relative overflow-x-clip bg-base-100 text-base-content">
    <div aria-hidden="true" class="pointer-events-none portfolio-glow"></div>
    <div aria-hidden="true" class="pointer-events-none portfolio-grid"></div>

    <x-partials.public-navbar
        :logoText="$logoText"
        :brandHref="route('home')"
        :navItems="$navItems"
        ctaText="Back to Journal"
        :ctaLink="route('journal.index')"
    />

    <main class="px-4 pb-20 pt-12 sm:px-8 sm:pt-16">
        <article class="mx-auto max-w-4xl">
            <img
                loading="eager"
                src="{{ $resolveAsset($article->thumbnail_path, 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1600&q=80') }}"
                alt="{{ $article->title }} cover"
                class="h-64 w-full rounded-3xl border border-white/10 object-cover shadow-2xl sm:h-80"
            >

            <div class="mt-8">
                <div class="flex flex-wrap items-center gap-2 text-xs text-base-content/60">
                    @if ($isPrivatePreview)
                        <span class="badge badge-warning badge-outline rounded-full">Private Preview</span>
                    @endif
                    <span class="badge badge-outline badge-info rounded-full">{{ $article->category?->name ?? 'General' }}</span>
                    <span>{{ optional($article->published_at ?: $article->created_at)->format('d M Y') }}</span>
                    <span>•</span>
                    <span>{{ $article->author_name ?: 'Admin' }}</span>
                    <span>•</span>
                    <span>{{ $article->read_time ?? 1 }} min read</span>
                    <span>•</span>
                    <span>{{ number_format($article->view_count) }} views</span>
                </div>

                <h1 class="mt-4 text-3xl font-semibold leading-tight text-base-content sm:text-4xl">{{ $article->title }}</h1>

                @if (! empty($article->tags))
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($article->tags as $tag)
                            <span class="badge badge-outline rounded-full">#{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-8 rounded-2xl border border-white/10 bg-base-100/60 p-5 shadow-xl">
                    <div class="prose prose-invert max-w-none leading-relaxed text-base-content/85">
                        {!! $article->content !!}
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-2">
                    <span class="text-sm text-base-content/60">Share:</span>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($article->title) }}" target="_blank" rel="noreferrer" class="btn btn-outline btn-xs rounded-xl">Twitter</a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank" rel="noreferrer" class="btn btn-outline btn-xs rounded-xl">LinkedIn</a>
                </div>
            </div>
        </article>

        @if ($relatedArticles->isNotEmpty())
            <section class="mx-auto mt-14 max-w-7xl">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-base-content">Related Articles</h2>
                    <a href="{{ route('journal.index') }}" wire:navigate class="btn btn-outline btn-sm rounded-xl">View All</a>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($relatedArticles as $related)
                        <article class="skill-category-card rounded-2xl border border-white/10 bg-white/5 p-4 shadow-lg backdrop-blur-lg">
                            <span aria-hidden="true" class="skill-card-spotlight"></span>
                            <p class="text-xs text-base-content/60">{{ optional($related->published_at ?: $related->created_at)->format('d M Y') }}</p>
                            <h3 class="mt-2 line-clamp-2 text-lg font-semibold text-base-content">
                                <a href="{{ route('journal.show', $related->slug) }}" wire:navigate class="hover:text-info">{{ $related->title }}</a>
                            </h3>
                            <p class="mt-2 line-clamp-2 text-sm text-base-content/70">{{ $related->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($related->content), 90) }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>
</div>
