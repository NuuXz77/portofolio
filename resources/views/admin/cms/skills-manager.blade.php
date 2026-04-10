<section class="space-y-4">
    @php
        $columns = [
            ['label' => 'Name', 'field' => 'name', 'sortable' => true],
            ['label' => 'Category', 'field' => 'category', 'sortable' => true],
            ['label' => 'Level', 'field' => 'level', 'sortable' => true],
            ['label' => 'Icon'],
            ['label' => 'Order', 'field' => 'sort_order', 'sortable' => true],
            ['label' => 'Visible'],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search skill" />
            <x-ui.select-field name="categoryFilter" wire:model.live="categoryFilter">
                <option value="all">All Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </x-ui.select-field>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.portfolio.categories') }}" wire:navigate class="btn btn-outline rounded-xl">Manage Categories</a>
            <button wire:click="openCreateModal" class="btn btn-info rounded-xl text-white">Add Skill</button>
        </div>
    </div>

    <x-ui.table
        :columns="$columns"
        :data="$skills"
        :sortField="$sortField"
        :sortDirection="$sortDirection"
        emptyMessage="No skill found."
    >
        @foreach ($skills as $skill)
            <tr wire:key="skill-{{ $skill->id }}">
                <td>{{ $skill->name }}</td>
                <td>{{ $skill->portfolioCategory?->name ?? $skill->category }}</td>
                <td>{{ $skill->level }}%</td>
                <td>
                    @if ($skill->icon)
                        <div class="inline-flex items-center gap-2 rounded-xl border border-base-content/15 bg-base-100/70 px-2 py-1">
                            @if (str_starts_with((string) $skill->icon, 'si:'))
                                @php
                                    $parts = explode(':', (string) $skill->icon, 3);
                                    $slug = $parts[1] ?? '';
                                    $hex = strtoupper($parts[2] ?? '000000');
                                @endphp
                                @if ($slug !== '')
                                    <img
                                        src="{{ $simpleIconsCdn }}/{{ $slug }}/{{ $hex }}"
                                        alt="{{ $slug }}"
                                        class="h-4 w-4"
                                        loading="lazy"
                                    >
                                @else
                                    <i data-lucide="puzzle" class="h-4 w-4 text-info"></i>
                                @endif
                            @else
                                <i data-lucide="{{ $skill->icon }}" class="h-4 w-4 text-info"></i>
                            @endif
                            <span class="text-xs text-base-content/70">{{ $skill->icon }}</span>
                        </div>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $skill->sort_order }}</td>
                <td><span class="badge badge-soft {{ $skill->is_visible ? 'badge-success' : 'badge-ghost' }}">{{ $skill->is_visible ? 'Yes' : 'No' }}</span></td>
                <td class="text-right">
                    <x-ui.dropdown-action>
                        <li><button type="button" wire:click="openEditModal({{ $skill->id }})">Edit</button></li>
                        <li><button type="button" wire:click="delete({{ $skill->id }})" wire:confirm="Delete this skill?" class="text-error">Delete</button></li>
                    </x-ui.dropdown-action>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    <div>{{ $skills->links() }}</div>

    <x-ui.modal :open="$showModal" :title="$skillId ? 'Edit Skill' : 'Add Skill'" maxWidth="max-w-xl" closeAction="$set('showModal', false)">
        <form wire:submit="save" class="mt-4 grid gap-3">
            <x-ui.input-field label="Name" name="name" wire:model.defer="name" required />

            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.select-field label="Category" name="categoryId" wire:model.defer="categoryId" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-ui.select-field>
                <x-ui.input-field label="Level (%)" name="level" type="number" min="0" max="100" wire:model.defer="level" required />
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="space-y-2">
                    <x-ui.input-field label="Icon" name="icon" wire:model.live.debounce.250ms="icon" placeholder="layout-template" />

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 rounded-xl border border-base-content/15 bg-base-100/70 px-2.5 py-1.5">
                            @if ($icon !== '' && str_starts_with((string) $icon, 'si:'))
                                @php
                                    $parts = explode(':', (string) $icon, 3);
                                    $slug = $parts[1] ?? '';
                                    $hex = strtoupper($parts[2] ?? '000000');
                                @endphp
                                @if ($slug !== '')
                                    <img
                                        src="{{ $simpleIconsCdn }}/{{ $slug }}/{{ $hex }}"
                                        alt="{{ $slug }}"
                                        class="h-4 w-4"
                                        loading="lazy"
                                    >
                                @else
                                    <i data-lucide="sparkles" class="h-4 w-4 text-info"></i>
                                @endif
                            @else
                                <i data-lucide="{{ $icon !== '' ? $icon : 'sparkles' }}" class="h-4 w-4 text-info"></i>
                            @endif
                            <span class="text-xs text-base-content/70">{{ $icon !== '' ? $icon : 'No icon selected' }}</span>
                        </span>

                        <button type="button" class="btn btn-outline btn-sm rounded-xl" wire:click="openIconPicker">
                            Search Icon
                        </button>
                    </div>

                    @error('icon')
                        <span class="text-xs text-error">{{ $message }}</span>
                    @enderror
                </div>
                <x-ui.input-field label="Sort Order" name="sortOrder" type="number" min="0" wire:model.defer="sortOrder" required />
            </div>

            <label class="label cursor-pointer justify-start gap-2">
                <input wire:model.defer="isVisible" type="checkbox" class="checkbox checkbox-info checkbox-sm">
                <span class="label-text">Visible on landing page</span>
            </label>

            <div class="modal-action">
                <button type="button" class="btn" wire:click="$set('showModal', false)">Cancel</button>
                <button type="submit" class="btn btn-info text-white">Save</button>
            </div>
        </form>
    </x-ui.modal>

    <x-ui.modal :open="$showIconPicker" title="Icon Library" maxWidth="max-w-3xl" closeAction="closeIconPicker">
        <div class="mt-4 space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-base-content/60">
                <span>Total icon tersedia: {{ $this->technologyIconLibraryTotal + $this->iconLibraryTotal }}</span>
                <span>Ketik keyword untuk hasil yang lebih spesifik.</span>
            </div>

            <x-ui.input-field
                label="Search Icon"
                name="iconSearch"
                wire:model.live.debounce.200ms="iconSearch"
                placeholder="e.g. server, code, database"
            />

            <div class="space-y-2 rounded-xl border border-base-content/10 bg-base-100/60 p-2">
                <div class="flex flex-wrap items-center justify-between gap-2 px-1 text-xs text-base-content/60">
                    <span>Technology Icons: {{ count($this->filteredTechnologyIcons) }} / {{ $this->technologyIconLibraryTotal }}</span>
                    <span>Sumber dari config technology-icons.php</span>
                </div>

                <div class="grid max-h-56 grid-cols-2 gap-2 overflow-y-auto sm:grid-cols-3 lg:grid-cols-4">
                    @forelse ($this->filteredTechnologyIcons as $tech)
                        @php
                            $techColor = strtoupper($tech['color'] ?? '000000');
                            $techValue = 'si:'.$tech['slug'].':'.$techColor;
                        @endphp
                        <button
                            type="button"
                            wire:click="selectTechnologyIcon('{{ $tech['slug'] }}', '{{ $techColor }}')"
                            class="group flex items-center gap-2 rounded-xl border px-2 py-2 text-left text-xs transition {{ $icon === $techValue ? 'border-info/60 bg-info/10 text-info' : 'border-base-content/10 bg-base-100/70 text-base-content/75 hover:border-info/40 hover:bg-info/8' }}"
                        >
                            <img
                                src="{{ $simpleIconsCdn }}/{{ $tech['slug'] }}/{{ $techColor }}"
                                alt="{{ $tech['name'] }}"
                                class="h-4 w-4 shrink-0"
                                loading="lazy"
                            >
                            <span class="truncate">{{ $tech['name'] }}</span>
                        </button>
                    @empty
                        <p class="col-span-full px-2 py-3 text-sm text-base-content/60">No technology icon found. Try another keyword.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-base-content/10 bg-base-100/60 p-2">
                <div class="flex flex-wrap items-center justify-between gap-2 px-1 pb-2 text-xs text-base-content/60">
                    <span>Lucide Icons: {{ count($this->filteredIconLibrary) }} / {{ $this->iconLibraryTotal }}</span>
                </div>
                <div class="grid max-h-72 grid-cols-2 gap-2 overflow-y-auto sm:grid-cols-3 lg:grid-cols-4">
                    @forelse ($this->filteredIconLibrary as $iconName)
                        <button
                            type="button"
                            wire:click="selectIcon('{{ $iconName }}')"
                            class="group flex items-center gap-2 rounded-xl border px-2 py-2 text-left text-xs transition {{ $icon === $iconName ? 'border-info/60 bg-info/10 text-info' : 'border-base-content/10 bg-base-100/70 text-base-content/75 hover:border-info/40 hover:bg-info/8' }}"
                        >
                            <i data-lucide="{{ $iconName }}" class="h-4 w-4 shrink-0 {{ $icon === $iconName ? 'text-info' : 'text-base-content/70 group-hover:text-info' }}"></i>
                            <span class="truncate">{{ $iconName }}</span>
                        </button>
                    @empty
                        <p class="col-span-full px-2 py-3 text-sm text-base-content/60">No icon found. Try another keyword.</p>
                    @endforelse
                </div>
            </div>

            @if (trim($iconSearch) === '')
                <p class="text-xs text-base-content/55">Menampilkan 240 icon awal. Gunakan search untuk melihat semua icon.</p>
            @endif
        </div>
    </x-ui.modal>
</section>
