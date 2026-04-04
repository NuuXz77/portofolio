<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));

        $articles = Article::query()
            ->with('category:id,name,slug')
            ->publiclyVisible()
            ->when($search !== '', function ($builder) use ($search): void {
                $builder->where(function ($nested) use ($search): void {
                    $nested
                        ->where('title', 'like', '%'.$search.'%')
                        ->orWhere('excerpt', 'like', '%'.$search.'%')
                        ->orWhere('content', 'like', '%'.$search.'%');
                });
            })
            ->when($category !== '', function ($builder) use ($category): void {
                $builder->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category));
            })
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        $categories = ArticleCategory::query()
            ->whereHas('articles', fn ($builder) => $builder->publiclyVisible())
            ->withCount([
                'articles as public_articles_count' => fn ($builder) => $builder->publiclyVisible(),
            ])
            ->orderBy('name')
            ->get();

        return view('public.journal.index', [
            'articles' => $articles,
            'categories' => $categories,
            'search' => $search,
            'activeCategory' => $category,
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $article = Article::query()
            ->with('category:id,name,slug')
            ->where('slug', $slug)
            ->firstOrFail();

        $isAdmin = (bool) $request->user()?->role === 'admin';
        $providedKey = (string) $request->query('key', '');

        $isPublicPublished = $article->status === 'published'
            && $article->visibility === 'public'
            && (! $article->published_at || $article->published_at->lte(now()));

        $isPrivateAccessible = $article->status === 'published'
            && $article->visibility === 'private'
            && $article->access_token
            && hash_equals($article->access_token, $providedKey);

        if (! $isAdmin && ! $isPublicPublished && ! $isPrivateAccessible) {
            abort(404);
        }

        if (! $isAdmin) {
            $article->increment('view_count');
            $article->refresh();
        }

        $relatedArticles = Article::query()
            ->with('category:id,name,slug')
            ->publiclyVisible()
            ->where('id', '!=', $article->id)
            ->when($article->category_id, fn ($builder) => $builder->where('category_id', $article->category_id))
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.journal.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'isPrivatePreview' => $article->visibility === 'private',
        ]);
    }
}
