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

    public string $editingLocale = 'id';

    public string $title = '';

    public string $titleId = '';

    public string $titleEn = '';

    public string $description = '';

    public string $descriptionId = '';

    public string $descriptionEn = '';

    public string $statsText = '';

    public string $statsTextId = '';

    public string $statsTextEn = '';

    public ?string $existingImage = null;

    public $profileImage;

    #[Layout('components.layouts.admin')]
    #[Title('About Section')]
    public function mount(): void
    {
        $about = PortfolioContent::get('about', []);
        $title = \App\Support\LocalizedContent::split($about['title'] ?? '');
        $description = \App\Support\LocalizedContent::split($about['description'] ?? '');
        $stats = $about['stats'] ?? [];

        $this->titleId = $title['id'];
        $this->titleEn = $title['en'];
        $this->title = $this->titleId;
        $this->descriptionId = $description['id'];
        $this->descriptionEn = $description['en'];
        $this->description = $this->descriptionId;
        $this->existingImage = $about['profile_image'] ?? $about['image'] ?? null;

        if (is_array($stats) && (array_key_exists('id', $stats) || array_key_exists('en', $stats))) {
            $statsId = is_array($stats['id'] ?? null) ? (array) ($stats['id'] ?? []) : [];
            $statsEn = is_array($stats['en'] ?? null) ? (array) ($stats['en'] ?? []) : [];
        } else {
            $statsId = is_array($stats) ? $stats : [];
            $statsEn = is_array($stats) ? $stats : [];
        }

        $this->statsTextId = collect($statsId)
            ->map(fn (array $stat) => trim((string) \App\Support\LocalizedContent::resolve($stat['label'] ?? '', 'id')).' | '.trim((string) \App\Support\LocalizedContent::resolve($stat['value'] ?? '', 'id')))
            ->implode(PHP_EOL);

        $this->statsTextEn = collect($statsEn)
            ->map(fn (array $stat) => trim((string) \App\Support\LocalizedContent::resolve($stat['label'] ?? '', 'en')).' | '.trim((string) \App\Support\LocalizedContent::resolve($stat['value'] ?? '', 'en')))
            ->implode(PHP_EOL);

        $this->statsText = $this->statsTextId;
    }

    /**
     * @return array{0: array<int, array{label: string, value: string}>, 1: array<int, int>}
     */
    protected function parseStatsLines(string $raw): array
    {
        $stats = [];
        $invalidLines = [];
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];

        foreach ($lines as $index => $line) {
            $lineNumber = $index + 1;
            $trimmed = trim($line);

            if ($trimmed === '') {
                continue;
            }

            $label = '';
            $value = '';

            if (str_contains($trimmed, '|')) {
                [$label, $value] = array_pad(explode('|', $trimmed, 2), 2, '');
            } elseif (str_contains($trimmed, ':')) {
                [$label, $value] = array_pad(explode(':', $trimmed, 2), 2, '');
            } elseif (preg_match('/^(.+?)\s*[-–—]\s*(.+)$/u', $trimmed, $matches) === 1) {
                $label = $matches[1] ?? '';
                $value = $matches[2] ?? '';
            } else {
                $invalidLines[] = $lineNumber;

                continue;
            }

            $label = trim($label);
            $value = trim($value);

            if ($label === '' || $value === '') {
                $invalidLines[] = $lineNumber;

                continue;
            }

            $stats[] = [
                'label' => $label,
                'value' => $value,
            ];
        }

        return [$stats, $invalidLines];
    }

    public function save(): void
    {
        $this->title = trim($this->titleId) !== '' ? trim($this->titleId) : trim($this->titleEn);
        $this->description = trim($this->descriptionId) !== '' ? trim($this->descriptionId) : trim($this->descriptionEn);
        $this->statsText = trim($this->statsTextId) !== '' ? trim($this->statsTextId) : trim($this->statsTextEn);

        $this->validate([
            'titleId' => ['required', 'string', 'max:180'],
            'titleEn' => ['required', 'string', 'max:180'],
            'descriptionId' => ['required', 'string', 'max:6000'],
            'descriptionEn' => ['required', 'string', 'max:6000'],
            'statsTextId' => ['required', 'string', 'max:2000'],
            'statsTextEn' => ['required', 'string', 'max:2000'],
            'profileImage' => ['nullable', 'image', 'max:10240'],
        ]);

        $image = $this->existingImage;

        if ($this->profileImage) {
            $image = $this->profileImage->store('portfolio/about', 'public');
        }

        [$statsId, $invalidLinesId] = $this->parseStatsLines($this->statsTextId);
        [$statsEn, $invalidLinesEn] = $this->parseStatsLines($this->statsTextEn);

        $invalidLines = array_values(array_unique([...$invalidLinesId, ...$invalidLinesEn]));

        if ($invalidLines !== []) {
            $lineInfo = implode(', ', $invalidLines);
            $this->addError('statsTextId', "Format stats tidak valid pada baris: {$lineInfo}. Gunakan Label | Value (atau Label:Value / Label - Value).");
            $this->addError('statsTextEn', "Stats format is invalid on line(s): {$lineInfo}. Use Label | Value (or Label:Value / Label - Value).");

            return;
        }

        if ($statsId === [] || $statsEn === []) {
            $this->addError('statsTextId', 'Isi minimal 1 baris stats (ID) dengan format Label | Value.');
            $this->addError('statsTextEn', 'Fill at least 1 stats line (EN) with Label | Value format.');

            return;
        }

        PortfolioContent::set('about', [
            'title' => \App\Support\LocalizedContent::pack($this->titleId, $this->titleEn),
            'description' => \App\Support\LocalizedContent::pack($this->descriptionId, $this->descriptionEn),
            'stats' => [
                'id' => $statsId,
                'en' => $statsEn,
            ],
            'profile_image' => $image,
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
