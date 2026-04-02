<?php

namespace App\Livewire\Public;

use App\Models\Experience;
use App\Models\MenuItem;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Testimonial;
use App\Support\PortfolioContent;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Index extends Component
{
    #[Layout('components.layouts.portfolio')]
    public function render()
    {
        $seo = PortfolioContent::get('seo', [
            'title' => 'Wisnu.dev | Fullstack Web Developer',
            'description' => 'Portfolio and CMS powered website for fullstack engineering services.',
        ]);

        $menuItems = MenuItem::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $skills = Skill::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $projects = Project::query()
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $featuredProjects = Project::query()
            ->where('is_visible', true)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($featuredProjects->isEmpty()) {
            $featuredProjects = $projects->take(3)->values();
        }

        return view('public.index', [
            'seo' => $seo,
            'navbar' => PortfolioContent::get('navbar', []),
            'hero' => PortfolioContent::get('hero', []),
            'about' => PortfolioContent::get('about', []),
            'contactInfo' => PortfolioContent::get('contact_info', []),
            'footer' => PortfolioContent::get('footer', []),
            'menuItems' => $menuItems,
            'skillsByCategory' => $skills->groupBy('category'),
            'projects' => $projects,
            'featuredProjects' => $featuredProjects,
            'experiences' => Experience::query()->where('is_visible', true)->orderBy('sort_order')->orderBy('id')->get(),
            'services' => Service::query()->where('is_visible', true)->orderBy('sort_order')->orderBy('id')->get(),
            'testimonials' => Testimonial::query()->where('is_visible', true)->orderBy('sort_order')->orderBy('id')->get(),
        ])->title($seo['title'] ?? 'Wisnu.dev | Fullstack Web Developer');
    }
}
