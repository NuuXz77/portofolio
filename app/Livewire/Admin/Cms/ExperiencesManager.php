<?php

namespace App\Livewire\Admin\Cms;

use App\Models\Experience;
use App\Support\AdminActivity;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ExperiencesManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'sort_order';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public ?int $experienceId = null;

    public string $year = '';

    public string $role = '';

    public string $company = '';

    public string $description = '';

    public int $sortOrder = 0;

    public bool $isVisible = true;

    #[Layout('components.layouts.admin')]
    #[Title('Experience Management')]
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

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $experienceId): void
    {
        $item = Experience::query()->findOrFail($experienceId);

        $this->experienceId = $item->id;
        $this->year = $item->year;
        $this->role = $item->role;
        $this->company = $item->company ?? '';
        $this->description = $item->description;
        $this->sortOrder = $item->sort_order;
        $this->isVisible = $item->is_visible;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'year' => ['required', 'string', 'max:80'],
            'role' => ['required', 'string', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:3000'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isVisible' => ['required', 'boolean'],
        ]);

        Experience::query()->updateOrCreate(
            ['id' => $this->experienceId],
            [
                'year' => $this->year,
                'role' => $this->role,
                'company' => $this->company ?: null,
                'description' => $this->description,
                'sort_order' => $this->sortOrder,
                'is_visible' => $this->isVisible,
            ]
        );

        AdminActivity::log('saved', 'experiences', 'Saved experience record.', [
            'role' => $this->role,
        ]);

        $this->showModal = false;
        $this->resetForm();

        session()->flash('success', 'Experience saved successfully.');
    }

    public function delete(int $experienceId): void
    {
        $item = Experience::query()->findOrFail($experienceId);
        $role = $item->role;
        $item->delete();

        AdminActivity::log('deleted', 'experiences', 'Deleted experience record.', [
            'role' => $role,
        ]);

        session()->flash('success', 'Experience deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset(['experienceId', 'year', 'role', 'company', 'description']);
        $this->sortOrder = 0;
        $this->isVisible = true;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Experience::query()
            ->when($this->search !== '', function ($builder) {
                $builder->where(function ($inner) {
                    $inner->where('role', 'like', '%'.$this->search.'%')
                        ->orWhere('year', 'like', '%'.$this->search.'%')
                        ->orWhere('company', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('id');

        return view('admin.cms.experiences-manager', [
            'experiences' => $query->paginate(10),
        ]);
    }
}
