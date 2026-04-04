<section class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-white">Global Settings</h2>
    <p class="mt-1 text-sm text-base-content/60">Configure SEO metadata for the landing page.</p>

    <form wire:submit="save" class="mt-6 grid gap-4 lg:max-w-3xl">
        <x-ui.input-field label="Site Title" name="siteTitle" wire:model.defer="siteTitle" required />

        <x-ui.textarea-field label="Site Description" name="siteDescription" wire:model.defer="siteDescription" :rows="4" required />

        <x-ui.input-field label="Keywords (comma separated)" name="siteKeywords" wire:model.defer="siteKeywords" placeholder="fullstack, laravel, devops" />

        <div>
            <button type="submit" class="btn btn-info rounded-xl text-white">Save Settings</button>
        </div>
    </form>
</section>
