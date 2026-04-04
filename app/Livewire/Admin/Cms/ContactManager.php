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

    public string $email = '';

    public string $whatsapp = '';

    public string $linkedin = '';

    public string $github = '';

    public string $search = '';

    public string $statusFilter = 'all';

    #[Layout('components.layouts.admin')]
    #[Title('Contact Info & Inbox')]
    public function mount(): void
    {
        $contact = PortfolioContent::get('contact_info', []);
        $this->email = $contact['email'] ?? '';
        $this->whatsapp = $contact['whatsapp'] ?? '';
        $this->linkedin = $contact['linkedin'] ?? '';
        $this->github = $contact['github'] ?? '';
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
        $this->validate([
            'email' => ['required', 'email', 'max:180'],
            'whatsapp' => ['required', 'string', 'max:80'],
            'linkedin' => ['required', 'string', 'max:255'],
            'github' => ['required', 'string', 'max:255'],
        ]);

        PortfolioContent::set('contact_info', [
            'email' => $this->email,
            'whatsapp' => $this->whatsapp,
            'linkedin' => $this->linkedin,
            'github' => $this->github,
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
