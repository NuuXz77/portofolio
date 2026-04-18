<?php

namespace App\Livewire\Admin\Cms\Journal;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Support\AdminActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class ArticleEditor extends Component
{
    use WithFileUploads;

    public ?int $articleId = null;

    public string $editingLocale = 'id';

    public string $titleId = '';

    public string $titleEn = '';

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $excerptId = '';

    public string $excerptEn = '';

    public string $content = '';

    public string $contentId = '';

    public string $contentEn = '';

    public string $tagsInput = '';

    public ?int $categoryId = null;

    public string $status = 'draft';

    public string $visibility = 'public';

    public ?string $publishDate = null;

    public string $authorName = '';

    public ?string $existingThumbnail = null;

    public $thumbnail;

    public ?string $accessToken = null;

    public string $seoTitle = '';

    public string $seoTitleId = '';

    public string $seoTitleEn = '';

    public string $seoDescription = '';

    public string $seoDescriptionId = '';

    public string $seoDescriptionEn = '';

    protected bool $autoGenerateSlug = true;

    public function mount(?Article $article = null): void
    {
        $this->authorName = Auth::user()?->name ?? 'Admin';

        $this->titleId = '';
        $this->titleEn = '';
        $this->excerptId = '';
        $this->excerptEn = '';
        $this->contentId = '';
        $this->contentEn = '';
        $this->seoTitleId = '';
        $this->seoTitleEn = '';
        $this->seoDescriptionId = '';
        $this->seoDescriptionEn = '';

        if (! $article) {
            return;
        }

        $titleTranslations = \App\Support\LocalizedContent::split($article->title_translations ?? $article->title);
        $excerptTranslations = \App\Support\LocalizedContent::split($article->excerpt_translations ?? ($article->excerpt ?? ''));
        $contentTranslations = \App\Support\LocalizedContent::split($article->content_translations ?? $article->content);
        $seoTitleTranslations = \App\Support\LocalizedContent::split($article->seo_title_translations ?? ($article->seo_title ?? ''));
        $seoDescriptionTranslations = \App\Support\LocalizedContent::split($article->seo_description_translations ?? ($article->seo_description ?? ''));

        $this->articleId = $article->id;
        $this->titleId = $titleTranslations['id'];
        $this->titleEn = $titleTranslations['en'];
        $this->title = $this->titleId;
        $this->slug = $article->slug;
        $this->excerptId = $excerptTranslations['id'];
        $this->excerptEn = $excerptTranslations['en'];
        $this->excerpt = $this->excerptId;
        $this->contentId = $contentTranslations['id'];
        $this->contentEn = $contentTranslations['en'];
        $this->content = $this->contentId;
        $this->tagsInput = collect($article->tags ?? [])->join(', ');
        $this->categoryId = $article->category_id;
        $this->status = $article->status;
        $this->visibility = $article->visibility;
        $this->publishDate = $article->published_at?->format('Y-m-d\\TH:i');
        $this->authorName = $article->author_name ?: $this->authorName;
        $this->existingThumbnail = $article->thumbnail_path;
        $this->accessToken = $article->access_token;
        $this->seoTitleId = $seoTitleTranslations['id'];
        $this->seoTitleEn = $seoTitleTranslations['en'];
        $this->seoTitle = $this->seoTitleId;
        $this->seoDescriptionId = $seoDescriptionTranslations['id'];
        $this->seoDescriptionEn = $seoDescriptionTranslations['en'];
        $this->seoDescription = $this->seoDescriptionId;
        $this->autoGenerateSlug = false;
    }

    public function updatedTitleId(string $value): void
    {
        $this->title = $value;

        if (! $this->autoGenerateSlug) {
            return;
        }

        $this->slug = $this->buildUniqueSlug($value, $this->articleId);
    }

    public function updatedContentId(string $value): void
    {
        $this->content = $value;
    }

    public function updatedSlug(string $value): void
    {
        $this->autoGenerateSlug = false;
        $this->slug = Str::slug($value);
    }

    public function resetSlugAutoGeneration(): void
    {
        $this->autoGenerateSlug = true;
        $this->slug = $this->buildUniqueSlug($this->titleId !== '' ? $this->titleId : $this->titleEn, $this->articleId);
    }

