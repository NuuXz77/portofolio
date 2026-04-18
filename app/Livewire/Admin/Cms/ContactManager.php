<?php

namespace App\Livewire\Admin\Cms;

use App\Models\ContactMessage;
use App\Support\AdminActivity;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ContactManager extends Component
{
    use WithPagination;

    public string $editingLocale = 'id';

    public string $email = '';

    public string $whatsapp = '';

    public string $linkedin = '';

    public string $github = '';

    public string $sectionBadge = '';

    public string $sectionBadgeId = '';

    public string $sectionBadgeEn = '';

    public string $sectionTitle = '';

    public string $sectionTitleId = '';

    public string $sectionTitleEn = '';

    public string $sectionDescription = '';

    public string $sectionDescriptionId = '';

    public string $sectionDescriptionEn = '';

    public string $formTitle = '';

    public string $formTitleId = '';

    public string $formTitleEn = '';

    public string $submitText = '';

    public string $submitTextId = '';

    public string $submitTextEn = '';

    public string $search = '';

    public string $statusFilter = 'all';

    #[Layout('components.layouts.admin')]
    #[Title('Contact Info & Inbox')]
    public function mount(): void
    {
        $contact = PortfolioContent::get('contact_info', []);

        $sectionBadge = \App\Support\LocalizedContent::split($contact['contact_badge'] ?? __('common.contact'));
        $sectionTitle = \App\Support\LocalizedContent::split($contact['contact_title'] ?? "Let's build something meaningful");
        $sectionDescription = \App\Support\LocalizedContent::split($contact['contact_description'] ?? 'Open for freelance and long-term collaboration. Reach out anytime and I will get back to you quickly.');
        $formTitle = \App\Support\LocalizedContent::split($contact['form_title'] ?? __('common.send_message'));
        $submitText = \App\Support\LocalizedContent::split($contact['submit_text'] ?? __('common.send_message'));

        $this->email = $contact['email'] ?? '';
        $this->whatsapp = $contact['whatsapp'] ?? '';
        $this->linkedin = $contact['linkedin'] ?? '';
        $this->github = $contact['github'] ?? '';

        $this->sectionBadgeId = $sectionBadge['id'];
        $this->sectionBadgeEn = $sectionBadge['en'];
        $this->sectionBadge = $this->sectionBadgeId;

        $this->sectionTitleId = $sectionTitle['id'];
        $this->sectionTitleEn = $sectionTitle['en'];
        $this->sectionTitle = $this->sectionTitleId;

        $this->sectionDescriptionId = $sectionDescription['id'];
        $this->sectionDescriptionEn = $sectionDescription['en'];
        $this->sectionDescription = $this->sectionDescriptionId;

        $this->formTitleId = $formTitle['id'];
        $this->formTitleEn = $formTitle['en'];
        $this->formTitle = $this->formTitleId;

        $this->submitTextId = $submitText['id'];
        $this->submitTextEn = $submitText['en'];
        $this->submitText = $this->submitTextId;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function saveContactInfo(): void
    {
        $this->sectionBadge = trim($this->sectionBadgeId) !== '' ? trim($this->sectionBadgeId) : trim($this->sectionBadgeEn);
        $this->sectionTitle = trim($this->sectionTitleId) !== '' ? trim($this->sectionTitleId) : trim($this->sectionTitleEn);
        $this->sectionDescription = trim($this->sectionDescriptionId) !== '' ? trim($this->sectionDescriptionId) : trim($this->sectionDescriptionEn);
        $this->formTitle = trim($this->formTitleId) !== '' ? trim($this->formTitleId) : trim($this->formTitleEn);
        $this->submitText = trim($this->submitTextId) !== '' ? trim($this->submitTextId) : trim($this->submitTextEn);

        $this->validate([
            'email' => ['required', 'email', 'max:180'],
            'whatsapp' => ['required', 'string', 'max:80'],
            'linkedin' => ['required', 'string', 'max:255'],
            'github' => ['required', 'string', 'max:255'],
            'sectionBadgeId' => ['required', 'string', 'max:120'],
            'sectionBadgeEn' => ['required', 'string', 'max:120'],
            'sectionTitleId' => ['required', 'string', 'max:220'],
            'sectionTitleEn' => ['required', 'string', 'max:220'],
            'sectionDescriptionId' => ['required', 'string', 'max:2000'],
            'sectionDescriptionEn' => ['required', 'string', 'max:2000'],
            'formTitleId' => ['required', 'string', 'max:120'],
            'formTitleEn' => ['required', 'string', 'max:120'],
            'submitTextId' => ['required', 'string', 'max:80'],
            'submitTextEn' => ['required', 'string', 'max:80'],
        ]);

        PortfolioContent::set('contact_info', [
            'email' => $this->email,
            'whatsapp' => $this->whatsapp,
            'linkedin' => $this->linkedin,
            'github' => $this->github,
            'contact_badge' => \App\Support\LocalizedContent::pack($this->sectionBadgeId, $this->sectionBadgeEn),
            'contact_title' => \App\Support\LocalizedContent::pack($this->sectionTitleId, $this->sectionTitleEn),
            'contact_description' => \App\Support\LocalizedContent::pack($this->sectionDescriptionId, $this->sectionDescriptionEn),
            'form_title' => \App\Support\LocalizedContent::pack($this->formTitleId, $this->formTitleEn),
            'submit_text' => \App\Support\LocalizedContent::pack($this->submitTextId, $this->submitTextEn),
        ]);

        AdminActivity::log('updated', 'contact', 'Updated contact info settings.');
    session()->flash('success', 'Contact information updated.');
    $this->dispatch('app-toast', type: 'success', message: 'Contact information updated.');
    }

    public function markAsRead(int $messageId): void
    {
        ContactMessage::query()->whereKey($messageId)->update(['is_read' => true]);

        AdminActivity::log('updated', 'contact', 'Marked inbox message as read.', [
            'message_id' => $messageId,
        ]);
    }

    public function deleteMessage(int $messageId): void
    {
        ContactMessage::query()->whereKey($messageId)->delete();

        AdminActivity::log('deleted', 'contact', 'Deleted inbox message.', [
            'message_id' => $messageId,
        ]);
    session()->flash('success', 'Message deleted.');
    $this->dispatch('app-toast', type: 'success', message: 'Message deleted.');
    }

    public function render()
    {
        $query = ContactMessage::query()
            ->when($this->search !== '', function ($builder) {
                $builder->where(function ($inner) {
                    $inner->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('message', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter === 'read', fn ($builder) => $builder->where('is_read', true))
            ->when($this->statusFilter === 'unread', fn ($builder) => $builder->where('is_read', false))
            ->latest();

        return view('admin.cms.contact-manager', [
            'messages' => $query->paginate(10),
        ]);
    }
}
