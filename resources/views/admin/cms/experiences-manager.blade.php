<section class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search year, role, company" />
        <button wire:click="openCreateModal" class="btn btn-info rounded-xl text-white">Add Experience</button>
    </div>

    @php
        $columns = [
            ['label' => 'No', 'class' => 'w-16 text-center'],
            ['label' => 'Year', 'field' => 'year', 'sortable' => true],
            ['label' => 'Role', 'field' => 'role', 'sortable' => true],
            ['label' => 'Company/Type'],
            ['label' => 'Description'],
            ['label' => 'Visible'],
            ['label' => 'Order', 'field' => 'sort_order', 'sortable' => true],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <x-ui.table
        :columns="$columns"
        :data="$experiences"
        :sortField="$sortField"
        :sortDirection="$sortDirection"
        emptyMessage="Belum ada data experience."
    >
        @foreach ($experiences as $index => $experience)
            <tr wire:key="experience-{{ $experience->id }}" class="hover:bg-base-200/50 transition-colors">
                <td class="text-center font-medium text-base-content/50">{{ $experiences->firstItem() + $index }}</td>
                <td>{{ $experience->year }}</td>
                <td>{{ $experience->role }}</td>
                <td>{{ $experience->company ?: '-' }}</td>
                <td class="max-w-md truncate">{{ $experience->description }}</td>
                <td><span class="badge badge-soft {{ $experience->is_visible ? 'badge-success' : 'badge-ghost' }}">{{ $experience->is_visible ? 'Yes' : 'No' }}</span></td>
                <td>{{ $experience->sort_order }}</td>
                <td class="text-right">
                    <x-ui.dropdown-action>
                        <li><button type="button" wire:click="openEditModal({{ $experience->id }})">Edit</button></li>
                        <li><button type="button" wire:click="delete({{ $experience->id }})" wire:confirm="Delete this experience?" class="text-error">Delete</button></li>
                    </x-ui.dropdown-action>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    <div>{{ $experiences->links() }}</div>

    <x-ui.modal :open="$showModal" :title="$experienceId ? 'Edit Experience' : 'Add Experience'" closeAction="$set('showModal', false)">
        <form wire:submit="save" class="mt-4 grid gap-3">
            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.input-field label="Year" name="year" wire:model.defer="year" placeholder="2024 - Now" required />
                <x-ui.input-field label="Role" name="role" wire:model.defer="role" required />
            </div>

            <x-ui.input-field label="Company / Type" name="company" wire:model.defer="company" />

            <x-ui.textarea-field label="Description" name="description" wire:model.defer="description" :rows="4" required />

            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.input-field label="Sort Order" name="sortOrder" type="number" min="0" wire:model.defer="sortOrder" required />
                <label class="label cursor-pointer justify-start gap-2 pt-8">
                    <input wire:model.defer="isVisible" type="checkbox" class="checkbox checkbox-info checkbox-sm">
                    <span class="label-text">Visible</span>
                </label>
            </div>

            <div class="modal-action">
                <button type="button" class="btn" wire:click="$set('showModal', false)">Cancel</button>
                <button type="submit" class="btn btn-info text-white">Save</button>
            </div>
        </form>
    </x-ui.modal>
</section>
