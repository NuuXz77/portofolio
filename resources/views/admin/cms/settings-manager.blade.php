<section class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
    <h2 class="text-lg font-semibold text-white">Global Settings</h2>
    <p class="mt-1 text-sm text-base-content/60">Configure SEO metadata and dynamic landing section headers.</p>

    <form wire:submit="save" class="mt-6 grid gap-4 lg:max-w-4xl">
        <div class="tabs tabs-boxed inline-flex rounded-xl border border-base-content/10 bg-base-100/55 p-1">
            <button type="button" wire:click="$set('editingLocale', 'id')" class="tab rounded-lg px-4 {{ $editingLocale === 'id' ? 'tab-active bg-info text-base-content' : '' }}">ID</button>
            <button type="button" wire:click="$set('editingLocale', 'en')" class="tab rounded-lg px-4 {{ $editingLocale === 'en' ? 'tab-active bg-info text-base-content' : '' }}">EN</button>
        </div>

        <div class="divider text-xs uppercase tracking-[0.18em] text-base-content/50">SEO</div>

        @if ($editingLocale === 'id')
            <x-ui.input-field label="Site Title (ID)" name="siteTitleId" wire:model.defer="siteTitleId" required />
            <x-ui.textarea-field label="Site Description (ID)" name="siteDescriptionId" wire:model.defer="siteDescriptionId" :rows="4" required />
            <x-ui.input-field label="Keywords (ID)" name="siteKeywordsId" wire:model.defer="siteKeywordsId" placeholder="fullstack, laravel, devops" />

            <div class="divider text-xs uppercase tracking-[0.18em] text-base-content/50">Landing Section Headers (ID)</div>

            <x-ui.input-field label="About Badge (ID)" name="aboutBadgeId" wire:model.defer="aboutBadgeId" required />
            <x-ui.input-field label="Skills Badge (ID)" name="skillsBadgeId" wire:model.defer="skillsBadgeId" required />
            <x-ui.input-field label="Skills Title (ID)" name="skillsTitleId" wire:model.defer="skillsTitleId" required />
            <x-ui.input-field label="Projects Badge (ID)" name="projectsBadgeId" wire:model.defer="projectsBadgeId" required />
            <x-ui.input-field label="Projects Title (ID)" name="projectsTitleId" wire:model.defer="projectsTitleId" required />
            <x-ui.input-field label="Featured Badge (ID)" name="featuredBadgeId" wire:model.defer="featuredBadgeId" required />
            <x-ui.input-field label="Featured Title (ID)" name="featuredTitleId" wire:model.defer="featuredTitleId" required />
            <x-ui.input-field label="Journal Badge (ID)" name="journalBadgeId" wire:model.defer="journalBadgeId" required />
            <x-ui.input-field label="Journal Title (ID)" name="journalTitleId" wire:model.defer="journalTitleId" required />
            <x-ui.input-field label="Journey Badge (ID)" name="journeyBadgeId" wire:model.defer="journeyBadgeId" required />
            <x-ui.input-field label="Journey Title (ID)" name="journeyTitleId" wire:model.defer="journeyTitleId" required />
            <x-ui.input-field label="Education Badge (ID)" name="educationBadgeId" wire:model.defer="educationBadgeId" required />
            <x-ui.input-field label="Education Title (ID)" name="educationTitleId" wire:model.defer="educationTitleId" required />
            <x-ui.input-field label="Experience Badge (ID)" name="experienceBadgeId" wire:model.defer="experienceBadgeId" required />
            <x-ui.input-field label="Experience Title (ID)" name="experienceTitleId" wire:model.defer="experienceTitleId" required />
            <x-ui.input-field label="Services Badge (ID)" name="servicesBadgeId" wire:model.defer="servicesBadgeId" required />
            <x-ui.input-field label="Services Title (ID)" name="servicesTitleId" wire:model.defer="servicesTitleId" required />
            <x-ui.input-field label="Testimonials Badge (ID)" name="testimonialsBadgeId" wire:model.defer="testimonialsBadgeId" required />
            <x-ui.input-field label="Testimonials Title (ID)" name="testimonialsTitleId" wire:model.defer="testimonialsTitleId" required />
        @else
            <x-ui.input-field label="Site Title (EN)" name="siteTitleEn" wire:model.defer="siteTitleEn" required />
            <x-ui.textarea-field label="Site Description (EN)" name="siteDescriptionEn" wire:model.defer="siteDescriptionEn" :rows="4" required />
            <x-ui.input-field label="Keywords (EN)" name="siteKeywordsEn" wire:model.defer="siteKeywordsEn" placeholder="fullstack, laravel, devops" />

            <div class="divider text-xs uppercase tracking-[0.18em] text-base-content/50">Landing Section Headers (EN)</div>

            <x-ui.input-field label="About Badge (EN)" name="aboutBadgeEn" wire:model.defer="aboutBadgeEn" required />
            <x-ui.input-field label="Skills Badge (EN)" name="skillsBadgeEn" wire:model.defer="skillsBadgeEn" required />
            <x-ui.input-field label="Skills Title (EN)" name="skillsTitleEn" wire:model.defer="skillsTitleEn" required />
            <x-ui.input-field label="Projects Badge (EN)" name="projectsBadgeEn" wire:model.defer="projectsBadgeEn" required />
            <x-ui.input-field label="Projects Title (EN)" name="projectsTitleEn" wire:model.defer="projectsTitleEn" required />
            <x-ui.input-field label="Featured Badge (EN)" name="featuredBadgeEn" wire:model.defer="featuredBadgeEn" required />
            <x-ui.input-field label="Featured Title (EN)" name="featuredTitleEn" wire:model.defer="featuredTitleEn" required />
            <x-ui.input-field label="Journal Badge (EN)" name="journalBadgeEn" wire:model.defer="journalBadgeEn" required />
            <x-ui.input-field label="Journal Title (EN)" name="journalTitleEn" wire:model.defer="journalTitleEn" required />
            <x-ui.input-field label="Journey Badge (EN)" name="journeyBadgeEn" wire:model.defer="journeyBadgeEn" required />
            <x-ui.input-field label="Journey Title (EN)" name="journeyTitleEn" wire:model.defer="journeyTitleEn" required />
            <x-ui.input-field label="Education Badge (EN)" name="educationBadgeEn" wire:model.defer="educationBadgeEn" required />
            <x-ui.input-field label="Education Title (EN)" name="educationTitleEn" wire:model.defer="educationTitleEn" required />
            <x-ui.input-field label="Experience Badge (EN)" name="experienceBadgeEn" wire:model.defer="experienceBadgeEn" required />
            <x-ui.input-field label="Experience Title (EN)" name="experienceTitleEn" wire:model.defer="experienceTitleEn" required />
            <x-ui.input-field label="Services Badge (EN)" name="servicesBadgeEn" wire:model.defer="servicesBadgeEn" required />
            <x-ui.input-field label="Services Title (EN)" name="servicesTitleEn" wire:model.defer="servicesTitleEn" required />
            <x-ui.input-field label="Testimonials Badge (EN)" name="testimonialsBadgeEn" wire:model.defer="testimonialsBadgeEn" required />
            <x-ui.input-field label="Testimonials Title (EN)" name="testimonialsTitleEn" wire:model.defer="testimonialsTitleEn" required />
        @endif

        <div>
            <button type="submit" class="btn btn-info rounded-xl text-white">Save Settings</button>
        </div>
    </form>
</section>
