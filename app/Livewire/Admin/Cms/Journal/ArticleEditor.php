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

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $content = '';

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

    public string $seoDescription = '';

    protected bool $autoGenerateSlug = true;

    public function mount(?Article $article = null): void
    {
        $this->authorName = Auth::user()?->name ?? 'Admin';

        if (! $article) {
            return;
        }

        $this->articleId = $article->id;
        $this->title = $article->title;
        $this->slug = $article->slug;
        $this->excerpt = $article->excerpt ?? '';
        $this->content = $article->content;
        $this->tagsInput = collect($article->tags ?? [])->join(', ');
        $this->categoryId = $article->category_id;
        $this->status = $article->status;
        $this->visibility = $article->visibility;
        $this->publishDate = $article->published_at?->format('Y-m-d\\TH:i');
        $this->authorName = $article->author_name ?: $this->authorName;
        $this->existingThumbnail = $article->thumbnail_path;
        $this->accessToken = $article->access_token;
        $this->seoTitle = $article->seo_title ?? '';
        $this->seoDescription = $article->seo_description ?? '';
        $this->autoGenerateSlug = false;
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->autoGenerateSlug) {
            return;
        }

        $this->slug = $this->buildUniqueSlug($value, $this->articleId);
    }

    public function updatedSlug(string $value): void
    {
        $this->autoGenerateSlug = false;
        $this->slug = Str::slug($value);
    }

    public function resetSlugAutoGeneration(): void
    {
        $this->autoGenerateSlug = true;
        $this->slug = $this->buildUniqueSlug($this->title, $this->articleId);
    }

    public function save(): void
    {
        $this->validate($this->rules(), [], [
            'categoryId' => 'category',
            'tagsInput' => 'tags',
            'publishDate' => 'publish date',
            'authorName' => 'author',
            'seoTitle' => 'SEO title',
            'seoDescription' => 'SEO description',
        ]);

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
                'slug' => $slug,
                'thumbnail_path' => $thumbnailPath,
                'excerpt' => $this->excerpt !== ''
                    ? $this->excerpt
                    : Str::limit(preg_replace('/\\s+/', ' ', strip_tags($this->content)), 170),
                'content' => $this->content,
                'tags' => $tags,
                'status' => $this->status,
                'visibility' => $this->visibility,
                'published_at' => $publishedAt,
                'author_name' => $this->authorName,
                'read_time' => max(1, (int) ceil(str_word_count(strip_tags($this->content)) / 220)),
                'access_token' => $accessToken,
                'seo_title' => $this->seoTitle !== '' ? $this->seoTitle : null,
                'seo_description' => $this->seoDescription !== '' ? $this->seoDescription : null,
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
            'categories' => ArticleCategory::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'required',
                'string',
                'max:180',
                Rule::unique('articles', 'slug')->ignore($this->articleId),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'categoryId' => ['nullable', 'integer', Rule::exists('article_categories', 'id')],
            'tagsInput' => ['nullable', 'string', 'max:600'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'publishDate' => ['nullable', 'date'],
            'authorName' => ['required', 'string', 'max:120'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'seoTitle' => ['nullable', 'string', 'max:180'],
            'seoDescription' => ['nullable', 'string', 'max:255'],
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
