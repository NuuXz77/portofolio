<?php

namespace App\Livewire\Admin\Cms;

use App\Models\PortfolioCategory;
use App\Models\Project;
use App\Support\AdminActivity;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class ProjectsManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = 'all';

    public string $sortField = 'sort_order';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public ?int $projectId = null;

    public string $title = '';

    public string $description = '';

    public string $techStack = '';

    public string $demoLink = '';

    public string $githubLink = '';

    public string $categoryId = '';

    public bool $isFeatured = false;

    public bool $isVisible = true;

    public int $sortOrder = 0;

    public ?string $existingImage = null;

    public $projectImage;

    #[Layout('components.layouts.admin')]
    #[Title('Projects Management')]
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
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

    public function openCreateModal(): void
    {
        $this->resetForm();
        if ($this->categoryId === '') {
            $this->categoryId = (string) $this->resolveDefaultCategoryId();
        }
        $this->showModal = true;
    }

    public function openEditModal(int $projectId): void
    {
        $project = Project::query()->findOrFail($projectId);

        $this->projectId = $project->id;
        $this->title = $project->title;
        $this->description = $project->description;
        $this->techStack = implode(', ', $project->tech_stack ?? []);
        $this->demoLink = $project->demo_link ?? '';
        $this->githubLink = $project->github_link ?? '';
        $this->categoryId = (string) ($project->category_id ?: $this->resolveCategoryIdByName($project->category));
        $this->isFeatured = $project->is_featured;
        $this->isVisible = $project->is_visible;
        $this->sortOrder = $project->sort_order;
        $this->existingImage = $project->image_path;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:5000'],
            'techStack' => ['required', 'string', 'max:1000'],
            'demoLink' => ['nullable', 'string', 'max:255'],
            'githubLink' => ['nullable', 'string', 'max:255'],
            'categoryId' => [
                'required',
                'integer',
                Rule::exists('portfolio_categories', 'id')->where('type', 'project'),
            ],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isFeatured' => ['required', 'boolean'],
            'isVisible' => ['required', 'boolean'],
            'projectImage' => ['nullable', 'image', 'max:3072'],
        ]);

        $category = PortfolioCategory::query()
            ->project()
            ->findOrFail((int) $this->categoryId);

        $imagePath = $this->existingImage;

        if ($this->projectImage) {
            $imagePath = $this->projectImage->store('portfolio/projects', 'public');
        }

        $stack = collect(explode(',', $this->techStack))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->values()
            ->all();

        Project::query()->updateOrCreate(
            ['id' => $this->projectId],
            [
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'description' => $this->description,
                'tech_stack' => $stack,
                'image_path' => $imagePath,
                'demo_link' => $this->demoLink ?: null,
                'github_link' => $this->githubLink ?: null,
                'category' => $category->slug,
                'category_id' => $category->id,
                'is_featured' => $this->isFeatured,
                'is_visible' => $this->isVisible,
                'sort_order' => $this->sortOrder,
            ]
        );

        AdminActivity::log('saved', 'projects', 'Saved project record.', [
            'title' => $this->title,
        ]);

        $this->showModal = false;
        $this->resetForm();
    session()->flash('success', 'Project saved successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Project saved successfully.');
    }

    public function deleteProject(int $projectId): void
    {
        $project = Project::query()->findOrFail($projectId);
        $title = $project->title;
        $project->delete();

        AdminActivity::log('deleted', 'projects', 'Deleted project record.', [
            'title' => $title,
        ]);
    session()->flash('success', 'Project deleted successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Project deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'projectId',
            'title',
            'description',
            'techStack',
            'demoLink',
            'githubLink',
            'existingImage',
            'projectImage',
        ]);

        $this->categoryId = (string) $this->resolveDefaultCategoryId();
        $this->isFeatured = false;
        $this->isVisible = true;
        $this->sortOrder = 0;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Project::query()
            ->with('portfolioCategory:id,name,slug')
            ->when($this->search !== '', fn ($builder) => $builder->where('title', 'like', '%'.$this->search.'%'))
            ->when($this->categoryFilter !== 'all', fn ($builder) => $builder->where('category_id', (int) $this->categoryFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('id');

        return view('admin.cms.projects-manager', [
            'projects' => $query->paginate(8),
            'categories' => PortfolioCategory::query()->project()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    protected function resolveDefaultCategoryId(): int
    {
        $id = PortfolioCategory::query()
            ->project()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        return $id ? (int) $id : 0;
    }

    protected function resolveCategoryIdByName(?string $value): int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return 0;
        }

        $id = PortfolioCategory::query()
            ->project()
            ->where(function ($query) use ($value): void {
                $query
                    ->whereRaw('LOWER(name) = ?', [strtolower($value)])
                    ->orWhereRaw('LOWER(slug) = ?', [strtolower($value)]);
            })
            ->value('id');

        return $id ? (int) $id : 0;
    }
}
