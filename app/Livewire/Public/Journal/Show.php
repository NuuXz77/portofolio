<?php

namespace App\Livewire\Public\Journal;

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\ArticleCommentLike;
use App\Models\ArticleLike;
use App\Support\PublicNavbarData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    private const GUEST_TOKEN_COOKIE = 'journal_guest_token';

    private const GUEST_NAME_COOKIE = 'journal_guest_name';

    public Article $article;

    public Collection $relatedArticles;

    public bool $isPrivatePreview = false;

    public string $guestToken = '';

    public string $guestName = '';

    public string $guestEmail = '';

    public string $commentBody = '';

    public int $commentsVisible = 8;

    public array $replyDrafts = [];

    public array $replyOpen = [];

    public ?int $editingCommentId = null;

    public string $editingBody = '';

    public function mount(string $slug): void
    {
        $article = Article::query()
            ->with('category:id,name,slug')
            ->where('slug', $slug)
            ->firstOrFail();

        $isAdmin = (bool) request()->user()?->role === 'admin';
        $providedKey = (string) request()->query('key', '');

        $isPublicPublished = $article->status === 'published'
            && $article->visibility === 'public'
            && (! $article->published_at || $article->published_at->lte(now()));

        $isPrivateAccessible = $article->status === 'published'
            && $article->visibility === 'private'
            && $article->access_token
            && hash_equals($article->access_token, $providedKey);

        if (! $isAdmin && ! $isPublicPublished && ! $isPrivateAccessible) {
            abort(404);
        }

        if (! $isAdmin) {
            $article->increment('view_count');
            $article->refresh();
        }

        $this->article = $article;
        $this->isPrivatePreview = $article->visibility === 'private';

        $this->relatedArticles = Article::query()
            ->with('category:id,name,slug')
            ->publiclyVisible()
            ->where('id', '!=', $article->id)
            ->when($article->category_id, fn ($builder) => $builder->where('category_id', $article->category_id))
            ->latest('published_at')
            ->take(3)
            ->get();

        $this->guestToken = trim((string) request()->cookie(self::GUEST_TOKEN_COOKIE));

        if ($this->guestToken === '') {
            $this->guestToken = (string) Str::uuid().Str::random(24);
            Cookie::queue(Cookie::make(self::GUEST_TOKEN_COOKIE, $this->guestToken, 60 * 24 * 365 * 5));
        }

        $this->guestName = trim((string) request()->cookie(self::GUEST_NAME_COOKIE, ''));
    }

    public function toggleArticleLike(): void
    {
        $deviceHash = $this->guestTokenHash();

        $existing = ArticleLike::query()
            ->where('article_id', $this->article->id)
            ->where('device_token_hash', $deviceHash)
            ->first();

        $liked = false;

        if ($existing) {
            $existing->delete();
        } else {
            ArticleLike::query()->create([
                'article_id' => $this->article->id,
                'device_token_hash' => $deviceHash,
            ]);

            $liked = true;
        }

        $this->dispatch('journal-like-sync', kind: 'article', targetId: $this->article->id, liked: $liked);
    }

    public function postComment(): void
    {
        $validated = $this->validate([
            'guestName' => ['required', 'string', 'min:3', 'max:80'],
            'guestEmail' => ['nullable', 'email:rfc,dns', 'max:120'],
            'commentBody' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        if (! $this->canPostComment()) {
            return;
        }

        $name = $this->normalizeName((string) $validated['guestName']);
        $email = trim((string) ($validated['guestEmail'] ?? ''));
        $body = $this->normalizeBody((string) $validated['commentBody']);

        if (! $this->passesSpamFilter($body)) {
            $this->addError('commentBody', 'Komentar terdeteksi sebagai spam. Hindari link/promosi berlebihan.');
            $this->dispatch('app-toast', type: 'warning', message: 'Komentar terdeteksi spam.');

            return;
        }

        ArticleComment::query()->create([
            'article_id' => $this->article->id,
            'parent_id' => null,
            'depth' => 0,
            'guest_name' => $name,
            'guest_email' => $email !== '' ? $email : null,
            'body' => $body,
            'owner_token_hash' => $this->guestTokenHash(),
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 255, ''),
        ]);

        Cookie::queue(Cookie::make(self::GUEST_NAME_COOKIE, $name, 60 * 24 * 365));

        $this->guestName = $name;
        $this->commentBody = '';
        $this->resetErrorBag('commentBody');

        $this->dispatch('app-toast', type: 'success', message: 'Komentar berhasil diposting.');
    }

    public function toggleReplyComposer(int $commentId): void
    {
        $comment = ArticleComment::query()
            ->where('article_id', $this->article->id)
            ->find($commentId);

        if (! $comment || $comment->depth >= 2) {
            $this->dispatch('app-toast', type: 'warning', message: 'Reply maksimal sampai kedalaman level 2.');

            return;
        }

        $isOpen = (bool) ($this->replyOpen[$commentId] ?? false);
        $this->replyOpen[$commentId] = ! $isOpen;
    }

    public function postReply(int $parentId): void
    {
        $rules = [
            'guestName' => ['required', 'string', 'min:3', 'max:80'],
            'guestEmail' => ['nullable', 'email:rfc,dns', 'max:120'],
            "replyDrafts.$parentId" => ['required', 'string', 'min:5', 'max:2000'],
        ];

        $validated = $this->validate($rules);

        if (! $this->canPostComment()) {
            return;
        }

        $parent = ArticleComment::query()
            ->where('article_id', $this->article->id)
            ->find($parentId);

        if (! $parent) {
            $this->dispatch('app-toast', type: 'error', message: 'Komentar induk tidak ditemukan.');

            return;
        }

        $depth = (int) $parent->depth + 1;

        if ($depth > 2) {
            $this->dispatch('app-toast', type: 'warning', message: 'Reply maksimal sampai kedalaman level 2.');

            return;
        }

        $name = $this->normalizeName((string) $validated['guestName']);
        $email = trim((string) ($validated['guestEmail'] ?? ''));
        $body = $this->normalizeBody((string) ($validated['replyDrafts'][$parentId] ?? ''));

        if (! $this->passesSpamFilter($body)) {
            $this->addError("replyDrafts.$parentId", 'Balasan terdeteksi sebagai spam.');
            $this->dispatch('app-toast', type: 'warning', message: 'Balasan terdeteksi spam.');

            return;
        }

        ArticleComment::query()->create([
            'article_id' => $this->article->id,
            'parent_id' => $parent->id,
            'depth' => $depth,
            'guest_name' => $name,
            'guest_email' => $email !== '' ? $email : null,
            'body' => $body,
            'owner_token_hash' => $this->guestTokenHash(),
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 255, ''),
        ]);

        Cookie::queue(Cookie::make(self::GUEST_NAME_COOKIE, $name, 60 * 24 * 365));

        $this->guestName = $name;
        $this->replyDrafts[$parentId] = '';
        $this->replyOpen[$parentId] = false;
        $this->resetErrorBag("replyDrafts.$parentId");

        $this->dispatch('app-toast', type: 'success', message: 'Balasan berhasil dikirim.');
    }

    public function toggleCommentLike(int $commentId): void
    {
        $comment = ArticleComment::query()
            ->where('article_id', $this->article->id)
            ->find($commentId);

        if (! $comment) {
            return;
        }

        $deviceHash = $this->guestTokenHash();

        $existing = ArticleCommentLike::query()
            ->where('article_comment_id', $comment->id)
            ->where('device_token_hash', $deviceHash)
            ->first();

        $liked = false;

        if ($existing) {
            $existing->delete();
            $comment->likes_count = max(0, (int) $comment->likes_count - 1);
        } else {
            ArticleCommentLike::query()->create([
                'article_comment_id' => $comment->id,
                'device_token_hash' => $deviceHash,
            ]);

            $comment->likes_count = (int) $comment->likes_count + 1;
            $liked = true;
        }

        $comment->save();

        $this->dispatch('journal-like-sync', kind: 'comment', targetId: $comment->id, liked: $liked);
    }

    public function startEditingComment(int $commentId): void
    {
        $comment = ArticleComment::query()
            ->where('article_id', $this->article->id)
            ->find($commentId);

        if (! $comment || ! $this->ownsComment($comment)) {
            $this->dispatch('app-toast', type: 'error', message: 'Kamu hanya bisa edit komentar milikmu sendiri.');

            return;
        }

        $this->editingCommentId = $comment->id;
        $this->editingBody = $comment->body;
    }

    public function cancelEditingComment(): void
    {
        $this->editingCommentId = null;
        $this->editingBody = '';
    }

    public function saveEditedComment(int $commentId): void
    {
        $comment = ArticleComment::query()
            ->where('article_id', $this->article->id)
            ->find($commentId);

        if (! $comment || ! $this->ownsComment($comment)) {
            $this->dispatch('app-toast', type: 'error', message: 'Kamu hanya bisa edit komentar milikmu sendiri.');

            return;
        }

        if ($this->editingCommentId !== $comment->id) {
            $this->dispatch('app-toast', type: 'warning', message: 'Pilih komentar yang ingin diedit terlebih dahulu.');

            return;
        }

        $validated = $this->validate([
            'editingBody' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $nextBody = $this->normalizeBody((string) ($validated['editingBody'] ?? ''));

        if (! $this->passesSpamFilter($nextBody)) {
            $this->addError('editingBody', 'Komentar terdeteksi sebagai spam.');

            return;
        }

        $comment->body = $nextBody;
        $comment->is_edited = true;
        $comment->edited_at = now();
        $comment->save();

        $this->cancelEditingComment();
        $this->dispatch('app-toast', type: 'success', message: 'Komentar berhasil diperbarui.');
    }

    public function deleteComment(int $commentId): void
    {
        $comment = ArticleComment::query()
            ->where('article_id', $this->article->id)
            ->find($commentId);

        if (! $comment || ! $this->ownsComment($comment)) {
            $this->dispatch('app-toast', type: 'error', message: 'Kamu hanya bisa menghapus komentar milikmu sendiri.');

            return;
        }

        $comment->delete();

        if ($this->editingCommentId === $commentId) {
            $this->cancelEditingComment();
        }

        $this->dispatch('app-toast', type: 'success', message: 'Komentar berhasil dihapus.');
    }

    public function loadMoreComments(): void
    {
        $this->commentsVisible += 8;
    }

    protected function canPostComment(): bool
    {
        $key = $this->commentsRateLimitKey();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $message = "Terlalu cepat. Coba lagi dalam {$seconds} detik.";
            $this->addError('commentBody', $message);
            $this->dispatch('app-toast', type: 'warning', message: $message);

            return false;
        }

        RateLimiter::hit($key, 60);

        return true;
    }

    protected function commentsRateLimitKey(): string
    {
        $ip = (string) request()->ip();

        return 'journal-comment:'.$this->article->id.':'.sha1($ip.'|'.$this->guestTokenHash());
    }

    protected function guestTokenHash(): string
    {
        return hash('sha256', $this->guestToken);
    }

    protected function normalizeName(string $name): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', $name));
    }

    protected function normalizeBody(string $body): string
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $body);
        $normalized = trim((string) preg_replace('/[\t ]{2,}/u', ' ', $normalized));

        return $normalized;
    }

    protected function passesSpamFilter(string $text): bool
    {
        $normalized = mb_strtolower($text);
        $bannedSnippets = [
            'viagra',
            'casino',
            'slot gacor',
            'bonus new member',
            'click here',
            'free money',
        ];

        foreach ($bannedSnippets as $snippet) {
            if (str_contains($normalized, $snippet)) {
                return false;
            }
        }

        if (preg_match('/https?:\/\/|www\./iu', $normalized) === 1) {
            return false;
        }

        if (preg_match('/(.)\1{10,}/u', $normalized) === 1) {
            return false;
        }

        return true;
    }

    protected function ownsComment(ArticleComment $comment): bool
    {
        return hash_equals($comment->owner_token_hash, $this->guestTokenHash());
    }

    protected function loadVisibleComments(): Collection
    {
        return ArticleComment::query()
            ->where('article_id', $this->article->id)
            ->roots()
            ->orderByDesc('created_at')
            ->with([
                'replies' => fn ($query) => $query
                    ->orderBy('created_at')
                    ->with([
                        'replies' => fn ($nested) => $nested->orderBy('created_at'),
                    ]),
            ])
            ->limit($this->commentsVisible)
            ->get();
    }

    /**
     * @return array<int, int>
     */
    protected function collectCommentIds(Collection $comments): array
    {
        $ids = [];

        foreach ($comments as $comment) {
            if (! $comment instanceof ArticleComment) {
                continue;
            }

            $ids[] = $comment->id;

            if ($comment->relationLoaded('replies') && $comment->replies->isNotEmpty()) {
                $ids = [...$ids, ...$this->collectCommentIds($comment->replies)];
            }
        }

        return array_values(array_unique($ids));
    }

    #[Layout('components.layouts.portfolio')]
    public function render()
    {
        $brandName = PublicNavbarData::brandName();
        $navbarData = PublicNavbarData::forJournal();

        $comments = $this->loadVisibleComments();
        $allCommentIds = $this->collectCommentIds($comments);
        $deviceHash = $this->guestTokenHash();

        $likedCommentIds = $allCommentIds === []
            ? []
            : ArticleCommentLike::query()
                ->whereIn('article_comment_id', $allCommentIds)
                ->where('device_token_hash', $deviceHash)
                ->pluck('article_comment_id')
                ->map(fn ($id) => (int) $id)
                ->all();

        $ownedCommentIds = $allCommentIds === []
            ? []
            : ArticleComment::query()
                ->whereIn('id', $allCommentIds)
                ->where('owner_token_hash', $deviceHash)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

        $articleLikesCount = ArticleLike::query()
            ->where('article_id', $this->article->id)
            ->count();

        $articleLikedByMe = ArticleLike::query()
            ->where('article_id', $this->article->id)
            ->where('device_token_hash', $deviceHash)
            ->exists();

        $totalCommentsCount = ArticleComment::query()
            ->where('article_id', $this->article->id)
            ->count();

        $totalTopLevelComments = ArticleComment::query()
            ->where('article_id', $this->article->id)
            ->whereNull('parent_id')
            ->count();

        return view('public.journal.show', [
            'article' => $this->article,
            'relatedArticles' => $this->relatedArticles,
            'isPrivatePreview' => $this->isPrivatePreview,
            'comments' => $comments,
            'likedCommentIds' => $likedCommentIds,
            'ownedCommentIds' => $ownedCommentIds,
            'articleLikesCount' => $articleLikesCount,
            'articleLikedByMe' => $articleLikedByMe,
            'totalCommentsCount' => $totalCommentsCount,
            'totalTopLevelComments' => $totalTopLevelComments,
            'hasMoreComments' => $totalTopLevelComments > $comments->count(),
            ...$navbarData,
        ])->title(($this->article->seo_title ?: $this->article->title).' | '.$brandName);
    }
}
