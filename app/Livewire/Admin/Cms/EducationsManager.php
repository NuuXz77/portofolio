<?php

namespace App\Livewire\Admin\Cms;

use App\Models\Education;
use App\Support\AdminActivity;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Livewire\WithPagination;

class EducationsManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $sortField = 'start_year';

    public string $sortDirection = 'desc';

    public bool $showModal = false;

    public ?int $educationId = null;

    public string $institutionName = '';

    public string $major = '';

    public string $degree = 'Sarjana';

    public int $startYear = 0;

    public ?int $endYear = null;

    public string $description = '';

    public bool $isActive = true;

    public ?string $existingLogo = null;

    public $educationLogo;

    /**
     * @var array<int, string>
     */
    public array $degreeOptions = [
        'SMK',
        'Diploma',
        'Sarjana',
        'Magister',
        'Doktor',
        'Lainnya',
    ];

    public function mount(): void
    {
        $this->startYear = (int) now()->year;
    }

    #[Layout('components.layouts.admin')]
    #[Title('Education Management')]
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
        $this->sortDirection = $field === 'start_year' ? 'desc' : 'asc';
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal(int $educationId): void
    {
        $education = Education::query()->findOrFail($educationId);

        $this->educationId = $education->id;
        $this->institutionName = $education->institution_name;
        $this->major = $education->major;
        $this->degree = $education->degree;
        $this->startYear = (int) $education->start_year;
        $this->endYear = $education->end_year ? (int) $education->end_year : null;
        $this->description = (string) ($education->description ?? '');
        $this->isActive = $education->is_active;
        $this->existingLogo = $education->logo;
        $this->showModal = true;
    }

    public function save(): void
    {
        $maxYear = (int) now()->year + 10;

        $this->validate([
            'institutionName' => ['required', 'string', 'max:180'],
            'major' => ['required', 'string', 'max:180'],
            'degree' => ['required', 'string', Rule::in($this->degreeOptions)],
            'startYear' => ['required', 'integer', 'min:1900', 'max:'.$maxYear],
            'endYear' => ['nullable', 'integer', 'min:1900', 'max:'.$maxYear, 'gte:startYear'],
            'description' => ['nullable', 'string', 'max:4000'],
            'isActive' => ['required', 'boolean'],
            'educationLogo' => ['nullable', 'image', 'max:10240'],
        ]);

        $logoPath = $this->existingLogo;

        if ($this->educationLogo) {
            $logoPath = $this->educationLogo->store('portfolio/educations', 'public');
        }

        Education::query()->updateOrCreate(
            ['id' => $this->educationId],
            [
                'institution_name' => $this->institutionName,
                'major' => $this->major,
                'degree' => $this->degree,
                'start_year' => $this->startYear,
                'end_year' => $this->endYear,
                'description' => trim($this->description) !== '' ? $this->description : null,
                'logo' => $logoPath,
                'is_active' => $this->isActive,
            ]
        );

        AdminActivity::log('saved', 'educations', 'Saved education record.', [
            'institution_name' => $this->institutionName,
        ]);

        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', 'Education saved successfully.');
        $this->dispatch('app-toast', type: 'success', message: 'Education saved successfully.');
    }

    public function delete(int $educationId): void
    {
        $education = Education::query()->findOrFail($educationId);
        $institutionName = $education->institution_name;
        $education->delete();

        AdminActivity::log('deleted', 'educations', 'Deleted education record.', [
            'institution_name' => $institutionName,
        ]);

        session()->flash('success', 'Education deleted successfully.');
        $this->dispatch('app-toast', type: 'success', message: 'Education deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'educationId',
            'institutionName',
            'major',
            'description',
            'existingLogo',
            'educationLogo',
        ]);

        $this->degree = 'Sarjana';
        $this->startYear = (int) now()->year;
        $this->endYear = null;
        $this->isActive = true;
        $this->resetValidation();
    }

    public function render()
    {
        $educations = Education::query()
            ->when($this->search !== '', function ($builder): void {
                $builder->where(function ($inner): void {
                    $inner
                        ->where('institution_name', 'like', '%'.$this->search.'%')
                        ->orWhere('major', 'like', '%'.$this->search.'%')
                        ->orWhere('degree', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.cms.educations-manager', [
            'educations' => $educations,
        ]);
    }
}
