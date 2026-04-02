<section class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <x-ui.input-field name="search" wire:model.live.debounce.300ms="search" placeholder="Search testimonial" />
        <button wire:click="openCreateModal" class="btn btn-info rounded-xl text-white">Add Testimonial</button>
    </div>

    <x-ui.table>
        <x-slot:head>
                <tr>
                    <th><button wire:click="sortBy('name')" class="btn btn-ghost btn-xs">Name</button></th>
                    <th>Role</th>
                    <th>Message</th>
                    <th>Avatar</th>
                    <th>Visible</th>
                    <th>Order</th>
                    <th class="text-right">Actions</th>
                </tr>
        </x-slot:head>

        @forelse ($testimonials as $testimonial)
            <tr>
                <td>{{ $testimonial->name }}</td>
                <td>{{ $testimonial->role }}</td>
                <td class="max-w-md truncate">{{ $testimonial->message }}</td>
                <td>
                    @if ($testimonial->avatar_path)
                        <img src="{{ str_starts_with($testimonial->avatar_path, 'http') ? $testimonial->avatar_path : Storage::url($testimonial->avatar_path) }}" alt="Avatar" class="h-10 w-10 rounded-full object-cover">
                    @else
                        -
                    @endif
                </td>
                <td><span class="badge {{ $testimonial->is_visible ? 'badge-success' : 'badge-ghost' }}">{{ $testimonial->is_visible ? 'Yes' : 'No' }}</span></td>
                <td>{{ $testimonial->sort_order }}</td>
                <td class="text-right">
                    <x-ui.dropdown-action>
                        <li><button type="button" wire:click="openEditModal({{ $testimonial->id }})">Edit</button></li>
                        <li><button type="button" wire:click="delete({{ $testimonial->id }})" wire:confirm="Delete this testimonial?" class="text-error">Delete</button></li>
                    </x-ui.dropdown-action>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-base-content/55">No testimonial found.</td></tr>
        @endforelse
    </x-ui.table>

    <div>{{ $testimonials->links() }}</div>

    <x-ui.modal :open="$showModal" :title="$testimonialId ? 'Edit Testimonial' : 'Add Testimonial'" closeAction="$set('showModal', false)">
        <form wire:submit="save" class="mt-4 grid gap-3">
            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.input-field label="Name" name="name" wire:model.defer="name" required />
                <x-ui.input-field label="Role" name="role" wire:model.defer="role" required />
            </div>

            <x-ui.textarea-field label="Message" name="message" wire:model.defer="message" :rows="4" required />

            <div class="grid gap-3 sm:grid-cols-2">
                <x-ui.file-field label="Avatar" name="avatar" wire:model="avatar" accept="image/*" />
                <x-ui.input-field label="Sort Order" name="sortOrder" type="number" min="0" wire:model.defer="sortOrder" required />
            </div>

            @if ($existingAvatar)
                <img src="{{ str_starts_with($existingAvatar, 'http') ? $existingAvatar : Storage::url($existingAvatar) }}" alt="Avatar preview" class="h-16 w-16 rounded-full object-cover">
            @endif

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
