<?php

namespace App\Livewire\Public;

use App\Models\Article;
use App\Models\Experience;
use App\Models\MenuItem;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Testimonial;
use App\Support\PortfolioContent;
use App\Support\PublicNavbarData;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Index extends Component
{
    #[Layout('components.layouts.portfolio')]
    public function render()
    {
        $brandName = PublicNavbarData::brandName();
        $fallbackBrandTitle = $brandName.' | Fullstack Web Developer';

        $seo = PortfolioContent::get('seo', [
            'title' => $fallbackBrandTitle,
            'description' => 'Portfolio and CMS powered website for fullstack engineering services.',
        ]);

        $seoTitle = trim((string) ($seo['title'] ?? ''));
        $resolvedTitle = ($seoTitle !== '' && strcasecmp($seoTitle, 'Wisnu.dev | Fullstack Web Developer') !== 0)
            ? $seoTitle
            : $fallbackBrandTitle;

        $menuItems = MenuItem::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $skills = Skill::query()
            ->with('portfolioCategory:id,name')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $projects = Project::query()
            ->with('portfolioCategory:id,name,slug')
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $featuredProjects = Project::query()
            ->with('portfolioCategory:id,name,slug')
            ->where('is_visible', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $latestArticles = Article::query()
            ->with('category:id,name,slug')
            ->publiclyVisible()
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        if ($featuredProjects->isEmpty()) {
            $featuredProjects = $projects->take(3)->values();
        }

        $projectCategoryFilters = $projects
            ->map(function (Project $project): array {
                $value = trim((string) ($project->portfolioCategory?->slug ?? $project->category));
                $label = trim((string) ($project->portfolioCategory?->name ?? str_replace('-', ' ', (string) $project->category)));

                return [
                    'value' => $value,
                    'label' => $label !== '' ? ucfirst($label) : 'Uncategorized',
                ];
            })
            ->filter(static fn (array $item): bool => $item['value'] !== '')
            ->unique('value')
            ->values();

        return view('public.index', [
            'seo' => $seo,
            'navbar' => PortfolioContent::get('navbar', []),
            'hero' => PortfolioContent::get('hero', []),
            'about' => PortfolioContent::get('about', []),
            'contactInfo' => PortfolioContent::get('contact_info', []),
            'footer' => PortfolioContent::get('footer', []),
            'menuItems' => $menuItems,
            'skillsByCategory' => $skills->groupBy(fn (Skill $skill): string => trim((string) ($skill->portfolioCategory?->name ?? $skill->category)) ?: 'Other'),
            'projects' => $projects,
            'projectCategoryFilters' => $projectCategoryFilters,
            'featuredProjects' => $featuredProjects,
            'latestArticles' => $latestArticles,
            'experiences' => Experience::query()->where('is_visible', true)->orderBy('sort_order')->orderBy('id')->get(),
            'services' => Service::query()->where('is_visible', true)->orderBy('sort_order')->orderBy('id')->get(),
            'testimonials' => Testimonial::query()->where('is_visible', true)->orderBy('sort_order')->orderBy('id')->get(),
        ])->title($resolvedTitle);
    }
}
