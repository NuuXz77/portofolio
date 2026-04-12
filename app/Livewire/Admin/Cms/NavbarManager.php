<?php

namespace App\Livewire\Admin\Cms;

use App\Models\MenuItem;
use App\Support\AdminActivity;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class NavbarManager extends Component
{
    use WithFileUploads;

    public string $logoText = 'Wisnu.dev';

    public string $brandMode = 'text';

    public string $brandLogoType = 'image';

    public string $brandLogoIcon = 'sparkles';

    public ?string $existingBrandLogoImage = null;

    public $brandLogoImage;

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
        $rawBrandMode = (string) ($navbar['brand_mode'] ?? 'text');
        $rawBrandLogoType = (string) ($navbar['brand_logo_type'] ?? 'image');

        $this->logoText = (string) ($navbar['logo_text'] ?? 'Wisnu.dev');
        $this->brandMode = in_array($rawBrandMode, ['text', 'logo', 'combo'], true)
            ? $rawBrandMode
            : 'text';
        $this->brandLogoType = in_array($rawBrandLogoType, ['image', 'icon'], true)
            ? $rawBrandLogoType
            : 'image';
        $this->brandLogoIcon = (string) ($navbar['brand_logo_icon'] ?? 'sparkles');
        $this->existingBrandLogoImage = $navbar['brand_logo_image'] ?? null;
        $this->ctaText = $navbar['cta_text'] ?? 'Hire Me';
        $this->ctaLink = $navbar['cta_link'] ?? '#contact';
    }

    public function saveNavbar(): void
    {
        $this->validate([
            'logoText' => ['nullable', 'string', 'max:80'],
            'brandMode' => ['required', 'in:text,logo,combo'],
            'brandLogoType' => ['required', 'in:image,icon'],
            'brandLogoIcon' => ['nullable', 'string', 'max:80'],
            'brandLogoImage' => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg,webp,svg'],
            'ctaText' => ['required', 'string', 'max:80'],
            'ctaLink' => ['required', 'string', 'max:255'],
        ]);

        $requiresText = in_array($this->brandMode, ['text', 'combo'], true);
        $requiresMedia = in_array($this->brandMode, ['logo', 'combo'], true);
        $normalizedLogoText = trim($this->logoText);
        $normalizedLogoIcon = trim($this->brandLogoIcon);

        $logoImagePath = $this->existingBrandLogoImage;

        if ($this->brandLogoImage) {
            $logoImagePath = $this->brandLogoImage->store('portfolio/navbar', 'public');
        }

        if ($requiresText && $normalizedLogoText === '') {
            $this->addError('logoText', 'Logo text wajib diisi saat mode menampilkan teks.');

            return;
        }

        if ($requiresMedia && $this->brandLogoType === 'image' && blank($logoImagePath)) {
            $this->addError('brandLogoImage', 'Upload logo image untuk mode logo/image.');

            return;
        }

        if ($requiresMedia && $this->brandLogoType === 'icon' && $normalizedLogoIcon === '') {
            $this->addError('brandLogoIcon', 'Isi nama icon (contoh: sparkles) untuk mode logo/icon.');

            return;
        }

        PortfolioContent::set('navbar', [
            'logo_text' => $normalizedLogoText,
            'brand_mode' => $this->brandMode,
            'brand_logo_type' => $this->brandLogoType,
            'brand_logo_icon' => $normalizedLogoIcon,
            'brand_logo_image' => $logoImagePath,
            'cta_text' => $this->ctaText,
            'cta_link' => $this->ctaLink,
        ]);

        $this->existingBrandLogoImage = $logoImagePath;
        $this->brandLogoImage = null;

        AdminActivity::log('updated', 'navbar', 'Updated navbar global settings.');
        session()->flash('success', 'Navbar settings saved successfully.');
        $this->dispatch('app-toast', type: 'success', message: 'Navbar settings saved successfully.');
    }

    public function clearBrandLogoImage(): void
    {
        $this->existingBrandLogoImage = null;
        $this->brandLogoImage = null;
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
        $this->dispatch('app-toast', type: 'success', message: 'Menu item saved.');
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
        $this->dispatch('app-toast', type: 'success', message: 'Menu item deleted.');
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
