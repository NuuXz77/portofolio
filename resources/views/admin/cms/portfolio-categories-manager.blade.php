<section class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search category" />

            <x-ui.select-field name="typeFilter" wire:model.live="typeFilter">
                <option value="all">All Type</option>
                <option value="skill">Skill</option>
                <option value="project">Project</option>
            </x-ui.select-field>
        </div>

        <button type="button" wire:click="openCreateModal" class="btn btn-info rounded-xl text-white">Add Category</button>
    </div>

    @php
        $columns = [
            ['label' => 'Name', 'field' => 'name', 'sortable' => true],
            ['label' => 'Slug', 'field' => 'slug', 'sortable' => true],
            ['label' => 'Type', 'field' => 'type', 'sortable' => true],
            ['label' => 'Description'],
            ['label' => 'Usage'],
            ['label' => 'Visible'],
            ['label' => 'Order', 'field' => 'sort_order', 'sortable' => true],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <x-ui.table
        :columns="$columns"
        :data="$categories"
        :sortField="$sortField"
        :sortDirection="$sortDirection"
        emptyMessage="No categories yet"
        emptySubMessage="Add categories to relate skills and projects"
        emptyIcon="folder-tree"
    >
        @foreach ($categories as $category)
            <tr wire:key="portfolio-category-{{ $category->id }}">
                <td class="font-medium">{{ $category->name }}</td>
                <td class="text-base-content/70">{{ $category->slug }}</td>
                <td>
                    <span class="badge badge-outline {{ $category->type === 'skill' ? 'badge-info' : 'badge-success' }}">
                        {{ ucfirst($category->type) }}
                    </span>
                </td>
                <td class="max-w-md text-sm text-base-content/70">{{ $category->description ?: '-' }}</td>
                <td>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="badge badge-ghost">Skills: {{ $category->skills_count }}</span>
                        <span class="badge badge-ghost">Projects: {{ $category->projects_count }}</span>
                    </div>
                </td>
                <td>
                    <span class="badge badge-soft {{ $category->is_visible ? 'badge-success' : 'badge-ghost' }}">{{ $category->is_visible ? 'Yes' : 'No' }}</span>
                </td>
                <td>{{ $category->sort_order }}</td>
                <td class="text-right">
                    <x-ui.dropdown-action>
                        <li><button type="button" wire:click="openEditModal({{ $category->id }})">Edit</button></li>
                        <li><button type="button" wire:click="deleteCategory({{ $category->id }})" wire:confirm="Delete this category?" class="text-error">Delete</button></li>
                    </x-ui.dropdown-action>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    <div>{{ $categories->links() }}</div>

    <x-ui.modal :open="$showModal" :title="$categoryId ? 'Edit Category' : 'Add Category'" closeAction="$set('showModal', false)">
        <form wire:submit="save" class="mt-4 grid gap-3">
            <x-ui.input-field label="Name" name="name" wire:model.live="name" required />
            <x-ui.input-field label="Slug" name="slug" wire:model.defer="slug" required />

            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.select-field label="Type" name="type" wire:model.defer="type" required>
                    <option value="skill">Skill</option>
                    <option value="project">Project</option>
                </x-ui.select-field>

                <x-ui.input-field label="Sort Order" name="sortOrder" type="number" min="0" wire:model.defer="sortOrder" required />
            </div>

            <x-ui.textarea-field label="Description" name="description" wire:model.defer="description" :rows="3" />

            <label class="label cursor-pointer justify-start gap-2">
                <input wire:model.defer="isVisible" type="checkbox" class="checkbox checkbox-info checkbox-sm">
                <span class="label-text">Visible</span>
            </label>

            <div class="modal-action">
                <button type="button" class="btn" wire:click="$set('showModal', false)">Cancel</button>
                <button type="submit" class="btn btn-info text-white">Save</button>
            </div>
        </form>
    </x-ui.modal>
</section>
