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

    public string $editingLocale = 'id';

    public string $logoText = 'Wisnu.dev';

    public string $logoTextId = 'Wisnu.dev';

    public string $logoTextEn = 'Wisnu.dev';

    public string $brandMode = 'text';

    public string $brandLogoType = 'image';

    public string $brandLogoIcon = 'sparkles';

    public ?string $existingBrandLogoImage = null;

    public $brandLogoImage;

    public string $ctaText = 'Hire Me';

    public string $ctaTextId = 'Hire Me';

    public string $ctaTextEn = 'Hire Me';

    public string $ctaLink = '#contact';

    public ?int $menuItemId = null;

    public string $label = '';

    public string $labelId = '';

    public string $labelEn = '';

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
        $logoText = \App\Support\LocalizedContent::split($navbar['logo_text'] ?? 'Wisnu.dev');
        $ctaText = \App\Support\LocalizedContent::split($navbar['cta_text'] ?? 'Hire Me');

        $this->logoTextId = $logoText['id'];
        $this->logoTextEn = $logoText['en'];
        $this->logoText = $this->logoTextId;
        $this->brandMode = in_array($rawBrandMode, ['text', 'logo', 'combo'], true)
            ? $rawBrandMode
            : 'text';
        $this->brandLogoType = in_array($rawBrandLogoType, ['image', 'icon'], true)
            ? $rawBrandLogoType
            : 'image';
        $this->brandLogoIcon = (string) ($navbar['brand_logo_icon'] ?? 'sparkles');
        $this->existingBrandLogoImage = $navbar['brand_logo_image'] ?? null;
        $this->ctaTextId = $ctaText['id'];
        $this->ctaTextEn = $ctaText['en'];
        $this->ctaText = $this->ctaTextId;
        $this->ctaLink = $navbar['cta_link'] ?? '#contact';
    }

    public function saveNavbar(): void
    {
        $this->logoText = trim($this->logoTextId) !== '' ? trim($this->logoTextId) : trim($this->logoTextEn);
        $this->ctaText = trim($this->ctaTextId) !== '' ? trim($this->ctaTextId) : trim($this->ctaTextEn);

        $this->validate([
            'logoTextId' => ['nullable', 'string', 'max:80'],
            'logoTextEn' => ['nullable', 'string', 'max:80'],
            'brandMode' => ['required', 'in:text,logo,combo'],
            'brandLogoType' => ['required', 'in:image,icon'],
            'brandLogoIcon' => ['nullable', 'string', 'max:80'],
            'brandLogoImage' => ['nullable', 'image', 'max:10240', 'mimes:png,jpg,jpeg,webp,svg'],
            'ctaTextId' => ['required', 'string', 'max:80'],
            'ctaTextEn' => ['required', 'string', 'max:80'],
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
            'logo_text' => \App\Support\LocalizedContent::pack($this->logoTextId, $this->logoTextEn),
            'brand_mode' => $this->brandMode,
            'brand_logo_type' => $this->brandLogoType,
            'brand_logo_icon' => $normalizedLogoIcon,
            'brand_logo_image' => $logoImagePath,
            'cta_text' => \App\Support\LocalizedContent::pack($this->ctaTextId, $this->ctaTextEn),
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
        $label = \App\Support\LocalizedContent::split($item->label);

        $this->menuItemId = $item->id;
        $this->label = $label['id'];
        $this->labelId = $label['id'];
        $this->labelEn = $label['en'];
        $this->href = $item->href;
        $this->sortOrder = $item->sort_order;
        $this->isVisible = $item->is_visible;
    }

    public function saveMenuItem(): void
    {
        $this->label = trim($this->labelId) !== '' ? trim($this->labelId) : trim($this->labelEn);

        $this->validate([
            'labelId' => ['required', 'string', 'max:60'],
            'labelEn' => ['required', 'string', 'max:60'],
            'href' => ['required', 'string', 'max:255'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'isVisible' => ['required', 'boolean'],
        ]);

        MenuItem::query()->updateOrCreate(
            ['id' => $this->menuItemId],
            [
                'label' => json_encode(\App\Support\LocalizedContent::pack($this->labelId, $this->labelEn), JSON_UNESCAPED_UNICODE),
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
        $this->reset(['menuItemId', 'label', 'labelId', 'labelEn', 'href']);
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
