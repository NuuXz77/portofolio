<section class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-white">About Section Content</h2>
    <p class="mt-1 text-sm text-base-content/60">Manage profile image, story description, and dynamic stat cards.</p>

    <form wire:submit="save" class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="tabs tabs-boxed inline-flex rounded-xl border border-base-content/10 bg-base-100/55 p-1 lg:col-span-2">
            <button type="button" wire:click="$set('editingLocale', 'id')" class="tab rounded-lg px-4 {{ $editingLocale === 'id' ? 'tab-active bg-info text-base-content' : '' }}">ID</button>
            <button type="button" wire:click="$set('editingLocale', 'en')" class="tab rounded-lg px-4 {{ $editingLocale === 'en' ? 'tab-active bg-info text-base-content' : '' }}">EN</button>
        </div>

        @if ($editingLocale === 'id')
            <x-ui.input-field label="Section Title (ID)" name="titleId" wire:model.defer="titleId" wrapperClass="lg:col-span-2" required />

            <x-ui.textarea-field label="Description (ID, HTML supported)" name="descriptionId" wire:model.defer="descriptionId" :rows="8" wrapperClass="lg:col-span-2" required />

            <x-ui.textarea-field
                label="Stats ID (1 baris = 1 item)"
                name="statsTextId"
                wire:model.defer="statsTextId"
                :rows="6"
                wrapperClass="lg:col-span-2"
                placeholder="Projects Deployed | 7+&#10;Experience | 5 Years&#10;Tech Mastery | Multi Stack"
                hint="Gunakan format Label | Value. Juga mendukung Label:Value atau Label - Value."
                required
            />
        @else
            <x-ui.input-field label="Section Title (EN)" name="titleEn" wire:model.defer="titleEn" wrapperClass="lg:col-span-2" required />

            <x-ui.textarea-field label="Description (EN, HTML supported)" name="descriptionEn" wire:model.defer="descriptionEn" :rows="8" wrapperClass="lg:col-span-2" required />

            <x-ui.textarea-field
                label="Stats EN (1 line = 1 item)"
                name="statsTextEn"
                wire:model.defer="statsTextEn"
                :rows="6"
                wrapperClass="lg:col-span-2"
                placeholder="Projects Deployed | 7+&#10;Experience | 5 Years&#10;Tech Mastery | Multi Stack"
                hint="Use Label | Value format. Label:Value and Label - Value are also supported."
                required
            />
        @endif

        <x-ui.file-field label="Profile Image" name="profileImage" wire:model="profileImage" accept="image/*" wrapperClass="lg:col-span-2">
            @if ($existingImage)
                <img src="{{ str_starts_with($existingImage, 'http') ? $existingImage : Storage::url($existingImage) }}" alt="About preview" class="mt-3 h-52 w-full rounded-xl object-cover">
            @endif
        </x-ui.file-field>

        <div class="lg:col-span-2">
            <button type="submit" class="btn btn-info rounded-xl text-white" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save About Section</span>
                <span wire:loading wire:target="save" class="loading loading-spinner loading-sm"></span>
            </button>
        </div>
    </form>
</section>
