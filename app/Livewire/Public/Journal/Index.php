<?php

namespace App\Livewire\Public\Journal;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Support\PublicNavbarData;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $activeCategory = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActiveCategory(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->activeCategory = '';
        $this->resetPage();
    }

    public function mount(): void
    {
        $this->search = trim((string) request()->query('search', ''));
        $this->activeCategory = trim((string) request()->query('category', ''));
    }

    #[Layout('components.layouts.portfolio')]
    public function render()
    {
        $brandName = PublicNavbarData::brandName();
        $navbarData = PublicNavbarData::forJournal();

        $articles = Article::query()
            ->with('category:id,name,name_translations,slug')
            ->publiclyVisible()
            ->when($this->search !== '', function ($builder): void {
                $builder->where(function ($nested): void {
                    $nested
                        ->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('excerpt', 'like', '%'.$this->search.'%')
                        ->orWhere('content', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->activeCategory !== '', function ($builder): void {
                $builder->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $this->activeCategory));
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $categories = ArticleCategory::query()
            ->whereHas('articles', fn ($builder) => $builder->publiclyVisible())
            ->withCount([
                'articles as public_articles_count' => fn ($builder) => $builder->publiclyVisible(),
            ])
            ->orderBy('name')
            ->get();

        return view('public.journal.index', [
            'articles' => $articles,
            'categories' => $categories,
            'search' => $this->search,
            'activeCategory' => $this->activeCategory,
            ...$navbarData,
        ])->title(__('article.my_journal').' | '.$brandName);
    }
}
