<?php

namespace App\Livewire\Admin\Cms;

use App\Support\AdminActivity;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class FooterManager extends Component
{
    public string $editingLocale = 'id';

    public string $tagline = '';

    public string $taglineId = '';

    public string $taglineEn = '';

    public string $copyright = '';

    public string $copyrightId = '';

    public string $copyrightEn = '';

    public string $cta = '';

    public string $ctaId = '';

    public string $ctaEn = '';

    public string $socialsText = '';

    #[Layout('components.layouts.admin')]
    #[Title('Footer Management')]
    public function mount(): void
    {
        $footer = PortfolioContent::get('footer', []);
        $tagline = \App\Support\LocalizedContent::split($footer['tagline'] ?? '');
        $copyright = \App\Support\LocalizedContent::split($footer['copyright'] ?? '');
        $cta = \App\Support\LocalizedContent::split($footer['cta'] ?? '');

        $this->taglineId = $tagline['id'];
        $this->taglineEn = $tagline['en'];
        $this->tagline = $this->taglineId;
        $this->copyrightId = $copyright['id'];
        $this->copyrightEn = $copyright['en'];
        $this->copyright = $this->copyrightId;
        $this->ctaId = $cta['id'];
        $this->ctaEn = $cta['en'];
        $this->cta = $this->ctaId;

        $this->socialsText = collect($footer['socials'] ?? [])
            ->map(function (array $item): ?string {
                $labelMap = \App\Support\LocalizedContent::split($item['label'] ?? '');
                $labelId = $labelMap['id'];
                $labelEn = $labelMap['en'];
                $link = trim((string) ($item['link'] ?? ''));

                if ($labelId === '' || $labelEn === '' || $link === '') {
                    return null;
                }

                $icon = $this->resolveSocialIcon($labelId.' '.$labelEn, $link, (string) ($item['icon'] ?? ''));

                return $labelId.'|'.$labelEn.'|'.$link.'|'.$icon;
            })
            ->filter()
            ->implode(PHP_EOL);
    }

    public function save(): void
    {
        $this->tagline = trim($this->taglineId) !== '' ? trim($this->taglineId) : trim($this->taglineEn);
        $this->copyright = trim($this->copyrightId) !== '' ? trim($this->copyrightId) : trim($this->copyrightEn);
        $this->cta = trim($this->ctaId) !== '' ? trim($this->ctaId) : trim($this->ctaEn);

        $this->validate([
            'taglineId' => ['required', 'string', 'max:200'],
            'taglineEn' => ['required', 'string', 'max:200'],
            'copyrightId' => ['required', 'string', 'max:200'],
            'copyrightEn' => ['required', 'string', 'max:200'],
            'ctaId' => ['required', 'string', 'max:200'],
            'ctaEn' => ['required', 'string', 'max:200'],
            'socialsText' => ['required', 'string', 'max:4000'],
        ]);

        [$socials, $invalidLines] = $this->parseSocialLines($this->socialsText);

        if ($invalidLines !== []) {
            $lineInfo = implode(', ', $invalidLines);
            $this->addError('socialsText', "Format social links tidak valid pada baris: {$lineInfo}. Gunakan LabelID|LabelEN|Link|Icon.");

            return;
        }

        if ($socials === []) {
            $this->addError('socialsText', 'Isi minimal 1 social link dengan format LabelID|LabelEN|Link|Icon.');

            return;
        }

        PortfolioContent::set('footer', [
            'tagline' => \App\Support\LocalizedContent::pack($this->taglineId, $this->taglineEn),
            'copyright' => \App\Support\LocalizedContent::pack($this->copyrightId, $this->copyrightEn),
            'cta' => \App\Support\LocalizedContent::pack($this->ctaId, $this->ctaEn),
            'socials' => $socials,
        ]);

        AdminActivity::log('updated', 'footer', 'Updated footer settings.');
    session()->flash('success', 'Footer updated successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Footer updated successfully.');
    }

    public function render()
    {
        return view('admin.cms.footer-manager');
    }

    /**
    * @return array{0: array<int, array{label: array{id: string, en: string}, link: string, icon: string}>, 1: array<int, int>}
     */
    protected function parseSocialLines(string $raw): array
    {
        $socials = [];
        $invalidLines = [];
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];

        foreach ($lines as $index => $line) {
            $lineNumber = $index + 1;
            $trimmed = trim($line);

            if ($trimmed === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $trimmed, 4));

            if (count($parts) < 3) {
                $invalidLines[] = $lineNumber;

                continue;
            }

            if (count($parts) >= 4) {
                $labelId = (string) ($parts[0] ?? '');
                $labelEn = (string) ($parts[1] ?? '');
                $link = (string) ($parts[2] ?? '');
                $icon = (string) ($parts[3] ?? '');
            } else {
                $labelId = (string) ($parts[0] ?? '');
                $labelEn = $labelId;
                $link = (string) ($parts[1] ?? '');
                $icon = (string) ($parts[2] ?? '');
            }

            if ($labelId === '' || $labelEn === '' || $link === '') {
                $invalidLines[] = $lineNumber;

                continue;
            }

            $socials[] = [
                'label' => \App\Support\LocalizedContent::pack($labelId, $labelEn),
                'link' => $link,
                'icon' => $this->resolveSocialIcon($labelId.' '.$labelEn, $link, $icon),
            ];
        }

        return [$socials, $invalidLines];
    }

    protected function resolveSocialIcon(string $label, string $link, string $icon = ''): string
    {
        $normalizedIcon = strtolower(trim($icon));
        $normalizedIcon = str_replace([' ', '_'], '-', $normalizedIcon);

        if (in_array($normalizedIcon, ['brand-linkedin', 'linkedin'], true)) {
            return 'brand-linkedin';
        }

        if (in_array($normalizedIcon, ['brand-x', 'x', 'twitter'], true)) {
            return 'brand-x';
        }

        if (in_array($normalizedIcon, ['brand-github', 'github'], true) || str_contains($normalizedIcon, 'github')) {
            return 'brand-github';
        }

        if (in_array($normalizedIcon, ['wa', 'whatsapp'], true)) {
            return 'message-circle';
        }

        if (in_array($normalizedIcon, ['email'], true)) {
            return 'mail';
        }

        if ($normalizedIcon !== '') {
            return $normalizedIcon;
        }

        $labelAndLink = strtolower(trim($label.' '.$link));

        if (str_contains($labelAndLink, 'linkedin')) {
            return 'brand-linkedin';
        }

        if (str_contains($labelAndLink, 'twitter') || str_contains($labelAndLink, 'x.com')) {
            return 'brand-x';
        }

        if (str_contains($labelAndLink, 'github')) {
            return 'brand-github';
        }

        if (str_contains($labelAndLink, 'instagram')) {
            return 'instagram';
        }

        if (str_contains($labelAndLink, 'youtube')) {
            return 'youtube';
        }

        if (str_contains($labelAndLink, 'whatsapp') || str_contains($labelAndLink, 'wa.me')) {
            return 'message-circle';
        }

        if (str_contains($labelAndLink, 'mail') || str_contains($labelAndLink, '@')) {
            return 'mail';
        }

        return 'link-2';
    }
}
