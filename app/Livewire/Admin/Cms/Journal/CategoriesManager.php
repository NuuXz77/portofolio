<?php

namespace App\Livewire\Admin\Cms\Journal;

use App\Models\ArticleCategory;
use App\Support\AdminActivity;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesManager extends Component
{
    use WithPagination;

    public string $editingLocale = 'id';

    public string $search = '';

    public string $sortField = 'name';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public ?int $categoryId = null;

    public string $name = '';

    public string $nameId = '';

    public string $nameEn = '';

    public string $slug = '';

    public string $description = '';

    public string $descriptionId = '';

    public string $descriptionEn = '';

    protected bool $autoGenerateSlug = true;

    public function updatingSearch(): void
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

    public function updatedNameId(string $value): void
    {
        $this->nameId = $value;
        $this->name = $value;

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

    public function openEditModal(int $categoryId): void
    {
        $category = ArticleCategory::query()->findOrFail($categoryId);
        $nameTranslations = \App\Support\LocalizedContent::split($category->name_translations ?? $category->name);
        $descriptionTranslations = \App\Support\LocalizedContent::split($category->description_translations ?? ($category->description ?? ''));

        $this->categoryId = $category->id;
        $this->name = $nameTranslations['id'];
        $this->nameId = $nameTranslations['id'];
        $this->nameEn = $nameTranslations['en'];
        $this->slug = $category->slug;
        $this->description = $descriptionTranslations['id'];
        $this->descriptionId = $descriptionTranslations['id'];
        $this->descriptionEn = $descriptionTranslations['en'];
        $this->autoGenerateSlug = false;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->name = trim($this->nameId) !== '' ? trim($this->nameId) : trim($this->nameEn);
        $this->description = trim($this->descriptionId) !== '' ? trim($this->descriptionId) : trim($this->descriptionEn);

        $this->validate([
            'nameId' => ['required', 'string', 'max:120'],
            'nameEn' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'string',
                'max:160',
                Rule::unique('article_categories', 'slug')->ignore($this->categoryId),
            ],
            'descriptionId' => ['nullable', 'string', 'max:500'],
            'descriptionEn' => ['nullable', 'string', 'max:500'],
        ]);

        ArticleCategory::query()->updateOrCreate(
            ['id' => $this->categoryId],
            [
                'name' => $this->name,
                'name_translations' => \App\Support\LocalizedContent::pack($this->nameId, $this->nameEn),
                'slug' => $this->slug,
                'description' => $this->description !== '' ? $this->description : null,
                'description_translations' => \App\Support\LocalizedContent::pack($this->descriptionId, $this->descriptionEn),
            ]
        );

        AdminActivity::log('saved', 'journal-category', 'Saved journal category.', [
            'name' => $this->name,
        ]);

        $this->showModal = false;
        $this->resetForm();
    session()->flash('success', 'Category saved successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Category saved successfully.');
    }

    public function deleteCategory(int $categoryId): void
    {
        $category = ArticleCategory::query()->findOrFail($categoryId);

        if ($category->articles()->exists()) {
            session()->flash('error', 'Category cannot be deleted because it is used by articles.');
            $this->dispatch('app-toast', type: 'error', message: 'Category cannot be deleted because it is used by articles.');

            return;
        }

        $name = $category->name;
        $category->delete();

        AdminActivity::log('deleted', 'journal-category', 'Deleted journal category.', [
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
            'nameId',
            'nameEn',
            'slug',
            'description',
            'descriptionId',
            'descriptionEn',
        ]);

        $this->autoGenerateSlug = true;
        $this->resetValidation();
    }

    #[Layout('components.layouts.admin')]
    #[Title('Journal Categories')]
    public function render()
    {
        $categories = ArticleCategory::query()
            ->withCount('articles')
            ->when($this->search !== '', function ($builder): void {
                $builder->where(function ($nested): void {
                    $nested
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('slug', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('id')
            ->paginate(10);

        return view('admin.cms.journal.categories-manager', [
            'categories' => $categories,
        ]);
    }
}
