<?php

use App\Http\Controllers\ContactMessageController;
use App\Livewire\Admin\Cms\AboutManager;
use App\Livewire\Admin\Cms\ContactManager;
use App\Livewire\Admin\Cms\ExperiencesManager;
use App\Livewire\Admin\Cms\FooterManager;
use App\Livewire\Admin\Cms\HeroManager;
use App\Livewire\Admin\Cms\NavbarManager;
use App\Livewire\Admin\Cms\ProjectsManager;
use App\Livewire\Admin\Cms\ServicesManager;
use App\Livewire\Admin\Cms\SettingsManager;
use App\Livewire\Admin\Cms\SkillsManager;
use App\Livewire\Admin\Cms\TestimonialsManager;
use App\Livewire\Admin\Dashboard\Index as DashboardIndex;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', \App\Livewire\Public\Index::class);

Route::post('/contact-message', [ContactMessageController::class, 'store'])->name('contact.store');

Route::middleware('guest')->group(function () {
	Route::redirect('/login', '/admin/login')->name('login');
	Route::get('/admin/login', Login::class)->name('admin.login');
});

Route::post('/admin/logout', function () {
	Auth::logout();
	request()->session()->invalidate();
	request()->session()->regenerateToken();

	return redirect()->route('admin.login');
})->middleware('auth')->name('admin.logout');

Route::middleware(['auth', 'admin'])
	->prefix('admin')
	->name('admin.')
	->group(function () {
		Route::get('/', DashboardIndex::class)->name('dashboard');
		Route::get('/navbar', NavbarManager::class)->name('navbar');
		Route::get('/hero', HeroManager::class)->name('hero');
		Route::get('/about', AboutManager::class)->name('about');
		Route::get('/skills', SkillsManager::class)->name('skills');
		Route::get('/projects', ProjectsManager::class)->name('projects');
		Route::get('/experiences', ExperiencesManager::class)->name('experiences');
		Route::get('/services', ServicesManager::class)->name('services');
		Route::get('/testimonials', TestimonialsManager::class)->name('testimonials');
		Route::get('/contact', ContactManager::class)->name('contact');
		Route::get('/footer', FooterManager::class)->name('footer');
		Route::get('/settings', SettingsManager::class)->name('settings');
	});