<?php

namespace App\Livewire\Admin\Cms;

use App\Models\Skill;
use App\Support\AdminActivity;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class SkillsManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = 'all';

    public string $sortField = 'sort_order';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public ?int $skillId = null;

    public string $name = '';

    public string $category = 'Frontend';

    public int $level = 80;

    public string $icon = '';

    public int $sortOrder = 0;

    public bool $isVisible = true;

    #[Layout('components.layouts.admin')]
    #[Title('Skills Management')]
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
        $this->showModal = true;
    }

    public function openEditModal(int $skillId): void
    {
        $skill = Skill::query()->findOrFail($skillId);

        $this->skillId = $skill->id;
        $this->name = $skill->name;
        $this->category = $skill->category;
        $this->level = $skill->level;
        $this->icon = $skill->icon ?? '';
        $this->sortOrder = $skill->sort_order;
        $this->isVisible = $skill->is_visible;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'level' => ['required', 'integer', 'min:0', 'max:100'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isVisible' => ['required', 'boolean'],
        ]);

        Skill::query()->updateOrCreate(
            ['id' => $this->skillId],
            [
                'name' => $this->name,
                'category' => $this->category,
                'level' => $this->level,
                'icon' => $this->icon ?: null,
                'sort_order' => $this->sortOrder,
                'is_visible' => $this->isVisible,
            ]
        );

        AdminActivity::log('saved', 'skills', 'Saved skill record.', [
            'name' => $this->name,
        ]);

        $this->showModal = false;
        $this->resetForm();

        session()->flash('success', 'Skill saved successfully.');
    }

    public function delete(int $skillId): void
    {
        $skill = Skill::query()->findOrFail($skillId);
        $name = $skill->name;
        $skill->delete();

        AdminActivity::log('deleted', 'skills', 'Deleted skill record.', [
            'name' => $name,
        ]);

        session()->flash('success', 'Skill deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset(['skillId', 'name', 'icon']);
        $this->category = 'Frontend';
        $this->level = 80;
        $this->sortOrder = 0;
        $this->isVisible = true;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Skill::query()
            ->when($this->search !== '', fn ($builder) => $builder->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->categoryFilter !== 'all', fn ($builder) => $builder->where('category', $this->categoryFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('id');

        return view('admin.cms.skills-manager', [
            'skills' => $query->paginate(10),
            'categories' => Skill::query()->select('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }
}
