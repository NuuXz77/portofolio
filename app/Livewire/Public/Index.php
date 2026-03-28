<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

class Index extends Component
{
    #[Layout('components.layouts.guest')]
    #[Title('Home')]
    public function render()
    {
        return view('public.index');
    }
}
