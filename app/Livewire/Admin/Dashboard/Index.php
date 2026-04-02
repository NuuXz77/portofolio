<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Testimonial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Index extends Component
{
    #[Layout('components.layouts.admin')]
    #[Title('Dashboard')]
    public function render()
    {
        return view('admin.dashboard.index', [
            'totalProjects' => Project::query()->count(),
            'totalSkills' => Skill::query()->count(),
            'totalTestimonials' => Testimonial::query()->count(),
            'totalMessages' => ContactMessage::query()->count(),
            'unreadMessages' => ContactMessage::query()->where('is_read', false)->count(),
            'activities' => ActivityLog::query()->latest()->take(8)->get(),
            'quickActions' => [
                ['label' => 'Add Project', 'route' => 'admin.projects'],
                ['label' => 'Update Hero Section', 'route' => 'admin.hero'],
                ['label' => 'Manage Skills', 'route' => 'admin.skills'],
                ['label' => 'Review Inbox', 'route' => 'admin.contact'],
            ],
        ]);
    }
}
