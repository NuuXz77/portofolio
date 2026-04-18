<?php

namespace App\Livewire\Admin\Cms;

use App\Support\AdminActivity;
use App\Support\LocalizedContent;
use App\Support\PortfolioContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class SettingsManager extends Component
{
    public string $editingLocale = 'id';

    public string $siteTitle = '';

    public string $siteTitleId = '';

    public string $siteTitleEn = '';

    public string $siteDescription = '';

    public string $siteDescriptionId = '';

    public string $siteDescriptionEn = '';

    public string $siteKeywords = '';

    public string $siteKeywordsId = '';

    public string $siteKeywordsEn = '';

    public string $aboutBadgeId = '';

    public string $aboutBadgeEn = '';

    public string $skillsBadgeId = '';

    public string $skillsBadgeEn = '';

    public string $skillsTitleId = '';

    public string $skillsTitleEn = '';

    public string $projectsBadgeId = '';

    public string $projectsBadgeEn = '';

    public string $projectsTitleId = '';

    public string $projectsTitleEn = '';

    public string $featuredBadgeId = '';

    public string $featuredBadgeEn = '';

    public string $featuredTitleId = '';

    public string $featuredTitleEn = '';

    public string $journalBadgeId = '';

    public string $journalBadgeEn = '';

    public string $journalTitleId = '';

    public string $journalTitleEn = '';

    public string $journeyBadgeId = '';

    public string $journeyBadgeEn = '';

    public string $journeyTitleId = '';

    public string $journeyTitleEn = '';

    public string $educationBadgeId = '';

    public string $educationBadgeEn = '';

    public string $educationTitleId = '';

    public string $educationTitleEn = '';

    public string $experienceBadgeId = '';

    public string $experienceBadgeEn = '';

    public string $experienceTitleId = '';

    public string $experienceTitleEn = '';

    public string $servicesBadgeId = '';

    public string $servicesBadgeEn = '';

    public string $servicesTitleId = '';

    public string $servicesTitleEn = '';

    public string $testimonialsBadgeId = '';

    public string $testimonialsBadgeEn = '';

    public string $testimonialsTitleId = '';

    public string $testimonialsTitleEn = '';

    #[Layout('components.layouts.admin')]
    #[Title('Settings')]
    public function mount(): void
    {
        $seo = PortfolioContent::get('seo', []);
        $landing = PortfolioContent::get('landing_sections', []);

        $title = LocalizedContent::split($seo['title'] ?? '');
        $description = LocalizedContent::split($seo['description'] ?? '');
        $keywords = LocalizedContent::split($seo['keywords'] ?? '');

        $this->siteTitleId = $title['id'];
        $this->siteTitleEn = $title['en'];
        $this->siteTitle = $this->siteTitleId;

        $this->siteDescriptionId = $description['id'];
        $this->siteDescriptionEn = $description['en'];
        $this->siteDescription = $this->siteDescriptionId;

        $this->siteKeywordsId = $keywords['id'];
        $this->siteKeywordsEn = $keywords['en'];
        $this->siteKeywords = $this->siteKeywordsId;

        $this->aboutBadgeId = LocalizedContent::split($landing['about_badge'] ?? __('common.about_badge'))['id'];
        $this->aboutBadgeEn = LocalizedContent::split($landing['about_badge'] ?? __('common.about_badge'))['en'];
        $this->skillsBadgeId = LocalizedContent::split($landing['skills_badge'] ?? __('common.skills_badge'))['id'];
        $this->skillsBadgeEn = LocalizedContent::split($landing['skills_badge'] ?? __('common.skills_badge'))['en'];
        $this->skillsTitleId = LocalizedContent::split($landing['skills_title'] ?? __('common.skills_title'))['id'];
        $this->skillsTitleEn = LocalizedContent::split($landing['skills_title'] ?? __('common.skills_title'))['en'];
        $this->projectsBadgeId = LocalizedContent::split($landing['projects_badge'] ?? __('common.projects_badge'))['id'];
        $this->projectsBadgeEn = LocalizedContent::split($landing['projects_badge'] ?? __('common.projects_badge'))['en'];
        $this->projectsTitleId = LocalizedContent::split($landing['projects_title'] ?? __('common.projects_title'))['id'];
        $this->projectsTitleEn = LocalizedContent::split($landing['projects_title'] ?? __('common.projects_title'))['en'];
        $this->featuredBadgeId = LocalizedContent::split($landing['featured_badge'] ?? __('common.featured_badge'))['id'];
        $this->featuredBadgeEn = LocalizedContent::split($landing['featured_badge'] ?? __('common.featured_badge'))['en'];
        $this->featuredTitleId = LocalizedContent::split($landing['featured_title'] ?? __('common.featured_title'))['id'];
        $this->featuredTitleEn = LocalizedContent::split($landing['featured_title'] ?? __('common.featured_title'))['en'];
        $this->journalBadgeId = LocalizedContent::split($landing['journal_badge'] ?? __('common.journal_badge'))['id'];
        $this->journalBadgeEn = LocalizedContent::split($landing['journal_badge'] ?? __('common.journal_badge'))['en'];
        $this->journalTitleId = LocalizedContent::split($landing['journal_title'] ?? __('common.journal_title'))['id'];
        $this->journalTitleEn = LocalizedContent::split($landing['journal_title'] ?? __('common.journal_title'))['en'];
        $this->journeyBadgeId = LocalizedContent::split($landing['journey_badge'] ?? __('common.journey_badge'))['id'];
        $this->journeyBadgeEn = LocalizedContent::split($landing['journey_badge'] ?? __('common.journey_badge'))['en'];
        $this->journeyTitleId = LocalizedContent::split($landing['journey_title'] ?? __('common.journey_title'))['id'];
        $this->journeyTitleEn = LocalizedContent::split($landing['journey_title'] ?? __('common.journey_title'))['en'];
        $this->educationBadgeId = LocalizedContent::split($landing['education_badge'] ?? __('common.education_badge'))['id'];
        $this->educationBadgeEn = LocalizedContent::split($landing['education_badge'] ?? __('common.education_badge'))['en'];
        $this->educationTitleId = LocalizedContent::split($landing['education_title'] ?? __('common.education_title'))['id'];
        $this->educationTitleEn = LocalizedContent::split($landing['education_title'] ?? __('common.education_title'))['en'];
        $this->experienceBadgeId = LocalizedContent::split($landing['experience_badge'] ?? __('common.experience_badge'))['id'];
        $this->experienceBadgeEn = LocalizedContent::split($landing['experience_badge'] ?? __('common.experience_badge'))['en'];
        $this->experienceTitleId = LocalizedContent::split($landing['experience_title'] ?? __('common.experience_title'))['id'];
        $this->experienceTitleEn = LocalizedContent::split($landing['experience_title'] ?? __('common.experience_title'))['en'];
        $this->servicesBadgeId = LocalizedContent::split($landing['services_badge'] ?? __('common.services_badge'))['id'];
        $this->servicesBadgeEn = LocalizedContent::split($landing['services_badge'] ?? __('common.services_badge'))['en'];
        $this->servicesTitleId = LocalizedContent::split($landing['services_title'] ?? __('common.services_title'))['id'];
        $this->servicesTitleEn = LocalizedContent::split($landing['services_title'] ?? __('common.services_title'))['en'];
        $this->testimonialsBadgeId = LocalizedContent::split($landing['testimonials_badge'] ?? __('common.testimonials_badge'))['id'];
        $this->testimonialsBadgeEn = LocalizedContent::split($landing['testimonials_badge'] ?? __('common.testimonials_badge'))['en'];
        $this->testimonialsTitleId = LocalizedContent::split($landing['testimonials_title'] ?? __('common.testimonials_title'))['id'];
        $this->testimonialsTitleEn = LocalizedContent::split($landing['testimonials_title'] ?? __('common.testimonials_title'))['en'];
    }

    public function save(): void
    {
        $this->siteTitle = trim($this->siteTitleId) !== '' ? trim($this->siteTitleId) : trim($this->siteTitleEn);
        $this->siteDescription = trim($this->siteDescriptionId) !== '' ? trim($this->siteDescriptionId) : trim($this->siteDescriptionEn);
        $this->siteKeywords = trim($this->siteKeywordsId) !== '' ? trim($this->siteKeywordsId) : trim($this->siteKeywordsEn);

        $this->validate([
            'siteTitleId' => ['required', 'string', 'max:160'],
            'siteTitleEn' => ['required', 'string', 'max:160'],
            'siteDescriptionId' => ['required', 'string', 'max:320'],
            'siteDescriptionEn' => ['required', 'string', 'max:320'],
            'siteKeywordsId' => ['nullable', 'string', 'max:300'],
            'siteKeywordsEn' => ['nullable', 'string', 'max:300'],
            'aboutBadgeId' => ['required', 'string', 'max:80'],
            'aboutBadgeEn' => ['required', 'string', 'max:80'],
            'skillsBadgeId' => ['required', 'string', 'max:80'],
            'skillsBadgeEn' => ['required', 'string', 'max:80'],
            'skillsTitleId' => ['required', 'string', 'max:200'],
            'skillsTitleEn' => ['required', 'string', 'max:200'],
            'projectsBadgeId' => ['required', 'string', 'max:80'],
            'projectsBadgeEn' => ['required', 'string', 'max:80'],
            'projectsTitleId' => ['required', 'string', 'max:200'],
            'projectsTitleEn' => ['required', 'string', 'max:200'],
            'featuredBadgeId' => ['required', 'string', 'max:80'],
            'featuredBadgeEn' => ['required', 'string', 'max:80'],
            'featuredTitleId' => ['required', 'string', 'max:200'],
            'featuredTitleEn' => ['required', 'string', 'max:200'],
            'journalBadgeId' => ['required', 'string', 'max:80'],
            'journalBadgeEn' => ['required', 'string', 'max:80'],
            'journalTitleId' => ['required', 'string', 'max:200'],
            'journalTitleEn' => ['required', 'string', 'max:200'],
            'journeyBadgeId' => ['required', 'string', 'max:80'],
            'journeyBadgeEn' => ['required', 'string', 'max:80'],
            'journeyTitleId' => ['required', 'string', 'max:200'],
            'journeyTitleEn' => ['required', 'string', 'max:200'],
            'educationBadgeId' => ['required', 'string', 'max:80'],
            'educationBadgeEn' => ['required', 'string', 'max:80'],
            'educationTitleId' => ['required', 'string', 'max:200'],
            'educationTitleEn' => ['required', 'string', 'max:200'],
            'experienceBadgeId' => ['required', 'string', 'max:80'],
            'experienceBadgeEn' => ['required', 'string', 'max:80'],
            'experienceTitleId' => ['required', 'string', 'max:200'],
            'experienceTitleEn' => ['required', 'string', 'max:200'],
            'servicesBadgeId' => ['required', 'string', 'max:80'],
            'servicesBadgeEn' => ['required', 'string', 'max:80'],
            'servicesTitleId' => ['required', 'string', 'max:200'],
            'servicesTitleEn' => ['required', 'string', 'max:200'],
            'testimonialsBadgeId' => ['required', 'string', 'max:80'],
            'testimonialsBadgeEn' => ['required', 'string', 'max:80'],
            'testimonialsTitleId' => ['required', 'string', 'max:200'],
            'testimonialsTitleEn' => ['required', 'string', 'max:200'],
        ]);

        PortfolioContent::set('seo', [
            'title' => LocalizedContent::pack($this->siteTitleId, $this->siteTitleEn),
            'description' => LocalizedContent::pack($this->siteDescriptionId, $this->siteDescriptionEn),
            'keywords' => LocalizedContent::pack($this->siteKeywordsId, $this->siteKeywordsEn),
        ]);

        PortfolioContent::set('landing_sections', [
            'about_badge' => LocalizedContent::pack($this->aboutBadgeId, $this->aboutBadgeEn),
            'skills_badge' => LocalizedContent::pack($this->skillsBadgeId, $this->skillsBadgeEn),
            'skills_title' => LocalizedContent::pack($this->skillsTitleId, $this->skillsTitleEn),
            'projects_badge' => LocalizedContent::pack($this->projectsBadgeId, $this->projectsBadgeEn),
            'projects_title' => LocalizedContent::pack($this->projectsTitleId, $this->projectsTitleEn),
            'featured_badge' => LocalizedContent::pack($this->featuredBadgeId, $this->featuredBadgeEn),
            'featured_title' => LocalizedContent::pack($this->featuredTitleId, $this->featuredTitleEn),
            'journal_badge' => LocalizedContent::pack($this->journalBadgeId, $this->journalBadgeEn),
            'journal_title' => LocalizedContent::pack($this->journalTitleId, $this->journalTitleEn),
            'journey_badge' => LocalizedContent::pack($this->journeyBadgeId, $this->journeyBadgeEn),
            'journey_title' => LocalizedContent::pack($this->journeyTitleId, $this->journeyTitleEn),
            'education_badge' => LocalizedContent::pack($this->educationBadgeId, $this->educationBadgeEn),
            'education_title' => LocalizedContent::pack($this->educationTitleId, $this->educationTitleEn),
            'experience_badge' => LocalizedContent::pack($this->experienceBadgeId, $this->experienceBadgeEn),
            'experience_title' => LocalizedContent::pack($this->experienceTitleId, $this->experienceTitleEn),
            'services_badge' => LocalizedContent::pack($this->servicesBadgeId, $this->servicesBadgeEn),
            'services_title' => LocalizedContent::pack($this->servicesTitleId, $this->servicesTitleEn),
            'testimonials_badge' => LocalizedContent::pack($this->testimonialsBadgeId, $this->testimonialsBadgeEn),
            'testimonials_title' => LocalizedContent::pack($this->testimonialsTitleId, $this->testimonialsTitleEn),
        ]);

        AdminActivity::log('updated', 'settings', 'Updated SEO settings.');
    session()->flash('success', 'Settings saved successfully.');
    $this->dispatch('app-toast', type: 'success', message: 'Settings saved successfully.');
    }

    public function render()
    {
        return view('admin.cms.settings-manager');
    }
}
