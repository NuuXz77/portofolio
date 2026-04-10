<section class="space-y-4">
    @php
        $columns = [
            ['label' => 'Title', 'field' => 'title', 'sortable' => true],
            ['label' => 'Category'],
            ['label' => 'Tech Stack'],
            ['label' => 'Featured'],
            ['label' => 'Visible'],
            ['label' => 'Order'],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2">
            <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search project" />
            <x-ui.select-field name="categoryFilter" wire:model.live="categoryFilter">
                <option value="all">All Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </x-ui.select-field>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.portfolio.categories') }}" wire:navigate class="btn btn-outline rounded-xl">Manage Categories</a>
            <button wire:click="openCreateModal" class="btn btn-info rounded-xl text-white">Add Project</button>
        </div>
    </div>

    <x-ui.table
        :columns="$columns"
        :data="$projects"
        :sortField="$sortField"
        :sortDirection="$sortDirection"
        emptyMessage="No project found."
    >
        @foreach ($projects as $project)
            <tr wire:key="project-{{ $project->id }}">
                <td>{{ $project->title }}</td>
                <td>{{ $project->portfolioCategory?->name ?? $project->category }}</td>
                <td>
                    <div class="flex max-w-xs flex-wrap gap-1">
                        @foreach (($project->tech_stack ?? []) as $tag)
                            <span class="badge badge-outline badge-info">{{ $tag }}</span>
                        @endforeach
                    </div>
                </td>
                <td><span class="badge badge-soft {{ $project->is_featured ? 'badge-info' : 'badge-ghost' }}">{{ $project->is_featured ? 'Yes' : 'No' }}</span></td>
                <td><span class="badge badge-soft {{ $project->is_visible ? 'badge-success' : 'badge-ghost' }}">{{ $project->is_visible ? 'Yes' : 'No' }}</span></td>
                <td>{{ $project->sort_order }}</td>
                <td class="text-right">
                    <x-ui.dropdown-action>
                        <li><button type="button" wire:click="openEditModal({{ $project->id }})">Edit</button></li>
                        <li><button type="button" wire:click="deleteProject({{ $project->id }})" wire:confirm="Delete this project?" class="text-error">Delete</button></li>
                    </x-ui.dropdown-action>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    <div>{{ $projects->links() }}</div>

    <x-ui.modal :open="$showModal" :title="$projectId ? 'Edit Project' : 'Add Project'" maxWidth="max-w-3xl" bodyClass="max-h-[90vh] overflow-y-auto" closeAction="$set('showModal', false)">
        <form wire:submit="save" class="mt-4 grid gap-3">
            <x-ui.input-field label="Title" name="title" wire:model.defer="title" required />

            <x-ui.textarea-field label="Description" name="description" wire:model.defer="description" :rows="4" required />

            <x-ui.input-field label="Tech Stack (comma separated)" name="techStack" wire:model.defer="techStack" placeholder="Laravel, Livewire, MySQL" required />

            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.select-field label="Category" name="categoryId" wire:model.defer="categoryId" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-ui.select-field>

                <x-ui.input-field label="Sort Order" name="sortOrder" type="number" min="0" wire:model.defer="sortOrder" required />
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.input-field label="Demo Link" name="demoLink" wire:model.defer="demoLink" placeholder="https://..." />

                <x-ui.input-field label="GitHub Link" name="githubLink" wire:model.defer="githubLink" placeholder="https://github.com/..." />
            </div>

            <x-ui.file-field label="Thumbnail Image" name="projectImage" wire:model="projectImage" accept="image/*">
                @if ($existingImage)
                    <img src="{{ str_starts_with($existingImage, 'http') ? $existingImage : Storage::url($existingImage) }}" alt="Project preview" class="mt-2 h-40 w-full rounded-xl object-cover">
                @endif
            </x-ui.file-field>

            <div class="grid gap-2 sm:grid-cols-2">
                <label class="label cursor-pointer justify-start gap-2">
                    <input wire:model.defer="isFeatured" type="checkbox" class="checkbox checkbox-info checkbox-sm">
                    <span class="label-text">Featured Project</span>
                </label>

                <label class="label cursor-pointer justify-start gap-2">
                    <input wire:model.defer="isVisible" type="checkbox" class="checkbox checkbox-info checkbox-sm">
                    <span class="label-text">Visible on landing page</span>
                </label>
            </div>

            <div class="modal-action">
                <button type="button" class="btn" wire:click="$set('showModal', false)">Cancel</button>
                <button type="submit" class="btn btn-info text-white">Save</button>
            </div>
        </form>
    </x-ui.modal>
</section>
