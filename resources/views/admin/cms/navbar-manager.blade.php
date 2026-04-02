<section class="grid gap-6 xl:grid-cols-[1.1fr,1.4fr]">
    <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-white">Navbar Settings</h2>
        <p class="mt-1 text-sm text-base-content/60">Edit logo and CTA displayed on the landing page.</p>

        <form wire:submit="saveNavbar" class="mt-5 space-y-4">
            <x-ui.input-field label="Logo Text" name="logoText" wire:model.defer="logoText" required />

            <x-ui.input-field label="CTA Text" name="ctaText" wire:model.defer="ctaText" required />

            <x-ui.input-field label="CTA Link" name="ctaLink" wire:model.defer="ctaLink" required />

            <button type="submit" class="btn btn-info rounded-xl text-white">Save Navbar Settings</button>
        </form>
    </article>

    <article class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-semibold text-white">Menu Items</h2>
                <p class="text-sm text-base-content/60">Create and manage navbar menu links.</p>
            </div>
            <button wire:click="resetMenuForm" type="button" class="btn btn-outline rounded-xl">Clear Form</button>
        </div>

        <form wire:submit="saveMenuItem" class="mt-4 grid gap-3 md:grid-cols-2">
            <x-ui.input-field label="Label" name="label" wire:model.defer="label" wrapperClass="md:col-span-2" required />

            <x-ui.input-field label="Href" name="href" wire:model.defer="href" wrapperClass="md:col-span-2" placeholder="#home" required />

            <x-ui.input-field label="Sort Order" name="sortOrder" type="number" min="0" wire:model.defer="sortOrder" required />

            <label class="label cursor-pointer justify-start gap-2 pt-8">
                <input wire:model.defer="isVisible" type="checkbox" class="checkbox checkbox-info checkbox-sm">
                <span class="label-text">Visible</span>
            </label>

            <div class="md:col-span-2">
                <button type="submit" class="btn btn-info rounded-xl text-white">{{ $menuItemId ? 'Update Item' : 'Create Item' }}</button>
            </div>
        </form>

        <x-ui.table wrapperClass="mt-5 rounded-xl" tableClass="table-zebra">
            <x-slot:head>
                <tr>
                    <th>Label</th>
                    <th>Href</th>
                    <th>Order</th>
                    <th>Visible</th>
                    <th class="text-right">Actions</th>
                </tr>
            </x-slot:head>

            @forelse ($menuItems as $item)
                <tr>
                    <td>{{ $item->label }}</td>
                    <td>{{ $item->href }}</td>
                    <td>{{ $item->sort_order }}</td>
                    <td>
                        <span class="badge {{ $item->is_visible ? 'badge-success' : 'badge-ghost' }}">{{ $item->is_visible ? 'Yes' : 'No' }}</span>
                    </td>
                    <td class="text-right">
                        <x-ui.dropdown-action>
                            <li><button type="button" wire:click="editMenuItem({{ $item->id }})">Edit</button></li>
                            <li><button type="button" wire:click="deleteMenuItem({{ $item->id }})" wire:confirm="Delete this menu item?" class="text-error">Delete</button></li>
                        </x-ui.dropdown-action>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-base-content/55">No menu items yet.</td>
                </tr>
            @endforelse
        </x-ui.table>
    </article>
</section>
