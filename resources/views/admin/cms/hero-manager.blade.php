<section class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
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

        <x-ui.file-field
            label="Secondary CTA File (CV)"
            name="secondaryCtaFile"
            wire:model="secondaryCtaFile"
            accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            wrapperClass="lg:col-span-2"
            hint="Opsional. Jika diisi, tombol secondary di landing otomatis mengarah ke file ini (maks 10MB)."
        />

        @if ($existingSecondaryCtaFile)
            <div class="lg:col-span-2 rounded-xl border border-base-content/10 bg-base-100/50 p-3">
                <p class="text-xs text-base-content/60">Current file</p>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <a
                        href="{{ str_starts_with($existingSecondaryCtaFile, 'http') ? $existingSecondaryCtaFile : Storage::url($existingSecondaryCtaFile) }}"
                        target="_blank"
                        rel="noreferrer"
                        class="btn btn-sm btn-outline rounded-xl"
                    >
                        Preview File
                    </a>
                    <button type="button" wire:click="clearSecondaryCtaFile" class="btn btn-sm btn-ghost rounded-xl text-error">
                        Remove File
                    </button>
                </div>
            </div>
        @endif

        <x-ui.file-field label="Hero Image" name="heroImage" wire:model="heroImage" accept="image/*" wrapperClass="lg:col-span-2" hint="Maksimal ukuran file 10MB agar kualitas tetap bagus.">
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
