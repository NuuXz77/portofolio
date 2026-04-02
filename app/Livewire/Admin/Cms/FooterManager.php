<?php

namespace App\Livewire\Admin\Cms;

use App\Support\AdminActivity;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class FooterManager extends Component
{
    public string $tagline = '';

    public string $copyright = '';

    public string $cta = '';

    public string $socialsText = '';

    #[Layout('components.layouts.admin')]
    #[Title('Footer Management')]
    public function mount(): void
    {
        $footer = PortfolioContent::get('footer', []);
        $this->tagline = $footer['tagline'] ?? '';
        $this->copyright = $footer['copyright'] ?? '';
        $this->cta = $footer['cta'] ?? '';

        $this->socialsText = collect($footer['socials'] ?? [])
            ->map(fn (array $item) => ($item['label'] ?? '').'|'.($item['link'] ?? ''))
            ->implode(PHP_EOL);
    }

    public function save(): void
    {
        $this->validate([
            'tagline' => ['required', 'string', 'max:200'],
            'copyright' => ['required', 'string', 'max:200'],
            'cta' => ['required', 'string', 'max:200'],
            'socialsText' => ['required', 'string', 'max:2000'],
        ]);

        $socials = collect(preg_split('/\r\n|\r|\n/', $this->socialsText))
            ->map(function (string $line) {
                [$label, $link] = array_pad(explode('|', $line, 2), 2, '');

                return [
                    'label' => trim($label),
                    'link' => trim($link),
                ];
            })
            ->filter(fn (array $item) => $item['label'] !== '' && $item['link'] !== '')
            ->values()
            ->all();

        PortfolioContent::set('footer', [
            'tagline' => $this->tagline,
            'copyright' => $this->copyright,
            'cta' => $this->cta,
            'socials' => $socials,
        ]);

        AdminActivity::log('updated', 'footer', 'Updated footer settings.');

        session()->flash('success', 'Footer updated successfully.');
    }

    public function render()
    {
        return view('admin.cms.footer-manager');
    }
}
