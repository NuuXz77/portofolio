<section class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search skill" />
            <x-ui.select-field name="categoryFilter" wire:model.live="categoryFilter">
                <option value="all">All Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}">{{ $category }}</option>
                @endforeach
            </x-ui.select-field>
        </div>
        <button wire:click="openCreateModal" class="btn btn-info rounded-xl text-white">Add Skill</button>
    </div>

    <x-ui.table>
        <x-slot:head>
                <tr>
                    <th><button wire:click="sortBy('name')" class="btn btn-ghost btn-xs">Name</button></th>
                    <th><button wire:click="sortBy('category')" class="btn btn-ghost btn-xs">Category</button></th>
                    <th><button wire:click="sortBy('level')" class="btn btn-ghost btn-xs">Level</button></th>
                    <th>Icon</th>
                    <th><button wire:click="sortBy('sort_order')" class="btn btn-ghost btn-xs">Order</button></th>
                    <th>Visible</th>
                    <th class="text-right">Actions</th>
                </tr>
        </x-slot:head>

        @forelse ($skills as $skill)
            <tr>
                <td>{{ $skill->name }}</td>
                <td>{{ $skill->category }}</td>
                <td>{{ $skill->level }}%</td>
                <td>{{ $skill->icon ?: '-' }}</td>
                <td>{{ $skill->sort_order }}</td>
                <td><span class="badge {{ $skill->is_visible ? 'badge-success' : 'badge-ghost' }}">{{ $skill->is_visible ? 'Yes' : 'No' }}</span></td>
                <td class="text-right">
                    <x-ui.dropdown-action>
                        <li><button type="button" wire:click="openEditModal({{ $skill->id }})">Edit</button></li>
                        <li><button type="button" wire:click="delete({{ $skill->id }})" wire:confirm="Delete this skill?" class="text-error">Delete</button></li>
                    </x-ui.dropdown-action>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-base-content/55">No skill found.</td>
            </tr>
        @endforelse
    </x-ui.table>

    <div>{{ $skills->links() }}</div>

    <x-ui.modal :open="$showModal" :title="$skillId ? 'Edit Skill' : 'Add Skill'" maxWidth="max-w-xl" closeAction="$set('showModal', false)">
        <form wire:submit="save" class="mt-4 grid gap-3">
            <x-ui.input-field label="Name" name="name" wire:model.defer="name" required />

            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.input-field label="Category" name="category" wire:model.defer="category" required />
                <x-ui.input-field label="Level (%)" name="level" type="number" min="0" max="100" wire:model.defer="level" required />
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.input-field label="Icon" name="icon" wire:model.defer="icon" placeholder="layout-template" />
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
</section>
