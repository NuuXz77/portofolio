<section class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-white">Hero Section Content</h2>
    <p class="mt-1 text-sm text-base-content/60">Manage headline, subheadline, roles, CTA buttons, and hero image.</p>

    <form wire:submit="save" class="mt-6 grid gap-4 lg:grid-cols-2">
        <x-ui.input-field label="Headline" name="headline" wire:model.defer="headline" wrapperClass="lg:col-span-2" required />

        <x-ui.textarea-field label="Subheadline" name="subheadline" wire:model.defer="subheadline" :rows="3" wrapperClass="lg:col-span-2" required />

        <x-ui.textarea-field label="Typing Roles (one per line)" name="rolesText" wire:model.defer="rolesText" :rows="4" wrapperClass="lg:col-span-2" required />

        <x-ui.input-field label="Primary CTA Text" name="primaryCtaText" wire:model.defer="primaryCtaText" required />

        <x-ui.input-field label="Primary CTA Link" name="primaryCtaLink" wire:model.defer="primaryCtaLink" required />

        <x-ui.input-field label="Secondary CTA Text" name="secondaryCtaText" wire:model.defer="secondaryCtaText" required />

        <x-ui.input-field label="Secondary CTA Link" name="secondaryCtaLink" wire:model.defer="secondaryCtaLink" required />

        <x-ui.file-field label="Hero Image" name="heroImage" wire:model="heroImage" accept="image/*" wrapperClass="lg:col-span-2">
            @if ($existingImage)
                <img src="{{ str_starts_with($existingImage, 'http') ? $existingImage : Storage::url($existingImage) }}" alt="Hero preview" class="mt-3 h-52 w-full rounded-xl object-cover">
            @endif
        </x-ui.file-field>

        <div class="lg:col-span-2">
            <button type="submit" class="btn btn-info rounded-xl text-white" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save Hero Content</span>
                <span wire:loading wire:target="save" class="loading loading-spinner loading-sm"></span>
            </button>
        </div>
    </form>
</section>
