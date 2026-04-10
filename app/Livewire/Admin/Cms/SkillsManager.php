<?php

namespace App\Livewire\Admin\Cms;

use App\Models\PortfolioCategory;
use App\Models\Skill;
use App\Support\AdminActivity;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class SkillsManager extends Component
{
    use WithPagination;

    /**
     * Fallback icons if generated config is unavailable.
     *
     * @var array<int, string>
     */
    protected array $iconLibrary = [
        'activity',
        'app-window',
        'arrow-up',
        'arrow-up-right',
        'binary',
        'bot',
        'box',
        'boxes',
        'briefcase',
        'bug',
        'calendar',
        'check-circle-2',
        'chevron-down',
        'chevron-left',
        'chevron-right',
        'chevron-up',
        'cloud',
        'cloud-cog',
        'code',
        'code-2',
        'code-xml',
        'component',
        'container',
        'cpu',
        'database',
        'folder',
        'git-branch',
        'globe',
        'hard-drive',
        'key-round',
        'layers-3',
        'layout-dashboard',
        'layout-grid',
        'layout-template',
        'lock',
        'mail',
        'menu',
        'message-square',
        'monitor',
        'moon',
        'network',
        'notebook',
        'package',
        'panels-top-left',
        'quote',
        'rocket',
        'server',
        'server-cog',
        'settings',
        'shield-check',
        'smartphone',
        'sparkles',
        'star',
        'sun',
        'terminal',
        'test-tube',
        'waypoints',
        'workflow',
        'wrench',
        'x',
    ];

    /**
     * @var array<int, array{name: string, slug: string, color: string}>
     */
    protected array $technologyIconFallback = [
        ['name' => 'React', 'slug' => 'react', 'color' => '61DAFB'],
        ['name' => 'Next.js', 'slug' => 'nextdotjs', 'color' => '000000'],
        ['name' => 'Vue.js', 'slug' => 'vuedotjs', 'color' => '4FC08D'],
        ['name' => 'Nuxt', 'slug' => 'nuxtdotjs', 'color' => '00DC82'],
        ['name' => 'Laravel', 'slug' => 'laravel', 'color' => 'FF2D20'],
        ['name' => 'Livewire', 'slug' => 'livewire', 'color' => 'FB70A9'],
        ['name' => 'PHP', 'slug' => 'php', 'color' => '777BB4'],
        ['name' => 'Node.js', 'slug' => 'nodedotjs', 'color' => '339933'],
        ['name' => 'Express', 'slug' => 'express', 'color' => '000000'],
        ['name' => 'JavaScript', 'slug' => 'javascript', 'color' => 'F7DF1E'],
        ['name' => 'TypeScript', 'slug' => 'typescript', 'color' => '3178C6'],
        ['name' => 'Tailwind CSS', 'slug' => 'tailwindcss', 'color' => '06B6D4'],
        ['name' => 'MySQL', 'slug' => 'mysql', 'color' => '4479A1'],
        ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'color' => '4169E1'],
        ['name' => 'MongoDB', 'slug' => 'mongodb', 'color' => '47A248'],
        ['name' => 'Redis', 'slug' => 'redis', 'color' => 'DC382D'],
        ['name' => 'Docker', 'slug' => 'docker', 'color' => '2496ED'],
        ['name' => 'Kubernetes', 'slug' => 'kubernetes', 'color' => '326CE5'],
        ['name' => 'Git', 'slug' => 'git', 'color' => 'F05032'],
        ['name' => 'GitHub', 'slug' => 'github', 'color' => '181717'],
        ['name' => 'Linux', 'slug' => 'linux', 'color' => 'FCC624'],
        ['name' => 'Nginx', 'slug' => 'nginx', 'color' => '009639'],
        ['name' => 'AWS', 'slug' => 'amazonwebservices', 'color' => 'FF9900'],
        ['name' => 'Figma', 'slug' => 'figma', 'color' => 'F24E1E'],
    ];

    public string $search = '';

    public string $categoryFilter = 'all';

    public string $sortField = 'sort_order';

    public string $sortDirection = 'asc';

    public bool $showModal = false;

    public bool $showIconPicker = false;

    public ?int $skillId = null;

    public string $name = '';

    public string $categoryId = '';

    public int $level = 80;

    public string $icon = '';

    public string $iconSearch = '';

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
        if ($this->categoryId === '') {
            $this->categoryId = (string) $this->resolveDefaultCategoryId();
        }
        $this->showModal = true;
    }

    public function openEditModal(int $skillId): void
    {
        $skill = Skill::query()->findOrFail($skillId);

        $this->skillId = $skill->id;
        $this->name = $skill->name;
        $this->categoryId = (string) ($skill->category_id ?: $this->resolveCategoryIdByName($skill->category));
        $this->level = $skill->level;
        $this->icon = $skill->icon ?? '';
        $this->sortOrder = $skill->sort_order;
        $this->isVisible = $skill->is_visible;
        $this->showIconPicker = false;
        $this->iconSearch = '';
        $this->showModal = true;
    }

    public function updatedIcon(string $value): void
    {
        $this->icon = strtolower(trim($value));
    }

    public function openIconPicker(): void
    {
        $this->iconSearch = '';
        $this->showIconPicker = true;
    }

    public function closeIconPicker(): void
    {
        $this->showIconPicker = false;
    }

    public function selectIcon(string $icon): void
    {
        if (! in_array($icon, $this->resolveIconLibrary(), true)) {
            return;
        }

        $this->icon = $icon;
        $this->showIconPicker = false;
    }

    public function selectTechnologyIcon(string $slug, string $color): void
    {
        $slug = strtolower(trim($slug));
        $color = strtoupper(trim($color));

        if ($slug === '') {
            return;
        }

        $icon = $this->findTechnologyIcon($slug);

        if (! $icon) {
            return;
        }

        $resolvedColor = $color !== '' ? $color : ($icon['color'] ?? '000000');
        $this->icon = 'si:'.$slug.':'.$resolvedColor;
        $this->showIconPicker = false;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'categoryId' => [
                'required',
                'integer',
                Rule::exists('portfolio_categories', 'id')->where('type', 'skill'),
            ],
            'level' => ['required', 'integer', 'min:0', 'max:100'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isVisible' => ['required', 'boolean'],
        ]);

        $category = PortfolioCategory::query()
            ->skill()
            ->findOrFail((int) $this->categoryId);

        $normalizedIcon = strtolower(trim($this->icon));

        if ($normalizedIcon !== '') {
            if (str_starts_with($normalizedIcon, 'si:')) {
                $parts = explode(':', $normalizedIcon, 3);
                $slug = $parts[1] ?? '';
                $color = strtoupper($parts[2] ?? '');

                if ($slug === '' || ! $this->findTechnologyIcon($slug)) {
                    $this->addError('icon', 'Brand icon tidak valid. Pilih dari Technology Icon section.');

                    return;
                }

                if ($color === '') {
                    $color = strtoupper($this->findTechnologyIcon($slug)['color'] ?? '000000');
                }

                $normalizedIcon = 'si:'.$slug.':'.$color;
            } elseif ($this->findTechnologyIcon($normalizedIcon)) {
                $tech = $this->findTechnologyIcon($normalizedIcon);
                $normalizedIcon = 'si:'.$tech['slug'].':'.strtoupper($tech['color'] ?? '000000');
            } elseif (! in_array($normalizedIcon, $this->resolveIconLibrary(), true)) {
                $this->addError('icon', 'Icon tidak valid. Pilih dari Icon Library agar pasti tampil.');

                return;
            }
        }

        Skill::query()->updateOrCreate(
            ['id' => $this->skillId],
            [
                'name' => $this->name,
                'category' => $category->name,
                'category_id' => $category->id,
                'level' => $this->level,
                'icon' => $normalizedIcon !== '' ? $normalizedIcon : null,
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
    $this->dispatch('app-toast', type: 'success', message: 'Skill saved successfully.');
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
    $this->dispatch('app-toast', type: 'success', message: 'Skill deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->reset(['skillId', 'name', 'icon', 'showIconPicker', 'iconSearch']);
        $this->categoryId = (string) $this->resolveDefaultCategoryId();
        $this->level = 80;
        $this->sortOrder = 0;
        $this->isVisible = true;
        $this->resetValidation();
    }

    public function getFilteredIconLibraryProperty(): array
    {
        $library = $this->resolveIconLibrary();
        $query = strtolower(trim($this->iconSearch));

        if ($query === '') {
            return array_slice($library, 0, 240);
        }

        return array_values(array_filter(
            $library,
            static fn (string $icon): bool => str_contains($icon, $query)
        ));
    }

    public function getFilteredTechnologyIconsProperty(): array
    {
        $library = $this->resolveTechnologyIconLibrary();
        $query = strtolower(trim($this->iconSearch));

        if ($query === '') {
            return $library;
        }

        return array_values(array_filter(
            $library,
            static fn (array $icon): bool => str_contains(strtolower($icon['name']), $query)
                || str_contains(strtolower($icon['slug']), $query)
        ));
    }

    public function getIconLibraryTotalProperty(): int
    {
        return count($this->resolveIconLibrary());
    }

    public function getTechnologyIconLibraryTotalProperty(): int
    {
        return count($this->resolveTechnologyIconLibrary());
    }

    protected function resolveIconLibrary(): array
    {
        $icons = config('lucide-icons');

        if (is_array($icons) && $icons !== []) {
            return $icons;
        }

        return $this->iconLibrary;
    }

    /**
     * @return array<int, array{name: string, slug: string, color: string}>
     */
    protected function resolveTechnologyIconLibrary(): array
    {
        $icons = config('technology-icons');

        if (is_array($icons) && $icons !== []) {
            return array_values(array_filter($icons, static fn ($item): bool => is_array($item)
                && isset($item['name'], $item['slug'])
                && $item['name'] !== ''
                && $item['slug'] !== ''));
        }

        return $this->technologyIconFallback;
    }

    /**
     * @return array{name: string, slug: string, color: string}|null
     */
    protected function findTechnologyIcon(string $slug): ?array
    {
        $slug = strtolower(trim($slug));

        if ($slug === '') {
            return null;
        }

        foreach ($this->resolveTechnologyIconLibrary() as $icon) {
            if (strtolower((string) $icon['slug']) === $slug) {
                return [
                    'name' => (string) $icon['name'],
                    'slug' => (string) $icon['slug'],
                    'color' => strtoupper((string) ($icon['color'] ?? '000000')),
                ];
            }
        }

        return null;
    }

    public function render()
    {
        $query = Skill::query()
            ->with('portfolioCategory:id,name')
            ->when($this->search !== '', fn ($builder) => $builder->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->categoryFilter !== 'all', fn ($builder) => $builder->where('category_id', (int) $this->categoryFilter))
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('id');

        return view('admin.cms.skills-manager', [
            'skills' => $query->paginate(10),
            'categories' => PortfolioCategory::query()->skill()->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
        ]);
    }

    protected function resolveDefaultCategoryId(): int
    {
        $id = PortfolioCategory::query()
            ->skill()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        return $id ? (int) $id : 0;
    }

    protected function resolveCategoryIdByName(?string $name): int
    {
        $name = trim((string) $name);

        if ($name === '') {
            return 0;
        }

        $id = PortfolioCategory::query()
            ->skill()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->value('id');

        return $id ? (int) $id : 0;
    }
}
