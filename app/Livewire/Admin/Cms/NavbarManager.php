<?php

namespace App\Livewire\Admin\Cms;

use App\Models\MenuItem;
use App\Support\AdminActivity;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class NavbarManager extends Component
{
    public string $logoText = 'Wisnu.dev';

    public string $ctaText = 'Hire Me';

    public string $ctaLink = '#contact';

    public ?int $menuItemId = null;

    public string $label = '';

    public string $href = '';

    public int $sortOrder = 0;

    public bool $isVisible = true;

    #[Layout('components.layouts.admin')]
    #[Title('Navbar Management')]
    public function mount(): void
    {
        $navbar = PortfolioContent::get('navbar', []);
        $this->logoText = $navbar['logo_text'] ?? 'Wisnu.dev';
        $this->ctaText = $navbar['cta_text'] ?? 'Hire Me';
        $this->ctaLink = $navbar['cta_link'] ?? '#contact';
    }

    public function saveNavbar(): void
    {
        $this->validate([
            'logoText' => ['required', 'string', 'max:80'],
            'ctaText' => ['required', 'string', 'max:80'],
            'ctaLink' => ['required', 'string', 'max:255'],
        ]);

        PortfolioContent::set('navbar', [
            'logo_text' => $this->logoText,
            'cta_text' => $this->ctaText,
            'cta_link' => $this->ctaLink,
        ]);

        AdminActivity::log('updated', 'navbar', 'Updated navbar global settings.');

        session()->flash('success', 'Navbar settings saved successfully.');
    }

    public function editMenuItem(int $menuItemId): void
    {
        $item = MenuItem::query()->findOrFail($menuItemId);

        $this->menuItemId = $item->id;
        $this->label = $item->label;
        $this->href = $item->href;
        $this->sortOrder = $item->sort_order;
        $this->isVisible = $item->is_visible;
    }

    public function saveMenuItem(): void
    {
        $this->validate([
            'label' => ['required', 'string', 'max:60'],
            'href' => ['required', 'string', 'max:255'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isVisible' => ['required', 'boolean'],
        ]);

        MenuItem::query()->updateOrCreate(
            ['id' => $this->menuItemId],
            [
                'label' => $this->label,
                'href' => $this->href,
                'sort_order' => $this->sortOrder,
                'is_visible' => $this->isVisible,
            ]
        );

        AdminActivity::log('saved', 'navbar', 'Saved navbar menu item.', [
            'label' => $this->label,
        ]);

        $this->resetMenuForm();
        session()->flash('success', 'Menu item saved.');
    }

    public function deleteMenuItem(int $menuItemId): void
    {
        $item = MenuItem::query()->findOrFail($menuItemId);
        $label = $item->label;
        $item->delete();

        AdminActivity::log('deleted', 'navbar', 'Deleted navbar menu item.', [
            'label' => $label,
        ]);

        session()->flash('success', 'Menu item deleted.');
    }

    public function resetMenuForm(): void
    {
        $this->reset(['menuItemId', 'label', 'href']);
        $this->sortOrder = 0;
        $this->isVisible = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('admin.cms.navbar-manager', [
            'menuItems' => MenuItem::query()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }
}