    public function save(): void
    {
        $this->validate($this->rules(), [], [
            'titleId' => 'title (ID)',
            'titleEn' => 'title (EN)',
            'categoryId' => 'category',
            'tagsInput' => 'tags',
            'publishDate' => 'publish date',
            'authorName' => 'author',
            'seoTitleId' => 'SEO title (ID)',
            'seoTitleEn' => 'SEO title (EN)',
            'seoDescriptionId' => 'SEO description (ID)',
            'seoDescriptionEn' => 'SEO description (EN)',
        ]);

        $this->title = trim($this->titleId) !== '' ? trim($this->titleId) : trim($this->titleEn);
        $this->excerpt = trim($this->excerptId) !== '' ? trim($this->excerptId) : trim($this->excerptEn);
        $this->content = trim($this->contentId) !== '' ? $this->contentId : $this->contentEn;
        $this->seoTitle = trim($this->seoTitleId) !== '' ? trim($this->seoTitleId) : trim($this->seoTitleEn);
        $this->seoDescription = trim($this->seoDescriptionId) !== '' ? trim($this->seoDescriptionId) : trim($this->seoDescriptionEn);

        $cleanContent = trim(strip_tags($this->content));

        if ($cleanContent === '') {
            $this->addError('content', 'Content is required.');

            return;
        }

        $thumbnailPath = $this->existingThumbnail;

        if ($this->thumbnail) {
            $thumbnailPath = $this->thumbnail->store('journal/articles', 'public');
        }

        $slug = $this->buildUniqueSlug($this->slug ?: $this->title, $this->articleId);
        $tags = collect(explode(',', $this->tagsInput))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->values()
            ->all();

        $publishedAt = $this->status === 'published'
            ? ($this->publishDate ? now()->parse($this->publishDate) : now())
            : null;

        $accessToken = $this->visibility === 'private'
            ? ($this->accessToken ?: Str::random(40))
            : null;

        $article = Article::query()->updateOrCreate(
            ['id' => $this->articleId],
            [
                'category_id' => $this->categoryId,
                'created_by' => Auth::id(),
                'title' => $this->title,
                'title_translations' => \App\Support\LocalizedContent::pack($this->titleId, $this->titleEn),
                'slug' => $slug,
                'thumbnail_path' => $thumbnailPath,
                'excerpt' => $this->excerpt !== ''
                    ? $this->excerpt
                    : Str::limit(preg_replace('/\\s+/', ' ', strip_tags($this->content)), 170),
                'excerpt_translations' => \App\Support\LocalizedContent::pack($this->excerptId, $this->excerptEn),
                'content' => $this->content,
                'content_translations' => \App\Support\LocalizedContent::pack($this->contentId, $this->contentEn),
                'tags' => $tags,
                'status' => $this->status,
                'visibility' => $this->visibility,
                'published_at' => $publishedAt,
                'author_name' => $this->authorName,
                'read_time' => max(1, (int) ceil(str_word_count(strip_tags($this->content)) / 220)),
                'access_token' => $accessToken,
                'seo_title' => $this->seoTitle !== '' ? $this->seoTitle : null,
                'seo_title_translations' => \App\Support\LocalizedContent::pack($this->seoTitleId, $this->seoTitleEn),
                'seo_description' => $this->seoDescription !== '' ? $this->seoDescription : null,
                'seo_description_translations' => \App\Support\LocalizedContent::pack($this->seoDescriptionId, $this->seoDescriptionEn),
            ]
        );

        $this->articleId = $article->id;
        $this->slug = $article->slug;
        $this->existingThumbnail = $article->thumbnail_path;
        $this->accessToken = $article->access_token;

        AdminActivity::log('saved', 'journal', 'Saved journal article.', [
            'title' => $article->title,
            'status' => $article->status,
            'visibility' => $article->visibility,
        ]);
    session()->flash('success', 'Article saved successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Article saved successfully.');

        $this->redirectRoute('admin.journal.edit', ['article' => $article->id], navigate: true);
    }

    public function publishNow(): void
    {
        $this->status = 'published';

        if (! $this->publishDate) {
            $this->publishDate = now()->format('Y-m-d\\TH:i');
        }

        $this->save();
    }

    public function getPreviewUrlProperty(): ?string
    {
        if (! $this->slug) {
            return null;
        }

        $url = route('journal.show', ['slug' => $this->slug]);

        if ($this->visibility === 'private' && $this->accessToken) {
            return $url.'?key='.$this->accessToken;
        }

        return $url;
    }

    #[Layout('components.layouts.admin')]
    #[Title('Write Article')]
    public function render()
    {
        return view('admin.cms.journal.article-editor', [
            'categories' => ArticleCategory::query()->orderBy('name')->get(['id', 'name', 'name_translations']),
        ]);
    }

    protected function rules(): array
    {
        return [
            'titleId' => ['required', 'string', 'max:180'],
            'titleEn' => ['required', 'string', 'max:180'],
            'slug' => [
                'required',
                'string',
                'max:180',
                Rule::unique('articles', 'slug')->ignore($this->articleId),
            ],
            'excerptId' => ['nullable', 'string', 'max:500'],
            'excerptEn' => ['nullable', 'string', 'max:500'],
            'contentId' => ['required', 'string'],
            'contentEn' => ['required', 'string'],
            'categoryId' => ['nullable', 'integer', Rule::exists('article_categories', 'id')],
            'tagsInput' => ['nullable', 'string', 'max:600'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'publishDate' => ['nullable', 'date'],
            'authorName' => ['required', 'string', 'max:120'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'seoTitleId' => ['nullable', 'string', 'max:180'],
            'seoTitleEn' => ['nullable', 'string', 'max:180'],
            'seoDescriptionId' => ['nullable', 'string', 'max:255'],
            'seoDescriptionEn' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function buildUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source);

        if ($base === '') {
            $base = 'article';
        }

        $slug = $base;
        $counter = 1;

        while (
            Article::query()
                ->when($ignoreId, fn ($builder) => $builder->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
