<section class="space-y-5">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs uppercase tracking-[0.2em] text-info">Journal Editor</p>
            <h1 class="mt-1 text-2xl font-semibold text-white">{{ $articleId ? 'Edit Article' : 'Write New Article' }}</h1>
            <p class="text-sm text-base-content/60">Focused writing workspace with publish controls.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.journal.index') }}" wire:navigate class="btn btn-outline rounded-xl">Back</a>
            @if ($this->previewUrl)
                <a href="{{ $this->previewUrl }}" target="_blank" rel="noreferrer" class="btn btn-outline btn-info rounded-xl">Preview</a>
            @endif
            <button type="button" wire:click="publishNow" class="btn btn-success rounded-xl text-white">Publish Now</button>
        </div>
    </div>

    <div class="tabs tabs-boxed inline-flex rounded-xl border border-base-content/10 bg-base-100/55 p-1">
        <button type="button" wire:click="$set('editingLocale', 'id')" class="tab rounded-lg px-4 {{ $editingLocale === 'id' ? 'tab-active bg-info text-base-content' : '' }}">ID</button>
        <button type="button" wire:click="$set('editingLocale', 'en')" class="tab rounded-lg px-4 {{ $editingLocale === 'en' ? 'tab-active bg-info text-base-content' : '' }}">EN</button>
    </div>

    <form wire:submit="save" class="grid gap-5 xl:grid-cols-[1.45fr,1fr]">
        <article class="glass-card space-y-4 rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
            @if ($editingLocale === 'id')
                <x-ui.input-field label="Title (ID)" name="titleId" wire:model.live.debounce.300ms="titleId" placeholder="Tulis judul artikel (Indonesia)" required />
            @else
                <x-ui.input-field label="Title (EN)" name="titleEn" wire:model.live.debounce.300ms="titleEn" placeholder="Write article title (English)" required />
            @endif

            <div class="grid gap-3 md:grid-cols-[1fr,auto]">
                <x-ui.input-field label="Slug" name="slug" wire:model.defer="slug" placeholder="article-slug" required />
                <div class="md:pt-8">
                    <button type="button" wire:click="resetSlugAutoGeneration" class="btn btn-outline btn-sm rounded-xl">Auto Regenerate</button>
                </div>
            </div>

            @if ($editingLocale === 'id')
                <x-ui.textarea-field label="Excerpt (ID)" name="excerptId" wire:model.defer="excerptId" :rows="3" hint="Opsional. Jika kosong, akan dibuat otomatis dari konten ID." />
            @else
                <x-ui.textarea-field label="Excerpt (EN)" name="excerptEn" wire:model.defer="excerptEn" :rows="3" hint="Optional. Auto-generated from EN content when empty." />
            @endif

            <div class="glass-card-soft rounded-2xl border border-base-content/10 bg-base-200/35 p-3">
                <div class="mb-2 flex items-center justify-between gap-2">
                    <p class="text-sm font-medium text-base-content/80">Content {{ strtoupper($editingLocale) }}</p>
                    <span class="text-xs text-base-content/55">Supports heading, list, link, image, code block</span>
                </div>

                <div class="{{ $editingLocale === 'id' ? '' : 'hidden' }}">
                    <div wire:ignore data-journal-editor data-model-input="journal-content-input-id" class="glass-card-soft rounded-xl border border-base-content/15 bg-base-100">
                        <div data-journal-editor-area class="journal-editor min-h-72"></div>
                    </div>

                    <textarea id="journal-content-input-id" wire:model.defer="contentId" class="hidden"></textarea>

                    @error('contentId')
                        <span class="mt-2 block text-xs text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="{{ $editingLocale === 'en' ? '' : 'hidden' }}">
                    <div wire:ignore data-journal-editor data-model-input="journal-content-input-en" class="glass-card-soft rounded-xl border border-base-content/15 bg-base-100">
                        <div data-journal-editor-area class="journal-editor min-h-72"></div>
                    </div>

                    <textarea id="journal-content-input-en" wire:model.defer="contentEn" class="hidden"></textarea>

                    @error('contentEn')
                        <span class="mt-2 block text-xs text-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <x-ui.textarea-field label="Tags (comma separated)" name="tagsInput" wire:model.defer="tagsInput" :rows="2" placeholder="laravel, livewire, devlog" />
        </article>

        <aside class="space-y-4">
            <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
                <h2 class="text-base font-semibold text-white">Publishing</h2>

                <div class="mt-4 space-y-3">
                    <x-ui.select-field label="Category" name="categoryId" wire:model.defer="categoryId" placeholder="Select category">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ \App\Support\LocalizedContent::resolve($category->name_translations ?? $category->name, default: $category->name) }}</option>
                        @endforeach
                    </x-ui.select-field>

                    <x-ui.select-field label="Status" name="status" wire:model.defer="status" placeholder="Select status" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </x-ui.select-field>

                    <x-ui.select-field label="Visibility" name="visibility" wire:model.defer="visibility" placeholder="Select visibility" required>
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </x-ui.select-field>

                    <x-ui.input-field label="Publish Date" name="publishDate" type="datetime-local" wire:model.defer="publishDate" />
                    <x-ui.input-field label="Author" name="authorName" wire:model.defer="authorName" required />
                </div>
            </article>

            <article class="glass-card rounded-2xl border border-base-content/10 bg-base-100 p-5 shadow-sm">
                <h2 class="text-base font-semibold text-white">Media & SEO</h2>

                <div class="mt-4 space-y-3">
                    <x-ui.file-field label="Thumbnail" name="thumbnail" wire:model="thumbnail" accept="image/*" />

                    @if ($existingThumbnail)
                        <img src="{{ str_starts_with($existingThumbnail, 'http') ? $existingThumbnail : Storage::url($existingThumbnail) }}" alt="Thumbnail preview" class="h-40 w-full rounded-xl object-cover">
                    @endif

                    @if ($visibility === 'private' && $accessToken)
                        <div class="rounded-xl border border-warning/35 bg-warning/10 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-warning">Private Access Token</p>
                            <p class="mt-1 break-all text-xs text-base-content/75">{{ $accessToken }}</p>
                        </div>
                    @endif

                    @if ($editingLocale === 'id')
                        <x-ui.input-field label="SEO Title (ID)" name="seoTitleId" wire:model.defer="seoTitleId" />
                        <x-ui.textarea-field label="SEO Description (ID)" name="seoDescriptionId" wire:model.defer="seoDescriptionId" :rows="3" />
                    @else
                        <x-ui.input-field label="SEO Title (EN)" name="seoTitleEn" wire:model.defer="seoTitleEn" />
                        <x-ui.textarea-field label="SEO Description (EN)" name="seoDescriptionEn" wire:model.defer="seoDescriptionEn" :rows="3" />
                    @endif
                </div>
            </article>

            <button type="submit" class="btn btn-info w-full rounded-xl text-white">
                <span wire:loading.remove wire:target="save">Save Article</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <span class="loading loading-spinner loading-sm"></span>
                    Saving...
                </span>
            </button>
        </aside>
    </form>
</section>
