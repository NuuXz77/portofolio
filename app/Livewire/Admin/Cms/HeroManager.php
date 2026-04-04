<?php

namespace App\Livewire\Admin\Cms;

use App\Support\AdminActivity;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class HeroManager extends Component
{
    use WithFileUploads;

    public string $headline = '';

    public string $subheadline = '';

    public string $rolesText = '';

    public string $primaryCtaText = '';

    public string $primaryCtaLink = '';

    public string $secondaryCtaText = '';

    public string $secondaryCtaLink = '';

    public ?string $existingImage = null;

    public $heroImage;

    #[Layout('components.layouts.admin')]
    #[Title('Hero Section')]
    public function mount(): void
    {
        $hero = PortfolioContent::get('hero', []);

        $this->headline = $hero['headline'] ?? '';
        $this->subheadline = $hero['subheadline'] ?? '';
        $this->rolesText = implode(PHP_EOL, $hero['roles'] ?? []);
        $this->primaryCtaText = $hero['primary_cta_text'] ?? '';
        $this->primaryCtaLink = $hero['primary_cta_link'] ?? '#projects';
        $this->secondaryCtaText = $hero['secondary_cta_text'] ?? '';
        $this->secondaryCtaLink = $hero['secondary_cta_link'] ?? '#';
        $this->existingImage = $hero['image'] ?? null;
    }

    public function save(): void
    {
        $this->validate([
            'headline' => ['required', 'string', 'max:180'],
            'subheadline' => ['required', 'string', 'max:500'],
            'rolesText' => ['required', 'string', 'max:500'],
            'primaryCtaText' => ['required', 'string', 'max:80'],
            'primaryCtaLink' => ['required', 'string', 'max:255'],
            'secondaryCtaText' => ['required', 'string', 'max:80'],
            'secondaryCtaLink' => ['required', 'string', 'max:255'],
            'heroImage' => ['nullable', 'image', 'max:2048'],
        ]);

        $image = $this->existingImage;

        if ($this->heroImage) {
            $image = $this->heroImage->store('portfolio/hero', 'public');
        }

        $roles = collect(preg_split('/\r\n|\r|\n/', $this->rolesText))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();

        PortfolioContent::set('hero', [
            'headline' => $this->headline,
            'subheadline' => $this->subheadline,
            'roles' => $roles,
            'primary_cta_text' => $this->primaryCtaText,
            'primary_cta_link' => $this->primaryCtaLink,
            'secondary_cta_text' => $this->secondaryCtaText,
            'secondary_cta_link' => $this->secondaryCtaLink,
            'image' => $image,
        ]);

        $this->existingImage = $image;

        AdminActivity::log('updated', 'hero', 'Updated hero section settings.');
    session()->flash('success', 'Hero section updated.');
    $this->dispatch('app-toast', type: 'success', message: 'Hero section updated.');
    }

    public function render()
    {
        return view('admin.cms.hero-manager');
    }
}
