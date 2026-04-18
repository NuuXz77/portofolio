<?php

use App\Http\Controllers\ContactMessageController;
use App\Livewire\Admin\Cms\AboutManager;
use App\Livewire\Admin\Cms\ContactManager;
use App\Livewire\Admin\Cms\EducationsManager;
use App\Livewire\Admin\Cms\ExperiencesManager;
use App\Livewire\Admin\Cms\FooterManager;
use App\Livewire\Admin\Cms\HeroManager;
use App\Livewire\Admin\Cms\Journal\ArticleEditor;
use App\Livewire\Admin\Cms\Journal\ArticlesManager;
use App\Livewire\Admin\Cms\Journal\CategoriesManager;
use App\Livewire\Admin\Cms\NavbarManager;
use App\Livewire\Admin\Cms\PortfolioCategoriesManager;
use App\Livewire\Admin\Cms\ProjectsManager;
use App\Livewire\Admin\Cms\ServicesManager;
use App\Livewire\Admin\Cms\SettingsManager;
use App\Livewire\Admin\Cms\SkillsManager;
use App\Livewire\Admin\Cms\TestimonialsManager;
use App\Livewire\Admin\Dashboard\Index as DashboardIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Public\Journal\Index as PublicJournalIndex;
use App\Livewire\Public\Journal\Show as PublicJournalShow;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
	$supported = (array) config('app.supported_locales', ['id', 'en']);
	$fallback = (string) config('app.fallback_locale', 'id');
	$sessionLocale = trim((string) $request->session()->get('locale', ''));

	if (in_array($sessionLocale, $supported, true)) {
		$targetLocale = $sessionLocale;
	} else {
		$targetLocale = $fallback;

		foreach ((array) $request->getLanguages() as $language) {
			$twoLetter = strtolower(substr((string) $language, 0, 2));

			if (in_array($twoLetter, $supported, true)) {
				$targetLocale = $twoLetter;
				break;
			}
		}
	}

	return redirect()->route('home', ['locale' => $targetLocale]);
});

Route::prefix('{locale}')
	->whereIn('locale', (array) config('app.supported_locales', ['id', 'en']))
	->middleware('setLocale')
	->group(function () {
		Route::get('/', \App\Livewire\Public\Index::class)->name('home');
		Route::get('/journal', PublicJournalIndex::class)->name('journal.index');
		Route::get('/journal/{slug}', PublicJournalShow::class)->name('journal.show');
		Route::post('/contact-message', [ContactMessageController::class, 'store'])->name('contact.store');
	});

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
		Route::get('/portfolio-categories', PortfolioCategoriesManager::class)->name('portfolio.categories');
		Route::get('/projects', ProjectsManager::class)->name('projects');
		Route::get('/educations', EducationsManager::class)->name('educations');
		Route::get('/experiences', ExperiencesManager::class)->name('experiences');
		Route::get('/services', ServicesManager::class)->name('services');
		Route::get('/testimonials', TestimonialsManager::class)->name('testimonials');
		Route::get('/contact', ContactManager::class)->name('contact');
		Route::get('/footer', FooterManager::class)->name('footer');
		Route::get('/journal', ArticlesManager::class)->name('journal.index');
		Route::get('/journal/create', ArticleEditor::class)->name('journal.create');
		Route::get('/journal/{article}/edit', ArticleEditor::class)->name('journal.edit');
		Route::get('/journal/categories', CategoriesManager::class)->name('journal.categories');
		Route::get('/settings', SettingsManager::class)->name('settings');
	});