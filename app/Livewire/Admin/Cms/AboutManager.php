<?php

namespace App\Livewire\Admin\Cms;

use App\Support\AdminActivity;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class AboutManager extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $description = '';

    public string $statsText = '';

    public ?string $existingImage = null;

    public $profileImage;

    #[Layout('components.layouts.admin')]
    #[Title('About Section')]
    public function mount(): void
    {
        $about = PortfolioContent::get('about', []);
        $stats = $about['stats'] ?? [];

        $this->title = $about['title'] ?? '';
        $this->description = $about['description'] ?? '';
        $this->existingImage = $about['image'] ?? null;

        $this->statsText = collect($stats)
            ->map(fn (array $stat) => ($stat['label'] ?? '').'|'.($stat['value'] ?? ''))
            ->implode(PHP_EOL);
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:6000'],
            'statsText' => ['required', 'string', 'max:1200'],
            'profileImage' => ['nullable', 'image', 'max:2048'],
        ]);

        $image = $this->existingImage;

        if ($this->profileImage) {
            $image = $this->profileImage->store('portfolio/about', 'public');
        }

        $stats = collect(preg_split('/\r\n|\r|\n/', $this->statsText))
            ->map(function (string $line) {
                [$label, $value] = array_pad(explode('|', $line, 2), 2, '');

                return [
                    'label' => trim($label),
                    'value' => trim($value),
                ];
            })
            ->filter(fn (array $item) => $item['label'] !== '' && $item['value'] !== '')
            ->values()
            ->all();

        PortfolioContent::set('about', [
            'title' => $this->title,
            'description' => $this->description,
            'stats' => $stats,
            'image' => $image,
        ]);

        $this->existingImage = $image;

        AdminActivity::log('updated', 'about', 'Updated about section content.');
    session()->flash('success', 'About section updated.');
    $this->dispatch('app-toast', type: 'success', message: 'About section updated.');
    }

    public function render()
    {
        return view('admin.cms.about-manager');
    }
}
