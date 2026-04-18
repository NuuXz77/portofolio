<?php

namespace App\Livewire\Admin\Cms;

use Illuminate\Support\Str;
use App\Support\AdminActivity;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class HeroManager extends Component
{
    use WithFileUploads;

    public string $editingLocale = 'id';

    public string $headline = '';

    public string $headlineId = '';

    public string $headlineEn = '';

    public string $subheadline = '';

    public string $subheadlineId = '';

    public string $subheadlineEn = '';

    public string $rolesText = '';

    public string $rolesTextId = '';

    public string $rolesTextEn = '';

    public string $primaryCtaText = '';

    public string $primaryCtaTextId = '';

    public string $primaryCtaTextEn = '';

    public string $primaryCtaLink = '';

    public string $secondaryCtaText = '';

    public string $secondaryCtaTextId = '';

    public string $secondaryCtaTextEn = '';

    public string $secondaryCtaLink = '';

    public ?string $existingImage = null;

    public ?string $existingSecondaryCtaFile = null;

    public $heroImage;

    public $secondaryCtaFile;

    #[Layout('components.layouts.admin')]
    #[Title('Hero Section')]
    public function mount(): void
    {
        $hero = PortfolioContent::get('hero', []);

        $headline = \App\Support\LocalizedContent::split($hero['headline'] ?? '');
        $subheadline = \App\Support\LocalizedContent::split($hero['subheadline'] ?? '');
        $primaryCtaText = \App\Support\LocalizedContent::split($hero['primary_cta_text'] ?? '');
        $secondaryCtaText = \App\Support\LocalizedContent::split($hero['secondary_cta_text'] ?? '');
        $roles = $hero['roles'] ?? [];

        if (is_array($roles) && (array_key_exists('id', $roles) || array_key_exists('en', $roles))) {
            $rolesId = is_array($roles['id'] ?? null) ? (array) ($roles['id'] ?? []) : [];
            $rolesEn = is_array($roles['en'] ?? null) ? (array) ($roles['en'] ?? []) : [];
        } else {
            $rolesId = is_array($roles) ? $roles : [];
            $rolesEn = is_array($roles) ? $roles : [];
        }

        $this->headlineId = $headline['id'];
        $this->headlineEn = $headline['en'];
        $this->headline = $this->headlineId;
        $this->subheadlineId = $subheadline['id'];
        $this->subheadlineEn = $subheadline['en'];
        $this->subheadline = $this->subheadlineId;
        $this->rolesTextId = implode(PHP_EOL, $rolesId);
        $this->rolesTextEn = implode(PHP_EOL, $rolesEn);
        $this->rolesText = $this->rolesTextId;
        $this->primaryCtaTextId = $primaryCtaText['id'];
        $this->primaryCtaTextEn = $primaryCtaText['en'];
        $this->primaryCtaText = $this->primaryCtaTextId;
        $this->primaryCtaLink = $hero['primary_cta_link'] ?? '#projects';
        $this->secondaryCtaTextId = $secondaryCtaText['id'];
        $this->secondaryCtaTextEn = $secondaryCtaText['en'];
        $this->secondaryCtaText = $this->secondaryCtaTextId;
        $this->secondaryCtaLink = $hero['secondary_cta_link'] ?? '#';
        $this->existingImage = $hero['image'] ?? null;
        $this->existingSecondaryCtaFile = $hero['secondary_cta_file'] ?? null;
    }

    public function save(): void
    {
        $this->headline = trim($this->headlineId) !== '' ? trim($this->headlineId) : trim($this->headlineEn);
        $this->subheadline = trim($this->subheadlineId) !== '' ? trim($this->subheadlineId) : trim($this->subheadlineEn);
        $this->primaryCtaText = trim($this->primaryCtaTextId) !== '' ? trim($this->primaryCtaTextId) : trim($this->primaryCtaTextEn);
        $this->secondaryCtaText = trim($this->secondaryCtaTextId) !== '' ? trim($this->secondaryCtaTextId) : trim($this->secondaryCtaTextEn);
        $this->rolesText = trim($this->rolesTextId) !== '' ? trim($this->rolesTextId) : trim($this->rolesTextEn);

        $this->validate([
            'headlineId' => ['required', 'string', 'max:180'],
            'headlineEn' => ['required', 'string', 'max:180'],
            'subheadlineId' => ['required', 'string', 'max:500'],
            'subheadlineEn' => ['required', 'string', 'max:500'],
            'rolesTextId' => ['required', 'string', 'max:500'],
            'rolesTextEn' => ['required', 'string', 'max:500'],
            'primaryCtaTextId' => ['required', 'string', 'max:80'],
            'primaryCtaTextEn' => ['required', 'string', 'max:80'],
            'primaryCtaLink' => ['required', 'string', 'max:255'],
            'secondaryCtaTextId' => ['required', 'string', 'max:80'],
            'secondaryCtaTextEn' => ['required', 'string', 'max:80'],
            'secondaryCtaLink' => ['required', 'string', 'max:255'],
            'heroImage' => ['nullable', 'image', 'max:10240'],
            'secondaryCtaFile' => ['nullable', 'file', 'max:10240', 'mimes:pdf,doc,docx'],
        ]);

        $image = $this->existingImage;
        $secondaryCtaFilePath = $this->existingSecondaryCtaFile;

        if ($this->heroImage) {
            $image = $this->heroImage->storeAs(
                'portfolio/hero',
                $this->buildUploadFilename($this->heroImage, 'hero-image'),
                'public'
            );
        }

        if ($this->secondaryCtaFile) {
            $secondaryCtaFilePath = $this->secondaryCtaFile->storeAs(
                'portfolio/hero/cv',
                $this->buildUploadFilename($this->secondaryCtaFile, 'cv-file'),
                'public'
            );
        }

        $rolesId = collect(preg_split('/\r\n|\r|\n/', $this->rolesTextId))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();

        $rolesEn = collect(preg_split('/\r\n|\r|\n/', $this->rolesTextEn))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->values()
            ->all();

        PortfolioContent::set('hero', [
            'headline' => \App\Support\LocalizedContent::pack($this->headlineId, $this->headlineEn),
            'subheadline' => \App\Support\LocalizedContent::pack($this->subheadlineId, $this->subheadlineEn),
            'roles' => [
                'id' => $rolesId,
                'en' => $rolesEn,
            ],
            'primary_cta_text' => \App\Support\LocalizedContent::pack($this->primaryCtaTextId, $this->primaryCtaTextEn),
            'primary_cta_link' => $this->primaryCtaLink,
            'secondary_cta_text' => \App\Support\LocalizedContent::pack($this->secondaryCtaTextId, $this->secondaryCtaTextEn),
            'secondary_cta_link' => $this->secondaryCtaLink,
            'secondary_cta_file' => $secondaryCtaFilePath,
            'image' => $image,
        ]);

        $this->existingImage = $image;
        $this->existingSecondaryCtaFile = $secondaryCtaFilePath;
        $this->secondaryCtaFile = null;

        AdminActivity::log('updated', 'hero', 'Updated hero section settings.');
        session()->flash('success', 'Hero section updated.');
        $this->dispatch('app-toast', type: 'success', message: 'Hero section updated.');
    }

    public function clearSecondaryCtaFile(): void
    {
        $this->existingSecondaryCtaFile = null;
        $this->secondaryCtaFile = null;
    }

    private function buildUploadFilename($uploadedFile, string $fallbackBase): string
    {
        $originalName = pathinfo((string) $uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $baseName = Str::slug($originalName !== '' ? $originalName : $fallbackBase, '-');

        if ($baseName === '') {
            $baseName = $fallbackBase;
        }

        $timestamp = now()->format('Y-m-d_H-i-s');
        $extension = strtolower((string) $uploadedFile->getClientOriginalExtension());

        if ($extension === '') {
            $extension = strtolower((string) $uploadedFile->extension());
        }

        return $extension !== ''
            ? sprintf('%s_%s.%s', $baseName, $timestamp, $extension)
            : sprintf('%s_%s', $baseName, $timestamp);
    }

    public function render()
    {
        return view('admin.cms.hero-manager');
    }
}
