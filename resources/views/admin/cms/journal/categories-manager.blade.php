<section class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search category" />
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.journal.index') }}" wire:navigate class="btn btn-outline rounded-xl">Back to Articles</a>
            <button type="button" wire:click="openCreateModal" class="btn btn-info rounded-xl text-white">Add Category</button>
        </div>
    </div>

    @php
        $columns = [
            ['label' => 'Name', 'field' => 'name', 'sortable' => true],
            ['label' => 'Slug', 'field' => 'slug', 'sortable' => true],
            ['label' => 'Description'],
            ['label' => 'Articles'],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <x-ui.table
        :columns="$columns"
        :data="$categories"
        :sortField="$sortField"
        :sortDirection="$sortDirection"
        emptyMessage="No categories yet"
        emptySubMessage="Create categories for article grouping"
        emptyIcon="folder-tree"
    >
        @foreach ($categories as $category)
            <tr wire:key="journal-category-{{ $category->id }}">
                <td class="font-medium">{{ $category->name }}</td>
                <td class="text-base-content/70">{{ $category->slug }}</td>
                <td class="max-w-md text-sm text-base-content/70">{{ $category->description ?: '-' }}</td>
                <td>
                    <span class="badge badge-outline">{{ $category->articles_count }}</span>
                </td>
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
            <x-ui.textarea-field label="Description" name="description" wire:model.defer="description" :rows="3" />

            <div class="modal-action">
                <button type="button" class="btn" wire:click="$set('showModal', false)">Cancel</button>
                <button type="submit" class="btn btn-info text-white">Save</button>
            </div>
        </form>
    </x-ui.modal>
</section>
