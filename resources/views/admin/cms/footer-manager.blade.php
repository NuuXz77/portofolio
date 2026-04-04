<section class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-white">Footer Management</h2>
    <p class="mt-1 text-sm text-base-content/60">Manage tagline, copyright, CTA text, and social links.</p>

    <form wire:submit="save" class="mt-6 grid gap-4 lg:grid-cols-2">
        <x-ui.input-field label="Tagline" name="tagline" wire:model.defer="tagline" required />

        <x-ui.input-field label="Copyright" name="copyright" wire:model.defer="copyright" required />

        <x-ui.input-field label="Footer CTA" name="cta" wire:model.defer="cta" wrapperClass="lg:col-span-2" required />

        <x-ui.textarea-field label="Social Links (Format: Label|Link, one per line)" name="socialsText" wire:model.defer="socialsText" :rows="6" wrapperClass="lg:col-span-2" required />

        <div class="lg:col-span-2">
            <button type="submit" class="btn btn-info rounded-xl text-white">Save Footer</button>
        </div>
    </form>
</section>
