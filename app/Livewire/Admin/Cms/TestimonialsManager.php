<?php

namespace App\Livewire\Admin\Cms;

use App\Models\Testimonial;
use App\Support\AdminActivity;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class TestimonialsManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $sortField = 'sort_order';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public ?int $testimonialId = null;

    public string $name = '';

    public string $role = '';

    public string $message = '';

    public int $sortOrder = 0;

    public bool $isVisible = true;

    public ?string $existingAvatar = null;

    public $avatar;

    #[Layout('components.layouts.admin')]
    #[Title('Testimonials')]
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

    public function openEditModal(int $testimonialId): void
    {
        $item = Testimonial::query()->findOrFail($testimonialId);

        $this->testimonialId = $item->id;
        $this->name = $item->name;
        $this->role = $item->role;
        $this->message = $item->message;
        $this->sortOrder = $item->sort_order;
        $this->isVisible = $item->is_visible;
        $this->existingAvatar = $item->avatar_path;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'role' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isVisible' => ['required', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:10240'],
        ]);

        $avatarPath = $this->existingAvatar;

        if ($this->avatar) {
            $avatarPath = $this->avatar->store('portfolio/testimonials', 'public');
        }

        Testimonial::query()->updateOrCreate(
            ['id' => $this->testimonialId],
            [
                'name' => $this->name,
                'role' => $this->role,
                'message' => $this->message,
                'avatar_path' => $avatarPath,
                'sort_order' => $this->sortOrder,
                'is_visible' => $this->isVisible,
            ]
        );

        AdminActivity::log('saved', 'testimonials', 'Saved testimonial record.', [
            'name' => $this->name,
        ]);

        $this->showModal = false;
        $this->resetForm();
    session()->flash('success', 'Testimonial saved successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Testimonial saved successfully.');
    }

    public function delete(int $testimonialId): void
    {
        $item = Testimonial::query()->findOrFail($testimonialId);
        $name = $item->name;
        $item->delete();

        AdminActivity::log('deleted', 'testimonials', 'Deleted testimonial record.', [
            'name' => $name,
        ]);
    session()->flash('success', 'Testimonial deleted successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Testimonial deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset(['testimonialId', 'name', 'role', 'message', 'existingAvatar', 'avatar']);
        $this->sortOrder = 0;
        $this->isVisible = true;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Testimonial::query()
            ->when($this->search !== '', function ($builder) {
                $builder->where(function ($inner) {
                    $inner->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('role', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('id');

        return view('admin.cms.testimonials-manager', [
            'testimonials' => $query->paginate(10),
        ]);
    }
}
