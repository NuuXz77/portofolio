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
            ->map(fn (array $stat) => trim((string) ($stat['label'] ?? '')).' | '.trim((string) ($stat['value'] ?? '')))
            ->implode(PHP_EOL);
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

        [$stats, $invalidLines] = $this->parseStatsLines($this->statsText);

        if ($invalidLines !== []) {
            $lineInfo = implode(', ', $invalidLines);
            $this->addError('statsText', "Format stats tidak valid pada baris: {$lineInfo}. Gunakan Label | Value (atau Label:Value / Label - Value).");

            return;
        }

        if ($stats === []) {
            $this->addError('statsText', 'Isi minimal 1 baris stats dengan format Label | Value.');

            return;
        }

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
