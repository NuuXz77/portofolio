<section class="grid gap-6 xl:grid-cols-[1.1fr,1.4fr]">
    @php
        $menuColumns = [
            ['label' => 'Label'],
            ['label' => 'Href'],
            ['label' => 'Order'],
            ['label' => 'Visible'],
            ['label' => 'Actions', 'class' => 'text-right w-20'],
        ];
    @endphp

    <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
        <h2 class="text-lg font-semibold text-white">Navbar Settings</h2>
        <p class="mt-1 text-sm text-base-content/60">Edit logo and CTA displayed on the landing page.</p>

        <form wire:submit="saveNavbar" class="mt-5 space-y-4">
            <x-ui.select-field label="Brand Display" name="brandMode" wire:model.live="brandMode" required>
                <option value="text">Text only</option>
                <option value="logo">Logo/Icon only</option>
                <option value="combo">Logo/Icon + Text</option>
            </x-ui.select-field>

            <x-ui.input-field
                label="Logo Text"
                name="logoText"
                wire:model.defer="logoText"
                placeholder="Wisnu.dev"
                hint="Wajib jika mode menampilkan text (Text only / Combo)."
            />

            <x-ui.select-field label="Logo Source" name="brandLogoType" wire:model.live="brandLogoType" required>
                <option value="image">Image (PNG/JPG/WebP/SVG)</option>
                <option value="icon">Icon (Lucide)</option>
            </x-ui.select-field>

            @if ($brandLogoType === 'icon')
                <x-ui.input-field
                    label="Logo Icon"
                    name="brandLogoIcon"
                    wire:model.defer="brandLogoIcon"
                    placeholder="sparkles"
                    hint="Isi nama icon Lucide, contoh: sparkles, rocket, code-2."
                />

                <div class="rounded-xl border border-base-content/10 bg-base-100/40 p-3">
                    <p class="text-xs uppercase tracking-wide text-base-content/55">Icon Preview</p>
                    <div class="mt-2 inline-flex items-center gap-2 rounded-xl border border-white/10 bg-base-100/55 px-3 py-2 text-sm font-medium text-base-content">
                        <i data-lucide="{{ trim($brandLogoIcon) !== '' ? trim($brandLogoIcon) : 'sparkles' }}" class="h-4 w-4 text-info"></i>
                        <span>{{ trim($logoText) !== '' ? $logoText : 'Brand' }}</span>
                    </div>
                </div>
            @else
                <x-ui.file-field
                    label="Logo Image"
                    name="brandLogoImage"
                    wire:model="brandLogoImage"
                    accept="image/png,image/jpeg,image/webp,image/svg+xml"
                    hint="Gunakan logo transparan agar menyatu dengan navbar."
                />

                @if ($brandLogoImage || $existingBrandLogoImage)
                    <div class="rounded-xl border border-base-content/10 bg-base-100/40 p-3">
                        <p class="text-xs uppercase tracking-wide text-base-content/55">Image Preview</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <img
                                src="{{ $brandLogoImage ? $brandLogoImage->temporaryUrl() : \Illuminate\Support\Facades\Storage::url($existingBrandLogoImage) }}"
                                alt="Brand logo preview"
                                class="h-10 w-10 rounded-lg border border-white/15 object-cover"
                            >
                            <button type="button" wire:click="clearBrandLogoImage" class="btn btn-ghost btn-sm rounded-lg text-error">Remove</button>
                        </div>
                    </div>
                @endif
            @endif

            <x-ui.input-field label="CTA Text" name="ctaText" wire:model.defer="ctaText" required />

            <x-ui.input-field label="CTA Link" name="ctaLink" wire:model.defer="ctaLink" required />

            <button type="submit" class="btn btn-info rounded-xl text-white">Save Navbar Settings</button>
        </form>
    </article>

    <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
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

        <x-ui.table
            wrapperClass="mt-5 rounded-xl"
            tableClass="table-zebra"
            :columns="$menuColumns"
            :data="$menuItems"
            emptyMessage="No menu items yet."
        >
            @foreach ($menuItems as $item)
                <tr wire:key="menu-item-{{ $item->id }}">
                    <td>{{ $item->label }}</td>
                    <td>{{ $item->href }}</td>
                    <td>{{ $item->sort_order }}</td>
                    <td>
                        <span class="badge badge-soft {{ $item->is_visible ? 'badge-success' : 'badge-ghost' }}">{{ $item->is_visible ? 'Yes' : 'No' }}</span>
                    </td>
                    <td class="text-right">
                        <x-ui.dropdown-action>
                            <li><button type="button" wire:click="editMenuItem({{ $item->id }})">Edit</button></li>
                            <li><button type="button" wire:click="deleteMenuItem({{ $item->id }})" wire:confirm="Delete this menu item?" class="text-error">Delete</button></li>
                        </x-ui.dropdown-action>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>
    </article>
</section>
