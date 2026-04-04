<?php

namespace App\Livewire\Admin\Cms;

use App\Support\AdminActivity;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class SettingsManager extends Component
{
    public string $siteTitle = '';

    public string $siteDescription = '';

    public string $siteKeywords = '';

    #[Layout('components.layouts.admin')]
    #[Title('Settings')]
    public function mount(): void
    {
        $seo = PortfolioContent::get('seo', []);

        $this->siteTitle = $seo['title'] ?? '';
        $this->siteDescription = $seo['description'] ?? '';
        $this->siteKeywords = $seo['keywords'] ?? '';
    }

    public function save(): void
    {
        $this->validate([
            'siteTitle' => ['required', 'string', 'max:160'],
            'siteDescription' => ['required', 'string', 'max:320'],
            'siteKeywords' => ['nullable', 'string', 'max:300'],
        ]);

        PortfolioContent::set('seo', [
            'title' => $this->siteTitle,
            'description' => $this->siteDescription,
            'keywords' => $this->siteKeywords,
        ]);

        AdminActivity::log('updated', 'settings', 'Updated SEO settings.');
    session()->flash('success', 'Settings saved successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Settings saved successfully.');
    }

    public function render()
    {
        return view('admin.cms.settings-manager');
    }
}
