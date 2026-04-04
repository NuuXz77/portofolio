<?php

namespace App\Livewire\Public\Journal;

use App\Models\Article;
use App\Models\ArticleCategory;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public string $activeCategory = '';

    public function mount(): void
    {
        $this->search = trim((string) request()->query('search', ''));
        $this->activeCategory = trim((string) request()->query('category', ''));
    }

    #[Layout('components.layouts.portfolio')]
    public function render()
    {
        $articles = Article::query()
            ->with('category:id,name,slug')
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
        ])->title('My Journal | Wisnu.dev');
    }
}
