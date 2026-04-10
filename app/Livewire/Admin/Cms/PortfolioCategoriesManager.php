<?php

namespace App\Livewire\Admin\Cms;

use App\Models\PortfolioCategory;
use App\Support\AdminActivity;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class PortfolioCategoriesManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = 'all';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public ?int $categoryId = null;

    public string $name = '';

    public string $slug = '';

    public string $type = 'skill';

    public string $description = '';

    public int $sortOrder = 0;

    public bool $isVisible = true;

    protected bool $autoGenerateSlug = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
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

    public function updatedName(string $value): void
    {
        if (! $this->autoGenerateSlug) {
            return;
        }

        $this->slug = Str::slug($value);
    }

    public function updatedSlug(string $value): void
    {
        $this->autoGenerateSlug = false;
        $this->slug = Str::slug($value);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $id): void
    {
        $category = PortfolioCategory::query()->findOrFail($id);

        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->type = $category->type;
        $this->description = $category->description ?? '';
        $this->sortOrder = $category->sort_order;
        $this->isVisible = $category->is_visible;
        $this->autoGenerateSlug = false;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'string',
                'max:160',
                Rule::unique('portfolio_categories', 'slug')
                    ->where(fn ($query) => $query->where('type', $this->type))
                    ->ignore($this->categoryId),
            ],
            'type' => ['required', Rule::in(['skill', 'project'])],
            'description' => ['nullable', 'string', 'max:500'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isVisible' => ['required', 'boolean'],
        ]);

        PortfolioCategory::query()->updateOrCreate(
            ['id' => $this->categoryId],
            [
                'name' => $this->name,
                'slug' => $this->slug,
                'type' => $this->type,
                'description' => $this->description !== '' ? $this->description : null,
                'sort_order' => $this->sortOrder,
                'is_visible' => $this->isVisible,
            ]
        );

        AdminActivity::log('saved', 'portfolio-category', 'Saved portfolio category.', [
            'name' => $this->name,
            'type' => $this->type,
        ]);

        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', 'Category saved successfully.');
        $this->dispatch('app-toast', type: 'success', message: 'Category saved successfully.');
    }

    public function deleteCategory(int $id): void
    {
        $category = PortfolioCategory::query()
            ->withCount(['skills', 'projects'])
            ->findOrFail($id);

        if ($category->skills_count > 0 || $category->projects_count > 0) {
            session()->flash('error', 'Category cannot be deleted because it is already used by skills or projects.');
            $this->dispatch('app-toast', type: 'error', message: 'Category cannot be deleted because it is already used by skills or projects.');

            return;
        }

        $name = $category->name;
        $category->delete();

        AdminActivity::log('deleted', 'portfolio-category', 'Deleted portfolio category.', [
            'name' => $name,
        ]);

        session()->flash('success', 'Category deleted successfully.');
        $this->dispatch('app-toast', type: 'success', message: 'Category deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'categoryId',
            'name',
            'slug',
            'type',
            'description',
            'sortOrder',
            'isVisible',
        ]);

        $this->type = $this->typeFilter !== 'all' ? $this->typeFilter : 'skill';
        $this->sortOrder = 0;
        $this->isVisible = true;
        $this->autoGenerateSlug = true;
        $this->resetValidation();
    }

    #[Layout('components.layouts.admin')]
    #[Title('Portfolio Categories')]
    public function render()
    {
        $categories = PortfolioCategory::query()
            ->withCount(['skills', 'projects'])
            ->when($this->search !== '', function ($builder): void {
                $builder->where(function ($nested): void {
                    $nested
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter !== 'all', fn ($builder) => $builder->where('type', $this->typeFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('id')
            ->paginate(12);

        return view('admin.cms.portfolio-categories-manager', [
            'categories' => $categories,
        ]);
    }
}
