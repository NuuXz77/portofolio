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

    $shareUrl = route('journal.show', $article->slug);
    $shareUrlEncoded = urlencode($shareUrl);
    $shareWhatsappTextEncoded = urlencode($article->title.' - '.$shareUrl);
    $shareTwitterTextEncoded = urlencode($article->title);

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

    $likedCommentLookup = [];

    foreach (($likedCommentIds ?? []) as $likedCommentId) {
        $likedCommentLookup[(int) $likedCommentId] = true;
    }

    $ownedCommentLookup = [];

    foreach (($ownedCommentIds ?? []) as $ownedCommentId) {
        $ownedCommentLookup[(int) $ownedCommentId] = true;
    }

    $guestTokenForClient = (string) ($guestToken ?? '');
@endphp

<div
    data-journal-interaction
    data-guest-token="{{ $guestTokenForClient }}"
    data-article-id="{{ $article->id }}"
    data-share-url="{{ $shareUrl }}"
>
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
                    <div class="journal-content prose prose-invert max-w-none leading-relaxed text-base-content/85">
                        {!! $article->content !!}
                    </div>
                </div>

                <section class="mt-8 grid gap-4 rounded-2xl border border-white/10 bg-base-100/60 p-4 shadow-xl sm:grid-cols-[1fr_auto] sm:items-center sm:p-5">
                    <div>
                        <p class="text-sm font-semibold text-base-content">Share this article</p>
                        <p class="mt-1 text-xs text-base-content/65">Bagikan ke temanmu atau simpan link untuk dibaca nanti.</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                class="btn btn-outline btn-sm rounded-xl"
                                data-copy-link-button
                                data-share-url="{{ $shareUrl }}"
                            >
                                <i data-lucide="link" class="h-4 w-4"></i>
                                Copy Link
                            </button>

                            <a href="https://wa.me/?text={{ $shareWhatsappTextEncoded }}" target="_blank" rel="noreferrer" class="btn btn-outline btn-sm rounded-xl">
                                <i data-lucide="message-circle" class="h-4 w-4"></i>
                                WhatsApp
                            </a>

                            <a href="https://twitter.com/intent/tweet?url={{ $shareUrlEncoded }}&text={{ $shareTwitterTextEncoded }}" target="_blank" rel="noreferrer" class="btn btn-outline btn-sm rounded-xl">
                                <img src="https://cdn.simpleicons.org/x/E5E7EB" alt="Twitter" class="h-3.5 w-3.5" loading="lazy">
                                Twitter
                            </a>

                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrlEncoded }}" target="_blank" rel="noreferrer" class="btn btn-outline btn-sm rounded-xl">
                                <svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 fill-current" role="img">
                                    <path d="M20.45 20.45h-3.56v-5.58c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.15 1.45-2.15 2.94v5.68H9.33V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 1 1 0-4.14 2.07 2.07 0 0 1 0 4.14zM7.12 20.45H3.55V9h3.57v11.45zM22.23 0H1.77A1.76 1.76 0 0 0 0 1.74v20.52C0 23.23.79 24 1.77 24h20.46c.98 0 1.77-.77 1.77-1.74V1.74A1.76 1.76 0 0 0 22.23 0z" />
                                </svg>
                                LinkedIn
                            </a>
                        </div>
                    </div>

                    <button
                        type="button"
                        wire:click="toggleArticleLike"
                        wire:loading.attr="disabled"
                        wire:target="toggleArticleLike"
                        class="journal-like-btn btn rounded-2xl px-5 {{ $articleLikedByMe ? 'is-active btn-info' : 'btn-outline' }}"
                    >
                        <i data-lucide="heart" class="h-4 w-4"></i>
                        <span>{{ number_format($articleLikesCount) }} likes</span>
                    </button>
                </section>

                <section id="comments" class="mt-8 rounded-2xl border border-white/10 bg-base-100/60 p-4 shadow-xl sm:p-5">
                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-xl font-semibold text-base-content sm:text-2xl">Discussion</h2>
                        <span class="badge badge-outline badge-info rounded-full">{{ number_format($totalCommentsCount) }} comments</span>
                    </div>

                    <form wire:submit="postComment" class="space-y-3">
                        <div class="grid gap-3 sm:grid-cols-2">
                            <label class="form-control">
                                <span class="label">
                                    <span class="label-text text-xs text-base-content/65">Name</span>
                                </span>
                                <input
                                    type="text"
                                    wire:model.defer="guestName"
                                    class="input input-bordered w-full rounded-xl border-white/15 bg-base-100/70"
                                    placeholder="Your name"
                                >
                            </label>

                            <label class="form-control">
                                <span class="label">
                                    <span class="label-text text-xs text-base-content/65">Email (optional)</span>
                                </span>
                                <input
                                    type="email"
                                    wire:model.defer="guestEmail"
                                    class="input input-bordered w-full rounded-xl border-white/15 bg-base-100/70"
                                    placeholder="you@example.com"
                                >
                            </label>
                        </div>

                        <label class="form-control">
                            <span class="label">
                                <span class="label-text text-xs text-base-content/65">Comment</span>
                            </span>
                            <textarea
                                wire:model.defer="commentBody"
                                rows="4"
                                class="textarea textarea-bordered w-full rounded-xl border-white/15 bg-base-100/70"
                                placeholder="Write your thoughts..."
                            ></textarea>
                        </label>

                        @error('guestName')
                            <p class="text-xs text-error">{{ $message }}</p>
                        @enderror
                        @error('guestEmail')
                            <p class="text-xs text-error">{{ $message }}</p>
                        @enderror
                        @error('commentBody')
                            <p class="text-xs text-error">{{ $message }}</p>
                        @enderror

                        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                            <p class="text-xs text-base-content/60">Tanpa login. Komentar milikmu dikenali dari browser ini.</p>
                            <button type="submit" class="btn btn-info rounded-xl" wire:loading.attr="disabled" wire:target="postComment">
                                <span wire:loading.remove wire:target="postComment">Post Comment</span>
                                <span wire:loading wire:target="postComment" class="loading loading-spinner loading-sm"></span>
                            </button>
                        </div>
                    </form>
                </section>

                <section class="mt-6 space-y-4" aria-live="polite">
                    @if ($comments->isEmpty())
                        <article class="rounded-2xl border border-dashed border-white/20 bg-base-100/45 p-8 text-center">
                            <p class="text-base font-semibold text-base-content">Belum ada komentar</p>
                            <p class="mt-1 text-sm text-base-content/65">Jadilah orang pertama yang memulai diskusi.</p>
                        </article>
                    @else
                        @foreach ($comments as $comment)
                            @include('public.journal.partials.comment-item', [
                                'comment' => $comment,
                                'level' => 0,
                                'ownedCommentLookup' => $ownedCommentLookup,
                                'likedCommentLookup' => $likedCommentLookup,
                            ])
                        @endforeach

                        @if ($hasMoreComments)
                            <div class="pt-2 text-center">
                                <button
                                    type="button"
                                    wire:click="loadMoreComments"
                                    class="btn btn-outline rounded-xl"
                                >
                                    Load more comments
                                </button>
                            </div>
                        @endif
                    @endif
                </section>
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
