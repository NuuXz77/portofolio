<?php

namespace App\Livewire\Public\Journal;

use App\Models\Article;
use App\Support\PublicNavbarData;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Article $article;

    public Collection $relatedArticles;

    public bool $isPrivatePreview = false;

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
    }

    #[Layout('components.layouts.portfolio')]
    public function render()
    {
        $brandName = PublicNavbarData::brandName();
        $navbarData = PublicNavbarData::forJournal();

        return view('public.journal.show', [
            'article' => $this->article,
            'relatedArticles' => $this->relatedArticles,
            'isPrivatePreview' => $this->isPrivatePreview,
            ...$navbarData,
        ])->title(($this->article->seo_title ?: $this->article->title).' | '.$brandName);
    }
}
