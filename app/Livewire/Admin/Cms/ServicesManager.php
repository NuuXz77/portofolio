<?php

namespace App\Livewire\Admin\Cms;

use App\Models\Service;
use App\Support\AdminActivity;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ServicesManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'sort_order';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public ?int $serviceId = null;

    public string $title = '';

    public string $description = '';

    public string $icon = '';

    public int $sortOrder = 0;

    public bool $isVisible = true;

    #[Layout('components.layouts.admin')]
    #[Title('Services Management')]
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

    public function openEditModal(int $serviceId): void
    {
        $item = Service::query()->findOrFail($serviceId);

        $this->serviceId = $item->id;
        $this->title = $item->title;
        $this->description = $item->description;
        $this->icon = $item->icon ?? '';
        $this->sortOrder = $item->sort_order;
        $this->isVisible = $item->is_visible;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:2500'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isVisible' => ['required', 'boolean'],
        ]);

        Service::query()->updateOrCreate(
            ['id' => $this->serviceId],
            [
                'title' => $this->title,
                'description' => $this->description,
                'icon' => $this->icon ?: null,
                'sort_order' => $this->sortOrder,
                'is_visible' => $this->isVisible,
            ]
        );

        AdminActivity::log('saved', 'services', 'Saved service record.', [
            'title' => $this->title,
        ]);

        $this->showModal = false;
        $this->resetForm();
    session()->flash('success', 'Service saved successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Service saved successfully.');
    }

    public function delete(int $serviceId): void
    {
        $item = Service::query()->findOrFail($serviceId);
        $title = $item->title;
        $item->delete();

        AdminActivity::log('deleted', 'services', 'Deleted service record.', [
            'title' => $title,
        ]);
    session()->flash('success', 'Service deleted successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Service deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset(['serviceId', 'title', 'description', 'icon']);
        $this->sortOrder = 0;
        $this->isVisible = true;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Service::query()
            ->when($this->search !== '', fn ($builder) => $builder->where('title', 'like', '%'.$this->search.'%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('id');

        return view('admin.cms.services-manager', [
            'services' => $query->paginate(10),
        ]);
    }
}
