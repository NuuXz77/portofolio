<?php

namespace App\Livewire\Admin\Cms\Journal;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Support\AdminActivity;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ArticlesManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = 'all';

    public string $statusFilter = 'all';

    public string $visibilityFilter = 'all';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingVisibilityFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

    public function deleteArticle(int $articleId): void
    {
        $article = Article::query()->findOrFail($articleId);
        $title = $article->title;

        $article->delete();

        AdminActivity::log('deleted', 'journal', 'Deleted journal article.', [
            'title' => $title,
        ]);
    session()->flash('success', 'Article deleted successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Article deleted successfully.');
    }

    #[Layout('components.layouts.admin')]
    #[Title('Journal Articles')]
    public function render()
    {
        $query = Article::query()
            ->with('category:id,name')
            ->when($this->search !== '', function ($builder): void {
                $builder->where(function ($nested): void {
                    $nested
                        ->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('excerpt', 'like', '%'.$this->search.'%')
                        ->orWhere('content', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->categoryFilter !== 'all', fn ($builder) => $builder->where('category_id', $this->categoryFilter))
            ->when($this->statusFilter !== 'all', fn ($builder) => $builder->where('status', $this->statusFilter))
            ->when($this->visibilityFilter !== 'all', fn ($builder) => $builder->where('visibility', $this->visibilityFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderByDesc('id');

        return view('admin.cms.journal.articles-manager', [
            'articles' => $query->paginate(10),
            'categories' => ArticleCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
