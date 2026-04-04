<section class="space-y-4">
    @php
        $columns = [
            ['label' => 'Title', 'field' => 'title', 'sortable' => true],
            ['label' => 'Description'],
            ['label' => 'Icon'],
            ['label' => 'Visible'],
            ['label' => 'Order'],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search service" />
        <button wire:click="openCreateModal" class="btn btn-info rounded-xl text-white">Add Service</button>
    </div>

    <x-ui.table
        :columns="$columns"
        :data="$services"
        :sortField="$sortField"
        :sortDirection="$sortDirection"
        emptyMessage="No service data found."
    >
        @foreach ($services as $service)
            <tr wire:key="service-{{ $service->id }}">
                <td>{{ $service->title }}</td>
                <td class="max-w-md truncate">{{ $service->description }}</td>
                <td>{{ $service->icon ?: '-' }}</td>
                <td><span class="badge badge-soft {{ $service->is_visible ? 'badge-success' : 'badge-ghost' }}">{{ $service->is_visible ? 'Yes' : 'No' }}</span></td>
                <td>{{ $service->sort_order }}</td>
                <td class="text-right">
                    <x-ui.dropdown-action>
                        <li><button type="button" wire:click="openEditModal({{ $service->id }})">Edit</button></li>
                        <li><button type="button" wire:click="delete({{ $service->id }})" wire:confirm="Delete this service?" class="text-error">Delete</button></li>
                    </x-ui.dropdown-action>
                </td>
            </tr>
        @endforeach
    </x-ui.table>

    <div>{{ $services->links() }}</div>

    <x-ui.modal :open="$showModal" :title="$serviceId ? 'Edit Service' : 'Add Service'" closeAction="$set('showModal', false)">
        <form wire:submit="save" class="mt-4 grid gap-3">
            <x-ui.input-field label="Title" name="title" wire:model.defer="title" required />
            <x-ui.textarea-field label="Description" name="description" wire:model.defer="description" :rows="4" required />
            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.input-field label="Icon" name="icon" wire:model.defer="icon" placeholder="rocket" />
                <x-ui.input-field label="Sort Order" name="sortOrder" type="number" min="0" wire:model.defer="sortOrder" required />
            </div>
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
