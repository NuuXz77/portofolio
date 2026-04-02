<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    #[Layout('components.layouts.auth')]
    #[Title('Admin Login')]
    public function render()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    public function login(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if (! Auth::attempt($credentials, $this->remember)) {
            $this->addError('email', 'Invalid email or password.');

            return;
        }

        session()->regenerate();

        $this->redirectRoute('admin.dashboard', navigate: true);
    }
}
