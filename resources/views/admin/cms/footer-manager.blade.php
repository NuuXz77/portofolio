<section class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-white">Footer Management</h2>
    <p class="mt-1 text-sm text-base-content/60">Manage tagline, copyright, CTA text, and social links with explicit icons.</p>

    <form wire:submit="save" class="mt-6 grid gap-4 lg:grid-cols-2">
        <div class="tabs tabs-boxed inline-flex rounded-xl border border-base-content/10 bg-base-100/55 p-1 lg:col-span-2">
            <button type="button" wire:click="$set('editingLocale', 'id')" class="tab rounded-lg px-4 {{ $editingLocale === 'id' ? 'tab-active bg-info text-base-content' : '' }}">ID</button>
            <button type="button" wire:click="$set('editingLocale', 'en')" class="tab rounded-lg px-4 {{ $editingLocale === 'en' ? 'tab-active bg-info text-base-content' : '' }}">EN</button>
        </div>

        @if ($editingLocale === 'id')
            <x-ui.input-field label="Tagline (ID)" name="taglineId" wire:model.defer="taglineId" required />
            <x-ui.input-field label="Copyright (ID)" name="copyrightId" wire:model.defer="copyrightId" required />
            <x-ui.input-field label="Footer CTA (ID)" name="ctaId" wire:model.defer="ctaId" wrapperClass="lg:col-span-2" required />
        @else
            <x-ui.input-field label="Tagline (EN)" name="taglineEn" wire:model.defer="taglineEn" required />
            <x-ui.input-field label="Copyright (EN)" name="copyrightEn" wire:model.defer="copyrightEn" required />
            <x-ui.input-field label="Footer CTA (EN)" name="ctaEn" wire:model.defer="ctaEn" wrapperClass="lg:col-span-2" required />
        @endif

        <x-ui.textarea-field
            label="Social Links (Format: LabelID|LabelEN|Link|Icon, one per line)"
            name="socialsText"
            wire:model.defer="socialsText"
            :rows="8"
            wrapperClass="lg:col-span-2"
            placeholder="GitHub|GitHub|https://github.com/username|brand-github&#10;LinkedIn|LinkedIn|https://linkedin.com/in/username|brand-linkedin&#10;X|X|https://x.com/username|brand-x&#10;WhatsApp|WhatsApp|https://wa.me/6281234567890|message-circle"
            hint="Format lama Label|Link|Icon tetap didukung (akan dipakai untuk ID dan EN sekaligus)."
            required
        />

        <div class="lg:col-span-2">
            <button type="submit" class="btn btn-info rounded-xl text-white">Save Footer</button>
        </div>
    </form>
</section>
