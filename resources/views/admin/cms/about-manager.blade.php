<section class="rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-white">About Section Content</h2>
    <p class="mt-1 text-sm text-base-content/60">Manage profile image, story description, and dynamic stat cards.</p>

    <form wire:submit="save" class="mt-6 grid gap-4 lg:grid-cols-2">
        <x-ui.input-field label="Section Title" name="title" wire:model.defer="title" wrapperClass="lg:col-span-2" required />

        <x-ui.textarea-field label="Description (HTML supported)" name="description" wire:model.defer="description" :rows="8" wrapperClass="lg:col-span-2" required />

        <x-ui.textarea-field label="Stats (Format: Label|Value, one per line)" name="statsText" wire:model.defer="statsText" :rows="5" wrapperClass="lg:col-span-2" required />

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
