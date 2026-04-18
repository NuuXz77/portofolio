@php
    $localeText = static fn (mixed $value, string $default = ''): string => \App\Support\LocalizedContent::resolve($value, default: $default);

    $resolveAsset = function (?string $path, string $fallback = ''): string {
        if (! $path) {
            return $fallback;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return \Illuminate\Support\Facades\Storage::url($path);
    };

    $navItems = $menuItems->isNotEmpty()
        ? $menuItems->map(fn ($item) => [
            'href' => $item->href,
            'label' => $localeText($item->label, __('navigation.home')),
        ])->all()
        : [
            ['href' => '#home', 'label' => __('navigation.home')],
            ['href' => '#about', 'label' => __('navigation.about')],
            ['href' => '#education', 'label' => __('navigation.education')],
            ['href' => '#skills', 'label' => __('navigation.skills')],
            ['href' => '#projects', 'label' => __('navigation.projects')],
            ['href' => '#experience', 'label' => __('navigation.experience')],
            ['href' => route('journal.index'), 'label' => __('navigation.journal')],
            ['href' => '#contact', 'label' => __('navigation.contact')],
        ];

    $journalHref = route('journal.index');

    $navItems = collect($navItems)
        ->reject(function ($item) use ($journalHref) {
            $label = trim((string) ($item['label'] ?? ''));
            $href = trim((string) ($item['href'] ?? ''));

            return strcasecmp($label, 'Journal') === 0
                || strcasecmp($label, __('navigation.journal')) === 0
                || strcasecmp($href, '#journal') === 0
                || strcasecmp($href, $journalHref) === 0;
        })
        ->values()
        ->all();

    $navItems[] = ['href' => $journalHref, 'label' => __('navigation.journal')];

    $logoText = $localeText($navbar['logo_text'] ?? 'Wisnu.dev', 'Wisnu.dev');
    $rawBrandMode = (string) ($navbar['brand_mode'] ?? 'text');
    $rawBrandLogoType = (string) ($navbar['brand_logo_type'] ?? 'image');

    $brandMode = in_array($rawBrandMode, ['text', 'logo', 'combo'], true)
        ? $rawBrandMode
        : 'text';

    $brandLogoType = in_array($rawBrandLogoType, ['image', 'icon'], true)
        ? $rawBrandLogoType
        : 'image';
    $brandLogoImage = isset($navbar['brand_logo_image']) ? trim((string) $navbar['brand_logo_image']) : null;
    $brandLogoIcon = trim((string) ($navbar['brand_logo_icon'] ?? 'sparkles'));
    $ctaText = $localeText($navbar['cta_text'] ?? __('navigation.hire_me'), __('navigation.hire_me'));
    $ctaLink = $navbar['cta_link'] ?? '#contact';

    $heroHeadline = $localeText($hero['headline'] ?? null, 'Fullstack Web Developer & Problem Solver');
    $heroSubheadline = $localeText($hero['subheadline'] ?? null, 'Building scalable web applications with modern technologies.');

    $heroRolesRaw = $hero['roles'] ?? ['Web Developer', 'DevOps Engineer'];

    if (is_array($heroRolesRaw) && (array_key_exists('id', $heroRolesRaw) || array_key_exists('en', $heroRolesRaw))) {
        $heroRolesRaw = $heroRolesRaw[app()->getLocale()] ?? $heroRolesRaw[config('app.fallback_locale', 'id')] ?? [];
    }

    if (is_array($heroRolesRaw)) {
        $heroRoles = collect($heroRolesRaw)
            ->map(fn ($role) => trim((string) $role))
            ->filter()
            ->values()
            ->all();
    } else {
        $heroRoles = collect(preg_split('/[\r\n,|]+/', (string) $heroRolesRaw) ?: [])
            ->map(fn ($role) => trim((string) $role))
            ->filter()
            ->values()
            ->all();
    }

    if ($heroRoles === []) {
        $heroRoles = ['Web Developer', 'DevOps Engineer'];
    }

    $heroPrimaryText = $localeText($hero['primary_cta_text'] ?? null, 'View Projects');
    $heroPrimaryLink = $hero['primary_cta_link'] ?? '#projects';
    $heroSecondaryText = $localeText($hero['secondary_cta_text'] ?? null, 'Download CV');
    $heroSecondaryLink = $hero['secondary_cta_link'] ?? '#';
    $heroSecondaryFileRaw = trim((string) ($hero['secondary_cta_file'] ?? ''));
    $heroSecondaryFileUrl = $heroSecondaryFileRaw !== '' ? $resolveAsset($heroSecondaryFileRaw, '') : '';
    $heroSecondaryIsDownload = $heroSecondaryFileUrl !== '';

    if ($heroSecondaryIsDownload) {
        $heroSecondaryLink = $heroSecondaryFileUrl;
    }

    $heroImage = $resolveAsset($hero['image'] ?? null, 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1200&q=80');

    $aboutTitle = $localeText($about['title'] ?? null, 'About Me');
    $aboutDescription = $localeText($about['description'] ?? null, '<p>Dynamic about description can be managed from admin panel.</p>');
    $aboutStatsRaw = $about['stats'] ?? [];
    $landingSections = \App\Support\PortfolioContent::get('landing_sections', []);

    $aboutBadge = $localeText($landingSections['about_badge'] ?? null, __('common.about_badge'));
    $skillsBadge = $localeText($landingSections['skills_badge'] ?? null, __('common.skills_badge'));
    $skillsTitle = $localeText($landingSections['skills_title'] ?? null, __('common.skills_title'));
    $projectsBadge = $localeText($landingSections['projects_badge'] ?? null, __('common.projects_badge'));
    $projectsTitle = $localeText($landingSections['projects_title'] ?? null, __('common.projects_title'));
    $featuredBadge = $localeText($landingSections['featured_badge'] ?? null, __('common.featured_badge'));
    $featuredTitle = $localeText($landingSections['featured_title'] ?? null, __('common.featured_title'));
    $journalBadge = $localeText($landingSections['journal_badge'] ?? null, __('common.journal_badge'));
    $journalTitle = $localeText($landingSections['journal_title'] ?? null, __('common.journal_title'));
    $journeyBadge = $localeText($landingSections['journey_badge'] ?? null, __('common.journey_badge'));
    $journeyTitle = $localeText($landingSections['journey_title'] ?? null, __('common.journey_title'));
    $educationBadge = $localeText($landingSections['education_badge'] ?? null, __('common.education_badge'));
    $educationTitle = $localeText($landingSections['education_title'] ?? null, __('common.education_title'));
    $experienceBadge = $localeText($landingSections['experience_badge'] ?? null, __('common.experience_badge'));
    $experienceTitle = $localeText($landingSections['experience_title'] ?? null, __('common.experience_title'));
    $servicesBadge = $localeText($landingSections['services_badge'] ?? null, __('common.services_badge'));
    $servicesTitle = $localeText($landingSections['services_title'] ?? null, __('common.services_title'));
    $testimonialsBadge = $localeText($landingSections['testimonials_badge'] ?? null, __('common.testimonials_badge'));
    $testimonialsTitle = $localeText($landingSections['testimonials_title'] ?? null, __('common.testimonials_title'));

    if (is_array($aboutStatsRaw) && (array_key_exists('id', $aboutStatsRaw) || array_key_exists('en', $aboutStatsRaw))) {
        $aboutStatsRaw = $aboutStatsRaw[app()->getLocale()] ?? $aboutStatsRaw[config('app.fallback_locale', 'id')] ?? [];
    }

    $aboutStats = collect($aboutStatsRaw)
        ->map(function ($stat) use ($localeText): ?array {
            if (! is_array($stat)) {
                return null;
            }

            return [
                'label' => $localeText($stat['label'] ?? '', ''),
                'value' => $localeText($stat['value'] ?? '', ''),
            ];
        })
        ->filter(static fn (?array $stat): bool => is_array($stat) && (($stat['label'] ?? '') !== '' || ($stat['value'] ?? '') !== ''))
        ->values()
        ->all();
    $aboutImagePath = trim((string) ($about['profile_image'] ?? $about['image'] ?? ''));
    $aboutImage = $aboutImagePath !== '' ? $resolveAsset($aboutImagePath, '') : '';

    $defaultTechnologyLogos = collect(config('technology-icons', []))
        ->map(function (array $icon): array {
            return [
                'name' => (string) ($icon['name'] ?? 'Tech'),
                'slug' => strtolower((string) ($icon['slug'] ?? '')),
                'color' => strtoupper((string) ($icon['color'] ?? '9CA3AF')),
            ];
        })
        ->filter(static fn (array $icon): bool => $icon['slug'] !== '')
        ->unique('slug')
        ->values();

    $technologyLogos = collect($skillsByCategory)
        ->flatten(1)
        ->map(function ($skill): ?array {
            $rawIcon = trim((string) ($skill->icon ?? ''));

            if (! str_starts_with($rawIcon, 'si:')) {
                return null;
            }

            $parts = explode(':', $rawIcon, 3);
            $slug = strtolower(trim((string) ($parts[1] ?? '')));
            $color = strtoupper(trim((string) ($parts[2] ?? '9CA3AF')));

            if ($slug === '') {
                return null;
            }

            return [
                'name' => trim((string) ($skill->name ?? $slug)),
                'slug' => $slug,
                'color' => $color !== '' ? $color : '9CA3AF',
            ];
        })
        ->filter()
        ->unique('slug')
        ->values();

    if ($technologyLogos->count() < 10) {
        $technologyLogos = $technologyLogos
            ->concat($defaultTechnologyLogos)
            ->unique('slug')
            ->values();
    }

    $technologyLogos = $technologyLogos->take(18)->values();

    $technologyLogosLoop = collect();

    if ($technologyLogos->isNotEmpty()) {
        while ($technologyLogosLoop->count() < 14) {
            $technologyLogosLoop = $technologyLogosLoop->concat($technologyLogos);
        }

        $technologyLogosLoop = $technologyLogosLoop->take(14)->values();
    }

    $normalizeSkillKey = static fn (string $value): string => strtolower(preg_replace('/[^a-z0-9]+/', '', $value));

    $categoryIconMap = [
        'frontend' => 'monitor',
        'backend' => 'server-cog',
        'database' => 'database',
        'devops' => 'cloud-cog',
        'mobile' => 'smartphone',
        'tools' => 'wrench',
    ];

    $resolveCategoryIcon = static function (string $category) use ($categoryIconMap, $normalizeSkillKey): string {
        $normalized = $normalizeSkillKey($category);

        foreach ($categoryIconMap as $keyword => $icon) {
            if (str_contains($normalized, $keyword)) {
                return $icon;
            }
        }

        return 'layers-3';
    };

    $contactLinks = [
        ['label' => __('common.email'), 'value' => $contactInfo['email'] ?? '-', 'href' => isset($contactInfo['email']) ? 'mailto:'.$contactInfo['email'] : '#', 'icon' => 'mail'],
        ['label' => 'WhatsApp', 'value' => $contactInfo['whatsapp'] ?? '-', 'href' => isset($contactInfo['whatsapp']) ? 'https://wa.me/'.preg_replace('/\D+/', '', $contactInfo['whatsapp']) : '#', 'icon' => 'message-circle'],
        ['label' => 'LinkedIn', 'value' => $contactInfo['linkedin'] ?? '-', 'href' => $contactInfo['linkedin'] ?? '#', 'icon' => 'brand-linkedin'],
        ['label' => 'GitHub', 'value' => $contactInfo['github'] ?? '-', 'href' => $contactInfo['github'] ?? '#', 'icon' => 'brand-github'],
    ];

    $contactBadge = $localeText($contactInfo['contact_badge'] ?? null, __('common.contact'));
    $contactTitle = $localeText($contactInfo['contact_title'] ?? null, "Let's build something meaningful");
    $contactDescription = $localeText($contactInfo['contact_description'] ?? null, 'Open for freelance and long-term collaboration. Reach out anytime and I will get back to you quickly.');
    $contactFormTitle = $localeText($contactInfo['form_title'] ?? null, __('common.send_message'));
    $contactSubmitText = $localeText($contactInfo['submit_text'] ?? null, __('common.send_message'));

    $footerTagline = $localeText($footer['tagline'] ?? null, __('common.open_for_collaboration'));
    $footerCta = $localeText($footer['cta'] ?? null, __('common.open_for_collaboration'));
    $footerCopyright = $localeText($footer['copyright'] ?? null, __('common.footer_crafted'));
    $resolveFooterSocialIcon = static function (array $social): string {
        $icon = strtolower(trim((string) ($social['icon'] ?? '')));
        $icon = str_replace([' ', '_'], '-', $icon);

        if (in_array($icon, ['brand-linkedin', 'linkedin'], true)) {
            return 'brand-linkedin';
        }

        if (in_array($icon, ['brand-x', 'x', 'twitter'], true)) {
            return 'brand-x';
        }

        if (in_array($icon, ['brand-github', 'github'], true) || str_contains($icon, 'github')) {
            return 'brand-github';
        }

        if (in_array($icon, ['wa', 'whatsapp'], true)) {
            return 'message-circle';
        }

        if (in_array($icon, ['email'], true)) {
            return 'mail';
        }

        if ($icon !== '') {
            return $icon;
        }

        $labelAndLink = strtolower(trim((string) ($social['label'] ?? '').' '.(string) ($social['link'] ?? '')));

        if (str_contains($labelAndLink, 'linkedin')) {
            return 'brand-linkedin';
        }

        if (str_contains($labelAndLink, 'twitter') || str_contains($labelAndLink, 'x.com')) {
            return 'brand-x';
        }

        if (str_contains($labelAndLink, 'github')) {
            return 'brand-github';
        }

        if (str_contains($labelAndLink, 'instagram')) {
            return 'instagram';
        }

        if (str_contains($labelAndLink, 'youtube')) {
            return 'youtube';
        }

        if (str_contains($labelAndLink, 'whatsapp') || str_contains($labelAndLink, 'wa.me')) {
            return 'message-circle';
        }

        if (str_contains($labelAndLink, 'mail') || str_contains($labelAndLink, '@')) {
            return 'mail';
        }

        return 'link-2';
    };

    $footerSocials = collect($footer['socials'] ?? [])
        ->map(function ($item) use ($resolveFooterSocialIcon): array {
            return [
                'label' => \App\Support\LocalizedContent::resolve($item['label'] ?? '', default: ''),
                'link' => trim((string) ($item['link'] ?? '#')),
                'icon' => $resolveFooterSocialIcon((array) $item),
            ];
        })
        ->filter(static fn (array $item): bool => $item['label'] !== '' && $item['link'] !== '')
        ->values()
        ->all();
@endphp

    <div class="relative z-10 overflow-x-hidden">

        <x-partials.public-navbar
            :logoText="$logoText"
            :brandMode="$brandMode"
            :brandLogoType="$brandLogoType"
            :brandLogoImage="$brandLogoImage"
            :brandLogoIcon="$brandLogoIcon"
            brandHref="#home"
            :navItems="$navItems"
            :ctaText="$ctaText"
            :ctaLink="$ctaLink"
        />

        <main>
        <section id="home" class="relative px-4 pb-8 pt-4 sm:px-8 sm:pb-5 sm:pt-10 lg:pt-14">
            <div class="relative z-10 mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-2">
                <div>
                    <div data-aos="fade-right" data-aos-duration="900">
                        <span class="badge badge-outline badge-info mb-5 rounded-full px-4 py-3 text-xs tracking-wide">Fullstack Engineer • Open To Collaboration</span>
                        <h1 class="text-4xl font-semibold leading-tight text-base-content sm:text-5xl lg:text-6xl">{{ $heroHeadline }}</h1>
                        <p class="mt-5 max-w-2xl text-base leading-relaxed text-base-content/75 sm:text-lg">{{ $heroSubheadline }}</p>

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <span class="text-sm text-base-content/65">Currently working as</span>
                            <span id="typing-role" data-roles='@json($heroRoles)' class="rounded-full border border-info/40 bg-info/10 px-4 py-1.5 text-sm font-medium text-info"></span>
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ $heroPrimaryLink }}" class="btn btn-info rounded-xl px-6">{{ $heroPrimaryText }}</a>
                            <a href="{{ $heroSecondaryLink }}" @if ($heroSecondaryIsDownload) download @endif class="btn btn-outline rounded-xl px-6">{{ $heroSecondaryText }}</a>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div data-aos="fade-left" data-aos-duration="900" data-aos-delay="130">
                        <div class="hero-premium-frame rounded-4xl">
                            <div class="hero-premium-card portfolio-glass relative m-px overflow-hidden border border-white/10 p-4 shadow-2xl">
                                <img loading="eager" src="{{ $heroImage }}" alt="Developer workspace" class="h-96 w-full rounded-3xl object-cover sm:h-120">
                                <div class="absolute inset-0 rounded-3xl bg-linear-to-tr from-base-300/40 via-transparent to-info/20"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="px-4 py-14 sm:px-8 sm:py-20">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[1fr,1.2fr] lg:items-center">
                <div class="min-w-0">
                    <div data-aos="fade-up" data-aos-duration="800" class="min-w-0">
                        <div class="portfolio-glass relative overflow-hidden rounded-4xl border border-white/10 p-4 shadow-2xl">
                            @if ($aboutImage !== '')
                                <img loading="lazy" src="{{ $aboutImage }}" alt="Professional portrait" class="h-108 w-full rounded-3xl object-cover">
                            @else
                                <div class="flex h-108 w-full items-center justify-center rounded-3xl border border-dashed border-white/20 bg-base-100/45 text-center">
                                    <p class="px-6 text-sm text-base-content/65">Set About profile image from CMS.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="90" class="min-w-0">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">{{ $aboutBadge }}</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">{{ $aboutTitle }}</h2>
                    <div class="prose prose-invert mt-6 max-w-none text-base-content/75">{!! $aboutDescription !!}</div>

                    <div class="relative z-10 mt-8 grid gap-4 sm:grid-cols-3">
                        @foreach ($aboutStats as $stat)
                            <article class="portfolio-glass rounded-2xl border border-white/10 p-4 text-center">
                                <p class="text-2xl font-semibold text-base-content">{{ $stat['value'] ?? '-' }}</p>
                                <p class="mt-1 text-sm text-base-content/70">{{ $stat['label'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>

                    @if ($technologyLogosLoop->isNotEmpty())
                        <div class="relative z-0 mx-auto mt-8 w-full min-w-0 max-w-full space-y-3 px-1 text-center sm:max-w-2xl sm:px-0">
                            <p class="text-xs uppercase tracking-[0.2em] text-base-content/55">Technologies I use</p>

                            <div class="relative">
                                <span aria-hidden="true" class="pointer-events-none absolute inset-y-0 inset-x-4 rounded-full bg-info/15 opacity-20 blur-xl sm:inset-x-12"></span>

                                <div class="tech-logo-marquee isolate w-full min-w-0 max-w-full overflow-hidden [--tech-fade-size:2rem] [--tech-gap:0.5rem] [--tech-icon-size:1.05rem] [--tech-item-size:2.25rem] [--tech-pad-x:0.6rem] [--tech-pad-y:0.5rem] [--tech-speed:32s] sm:[--tech-fade-size:3.6rem] sm:[--tech-gap:0.66rem] sm:[--tech-icon-size:1.28rem] sm:[--tech-item-size:2.7rem] sm:[--tech-pad-x:1rem] sm:[--tech-pad-y:0.62rem] sm:[--tech-speed:28s]">
                                    <div class="tech-logo-track">
                                        @for ($i = 0; $i < 2; $i++)
                                            @foreach ($technologyLogosLoop as $logo)
                                                <span class="tech-logo-item group" style="--tech-glow: #{{ $logo['color'] }};" data-tech="{{ $logo['name'] }}" title="{{ $logo['name'] }}">
                                                    <span aria-hidden="true" class="tech-logo-item-glow"></span>
                                                    <img
                                                        src="{{ $simpleIconsCdn }}/{{ $logo['slug'] }}/{{ $logo['color'] }}"
                                                        alt="{{ $logo['name'] }}"
                                                        class="tech-logo-icon"
                                                        loading="lazy"
                                                    >
                                                </span>
                                            @endforeach
                                        @endfor
                                    </div>

                                    <span aria-hidden="true" class="tech-logo-fade tech-logo-fade-left"></span>
                                    <span aria-hidden="true" class="tech-logo-fade tech-logo-fade-right"></span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section id="skills" class="px-4 py-14 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 text-center" data-aos="fade-up" data-aos-duration="800">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">{{ $skillsBadge }}</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">{{ $skillsTitle }}</h2>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:gap-8">
                    @foreach ($skillsByCategory as $category => $groupSkills)
                        <article data-aos="fade-up" data-aos-duration="800" class="skill-category-card group isolate overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-6 shadow-lg backdrop-blur-lg transition-all duration-500 ease-out hover:-translate-y-1.5 hover:scale-[1.02] hover:border-white/20 hover:bg-white/10 hover:shadow-2xl sm:p-7">
                            <span aria-hidden="true" class="skill-card-spotlight"></span>
                            <div class="mb-6 flex items-center gap-3">
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-info/35 bg-info/15 text-info">
                                    <i data-lucide="{{ $resolveCategoryIcon((string) $category) }}" class="h-5 w-5"></i>
                                </span>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.16em] text-base-content/50">Category</p>
                                    <h3 class="text-xl font-semibold text-base-content">{{ $category }}</h3>
                                </div>
                            </div>

                            <div class="space-y-4">
                                @foreach ($groupSkills as $skill)
                                    @php
                                        $skillLevel = max(0, min(100, (int) $skill->level));
                                        $skillIcon = trim((string) ($skill->icon ?? ''));
                                        $skillIconParts = str_starts_with($skillIcon, 'si:') ? explode(':', $skillIcon, 3) : [];
                                        $skillBrandSlug = $skillIconParts[1] ?? '';
                                        $skillBrandColor = strtoupper($skillIconParts[2] ?? '000000');
                                    @endphp
                                    <div class="skill-item rounded-xl border border-white/10 bg-base-100/35 px-4 py-3 transition duration-300 hover:translate-x-1 hover:border-info/35 hover:bg-base-100/55" title="{{ $skill->name }} - {{ $skillLevel }}%">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex min-w-0 items-center gap-3">
                                                @if ($skillIcon !== '')
                                                    <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-info/30 bg-info/12 text-info">
                                                        @if ($skillBrandSlug !== '')
                                                            <img
                                                                src="{{ $simpleIconsCdn }}/{{ $skillBrandSlug }}/{{ $skillBrandColor }}"
                                                                alt="{{ $skillBrandSlug }}"
                                                                class="h-3.5 w-3.5"
                                                                loading="lazy"
                                                            >
                                                        @else
                                                            <i data-lucide="{{ $skillIcon }}" class="h-3.5 w-3.5"></i>
                                                        @endif
                                                    </span>
                                                @else
                                                    <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-white/15 bg-white/5 text-base-content/70">
                                                        <i data-lucide="code-2" class="h-3.5 w-3.5"></i>
                                                    </span>
                                                @endif
                                                <span class="truncate text-sm font-medium text-base-content/85 sm:text-[0.95rem]">{{ $skill->name }}</span>
                                            </div>
                                            <span class="text-sm font-semibold text-info">{{ $skillLevel }}%</span>
                                        </div>

                                        <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-base-300/45">
                                            <span
                                                class="skill-progress-fill block h-full rounded-full bg-linear-to-r from-sky-400 via-cyan-300 to-blue-500 shadow-[0_0_12px_rgba(56,189,248,0.58)] transition-[width] duration-1000 ease-out"
                                                data-skill-progress
                                                data-skill-level="{{ $skillLevel }}"
                                                style="width: 0%"
                                            ></span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="projects" class="px-4 py-14 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 text-center" data-aos="fade-up" data-aos-duration="800">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">{{ $projectsBadge }}</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">{{ $projectsTitle }}</h2>
                </div>

                <div class="mb-8 flex flex-wrap justify-center gap-2" data-aos="fade-up" data-aos-duration="800" data-aos-delay="80">
                    <button class="project-filter is-active btn btn-sm rounded-xl border-white/15 bg-base-100/70 text-base-content" data-filter="all">All</button>
                    @foreach ($projectCategoryFilters as $category)
                        <button class="project-filter btn btn-sm rounded-xl border-white/15 bg-base-100/70 text-base-content" data-filter="{{ $category['value'] }}">{{ $category['label'] }}</button>
                    @endforeach
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3" id="project-grid">
                    @foreach ($projects as $project)
                        <article data-aos="fade-up" data-aos-duration="800" data-category="{{ $project->portfolioCategory?->slug ?? $project->category }}" class="project-card skill-category-card card overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-xl backdrop-blur-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-info/20">
                            <span aria-hidden="true" class="skill-card-spotlight"></span>
                            <figure class="relative h-48 overflow-hidden">
                                <img loading="lazy" src="{{ $resolveAsset($project->image_path, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80') }}" alt="{{ $project->title }} preview" class="h-full w-full object-cover transition duration-700 hover:scale-110">
                                <div class="absolute inset-0 bg-linear-to-t from-base-300/70 via-transparent to-transparent"></div>
                            </figure>
                            <div class="card-body">
                                <h3 class="card-title text-base-content">{{ $project->title }}</h3>
                                <p class="text-sm leading-relaxed text-base-content/70">{{ $project->description }}</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach (($project->tech_stack ?? []) as $tech)
                                        <span class="badge badge-outline badge-info rounded-full">{{ $tech }}</span>
                                    @endforeach
                                </div>
                                <div class="card-actions mt-4 justify-end">
                                    <a href="{{ $project->demo_link ?: '#' }}" class="btn btn-sm btn-info rounded-xl text-base-content">Live Demo</a>
                                    <a href="{{ $project->github_link ?: '#' }}" class="btn btn-sm btn-outline rounded-xl">GitHub</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="px-4 py-14 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-7xl" data-aos="fade-up" data-aos-duration="800">
                <div class="mb-8 text-center">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">{{ $featuredBadge }}</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">{{ $featuredTitle }}</h2>
                </div>

                <div class="rounded-4xl border border-white/10 bg-base-100/70 p-3 shadow-2xl backdrop-blur-lg sm:p-4 lg:p-5">
                    <div class="swiper featured-swiper overflow-hidden rounded-3xl">
                        <div class="swiper-wrapper">
                            @forelse ($featuredProjects as $featured)
                                <article class="swiper-slide h-auto">
                                    <div class="grid h-full overflow-hidden rounded-3xl border border-white/10 bg-base-100/65 shadow-xl md:grid-cols-[1.05fr,1fr]">
                                        <div class="relative">
                                            <img loading="lazy" src="{{ $resolveAsset($featured->image_path, 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1400&q=80') }}" alt="{{ $featured->title }} showcase" class="h-40 w-full object-cover sm:h-44 md:h-48">
                                            <div class="absolute inset-0 bg-linear-to-t from-base-300/65 via-transparent to-transparent"></div>
                                            <span class="absolute left-4 top-4 badge badge-info badge-outline rounded-full">Featured</span>
                                        </div>

                                        <div class="flex h-full flex-col justify-between p-4 sm:p-5">
                                            <div>
                                                <h3 class="text-xl font-semibold text-base-content sm:text-2xl">{{ $featured->title }}</h3>
                                                <p class="featured-desc mt-2 text-sm leading-relaxed text-base-content/75">{{ $featured->description }}</p>
                                            </div>

                                            <div class="mt-4 flex flex-wrap gap-2">
                                                @if ($featured->demo_link)
                                                    <a href="{{ $featured->demo_link }}" target="_blank" rel="noreferrer" class="btn btn-info btn-sm rounded-xl">Live Demo</a>
                                                @endif
                                                @if ($featured->github_link)
                                                    <a href="{{ $featured->github_link }}" target="_blank" rel="noreferrer" class="btn btn-outline btn-sm rounded-xl">GitHub</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <article class="swiper-slide">
                                    <div class="flex min-h-44 items-center justify-center rounded-3xl border border-dashed border-white/20 bg-base-200/45 p-6 text-center">
                                        <div>
                                            <p class="text-lg font-semibold text-base-content">No featured projects yet</p>
                                            <p class="mt-1 text-sm text-base-content/65">Mark a project as featured from admin CMS to show it here.</p>
                                        </div>
                                    </div>
                                </article>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-3">
                        <button class="featured-prev btn btn-sm btn-circle border border-white/15 bg-base-100/70 hover:bg-base-100" aria-label="Previous featured project">
                            <i data-lucide="chevron-left" class="h-4 w-4"></i>
                        </button>
                        <div class="swiper-pagination featured-pagination relative! bottom-0! left-0! w-auto"></div>
                        <button class="featured-next btn btn-sm btn-circle border border-white/15 bg-base-100/70 hover:bg-base-100" aria-label="Next featured project">
                            <i data-lucide="chevron-right" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="px-4 py-14 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 flex flex-wrap items-center justify-between gap-3" data-aos="fade-up" data-aos-duration="800">
                    <div>
                        <p class="text-sm uppercase tracking-[0.24em] text-info">{{ $journalBadge }}</p>
                        <h2 class="mt-2 text-3xl font-semibold text-base-content sm:text-4xl">{{ $journalTitle }}</h2>
                    </div>
                    <a href="{{ route('journal.index') }}" class="btn btn-outline rounded-xl">View All</a>
                </div>

                <div class="grid gap-5 md:grid-cols-3">
                    @forelse ($latestArticles as $article)
                        <article data-aos="fade-up" data-aos-duration="800" class="skill-category-card overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-xl backdrop-blur-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-info/20">
                            <span aria-hidden="true" class="skill-card-spotlight"></span>
                            <a href="{{ route('journal.show', $article->slug) }}">
                                <img
                                    loading="lazy"
                                    src="{{ $resolveAsset($article->thumbnail_path, 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80') }}"
                                    alt="{{ $article->title }}"
                                    class="h-40 w-full object-cover"
                                >
                            </a>

                            <div class="p-5">
                                <div class="flex items-center gap-2 text-xs text-base-content/60">
                                    <span class="badge badge-outline badge-info rounded-full">{{ $article->category?->name ?? 'General' }}</span>
                                    <span>{{ optional($article->published_at ?: $article->created_at)->format('d M Y') }}</span>
                                </div>

                                <h3 class="mt-3 line-clamp-2 text-xl font-semibold text-base-content">
                                    <a href="{{ route('journal.show', $article->slug) }}" class="hover:text-info">{{ $article->title }}</a>
                                </h3>

                                <p class="mt-2 line-clamp-2 text-sm text-base-content/70">{{ $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 100) }}</p>
                            </div>
                        </article>
                    @empty
                        <article class="md:col-span-3 rounded-2xl border border-dashed border-white/20 bg-base-100/45 p-8 text-center">
                            <p class="text-lg font-semibold text-base-content">No journal posts yet</p>
                            <p class="mt-1 text-sm text-base-content/65">Publish articles from admin panel to show them here.</p>
                        </article>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="px-4 py-14 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 text-center" data-aos="fade-up" data-aos-duration="800">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">{{ $journeyBadge }}</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">{{ $journeyTitle }}</h2>
                </div>

                <div class="grid gap-8 lg:grid-cols-2 lg:gap-10">
                    <div id="education" data-aos="fade-up" data-aos-duration="800" class="space-y-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-info">{{ $educationBadge }}</p>
                            <h3 class="mt-2 text-2xl font-semibold text-base-content">{{ $educationTitle }}</h3>
                        </div>

                        <div class="relative">
                            <span aria-hidden="true" class="absolute inset-y-2 left-2 hidden w-px bg-white/15 sm:block"></span>

                            @forelse ($educations as $education)
                                @php
                                    $yearRange = $education->end_year
                                        ? $education->start_year.' - '.$education->end_year
                                        : $education->start_year.' - Present';
                                @endphp
                                <article data-aos="fade-up" data-aos-duration="800" class="group relative mb-6 last:mb-0 sm:pl-14">
                                    <span aria-hidden="true" class="absolute left-2 top-10 hidden h-4 w-4 -translate-x-1/2 rounded-full border border-info/50 bg-base-100 shadow-[0_0_0_6px_rgba(30,41,59,0.6)] sm:inline-flex"></span>

                                    <div class="skill-category-card isolate overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-xl backdrop-blur-lg transition-all duration-300 group-hover:-translate-y-1 group-hover:scale-[1.02] group-hover:shadow-info/20 sm:p-6">
                                        <span aria-hidden="true" class="skill-card-spotlight"></span>
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div class="inline-flex items-center gap-3">
                                                @if ($education->logo)
                                                    <img
                                                        src="{{ $resolveAsset($education->logo, '') }}"
                                                        alt="{{ $education->institution_name }} logo"
                                                        class="h-12 w-12 rounded-xl border border-white/10 object-cover"
                                                        loading="lazy"
                                                    >
                                                @else
                                                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl border border-white/10 bg-base-100/60 text-info">
                                                        <i data-lucide="graduation-cap" class="h-5 w-5"></i>
                                                    </span>
                                                @endif

                                                <div>
                                                    <p class="text-sm font-medium text-info">{{ $yearRange }}</p>
                                                    <h3 class="mt-1 text-xl font-semibold text-base-content">{{ $education->institution_name }}</h3>
                                                    <p class="mt-1 text-sm text-base-content/70">{{ $education->major }} • {{ $education->degree }}</p>
                                                </div>
                                            </div>

                                            @if (! $education->end_year)
                                                <span class="badge badge-info badge-outline rounded-full">Currently Studying</span>
                                            @endif
                                        </div>

                                        @if ($education->description)
                                            <p class="mt-4 leading-relaxed text-base-content/75">{{ $education->description }}</p>
                                        @endif
                                    </div>
                                </article>
                            @empty
                                <article class="rounded-2xl border border-dashed border-white/20 bg-base-100/45 p-8 text-center">
                                    <p class="text-lg font-semibold text-base-content">No education records yet</p>
                                    <p class="mt-1 text-sm text-base-content/65">Tambahkan riwayat pendidikan dari admin CMS agar tampil di sini.</p>
                                </article>
                            @endforelse
                        </div>
                    </div>

                    <div id="experience" data-aos="fade-up" data-aos-duration="800" data-aos-delay="80" class="space-y-6">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-info">{{ $experienceBadge }}</p>
                            <h3 class="mt-2 text-2xl font-semibold text-base-content">{{ $experienceTitle }}</h3>
                        </div>

                        <div class="relative">
                            <span aria-hidden="true" class="absolute inset-y-2 left-2 hidden w-px bg-white/15 sm:block"></span>

                            @forelse ($experiences as $experience)
                                <article data-aos="fade-up" data-aos-duration="800" class="group relative mb-6 last:mb-0 sm:pl-14">
                                    <span aria-hidden="true" class="absolute left-2 top-7 hidden h-4 w-4 -translate-x-1/2 rounded-full border border-info/50 bg-base-100 shadow-[0_0_0_6px_rgba(30,41,59,0.6)] sm:inline-flex"></span>

                                    <div class="skill-category-card isolate overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 shadow-xl backdrop-blur-lg transition-all duration-300 group-hover:-translate-y-1 group-hover:scale-[1.02] group-hover:shadow-info/20 sm:p-6">
                                        <span aria-hidden="true" class="skill-card-spotlight"></span>
                                        <p class="text-sm font-medium text-info">{{ $experience->year }}</p>
                                        <h3 class="mt-2 text-xl font-semibold text-base-content">{{ $experience->role }}</h3>
                                        <p class="mt-2 text-sm text-base-content/60">{{ $experience->company }}</p>
                                        <p class="mt-3 leading-relaxed text-base-content/75">{{ $experience->description }}</p>
                                    </div>
                                </article>
                            @empty
                                <article class="rounded-2xl border border-dashed border-white/20 bg-base-100/45 p-8 text-center">
                                    <p class="text-lg font-semibold text-base-content">No experience records yet</p>
                                    <p class="mt-1 text-sm text-base-content/65">Tambahkan pengalaman dari admin CMS agar tampil di sini.</p>
                                </article>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="services" class="px-4 py-14 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 text-center" data-aos="fade-up" data-aos-duration="800">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">{{ $servicesBadge }}</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">{{ $servicesTitle }}</h2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($services as $service)
                        <article data-aos="fade-up" data-aos-duration="800" class="skill-category-card card rounded-3xl border border-white/10 bg-white/5 shadow-xl backdrop-blur-lg transition-all duration-300 hover:-translate-y-2 hover:shadow-info/20">
                            <span aria-hidden="true" class="skill-card-spotlight"></span>
                            <div class="card-body">
                                <span class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-info/20 text-info">
                                    <i data-lucide="{{ $service->icon ?: 'star' }}" class="h-6 w-6"></i>
                                </span>
                                <h3 class="text-xl font-semibold text-base-content">{{ $service->title }}</h3>
                                <p class="text-sm leading-relaxed text-base-content/70">{{ $service->description }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="px-4 py-14 sm:px-8 sm:py-20">
            <div class="mx-auto max-w-7xl" data-aos="fade-up" data-aos-duration="800">
                <div class="mb-8 text-center">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">{{ $testimonialsBadge }}</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">{{ $testimonialsTitle }}</h2>
                </div>

                <div class="rounded-4xl border border-white/10 bg-base-100/70 p-3 shadow-2xl backdrop-blur-lg sm:p-4 lg:p-5">
                    <div class="swiper testimonial-swiper overflow-hidden rounded-3xl">
                        <div class="swiper-wrapper">
                            @forelse ($testimonials as $testimonial)
                                <article class="swiper-slide h-auto">
                                    <div class="flex h-full flex-col justify-between rounded-3xl border border-white/10 bg-base-200/55 p-5 sm:p-6">
                                        <div>
                                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-info/15 text-info">
                                                <i data-lucide="quote" class="h-5 w-5"></i>
                                            </span>
                                            <p class="mt-4 text-base leading-relaxed text-base-content/80 sm:text-[1.02rem]">{{ $testimonial->message }}</p>
                                        </div>

                                        <div class="mt-6 border-t border-white/10 pt-4">
                                            <div class="flex items-center gap-3">
                                                @if ($testimonial->avatar_path)
                                                    <img src="{{ $resolveAsset($testimonial->avatar_path) }}" alt="{{ $testimonial->name }}" class="h-11 w-11 rounded-full object-cover">
                                                @else
                                                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-info/15 text-sm font-semibold text-info">
                                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($testimonial->name, 0, 1)) }}
                                                    </span>
                                                @endif
                                                <div>
                                                    <p class="text-sm font-semibold text-base-content sm:text-base">{{ $testimonial->name }}</p>
                                                    <p class="text-xs text-base-content/60 sm:text-sm">{{ $testimonial->role }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <article class="swiper-slide">
                                    <div class="flex min-h-44 items-center justify-center rounded-3xl border border-dashed border-white/20 bg-base-200/45 p-6 text-center">
                                        <div>
                                            <p class="text-lg font-semibold text-base-content">Belum ada testimoni</p>
                                            <p class="mt-1 text-sm text-base-content/65">Tambahkan testimoni dari admin CMS agar tampil di sini.</p>
                                        </div>
                                    </div>
                                </article>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-3">
                        <button class="testimonial-prev btn btn-sm btn-circle border border-white/15 bg-base-100/70 hover:bg-base-100" aria-label="Previous testimonial">
                            <i data-lucide="chevron-left" class="h-4 w-4"></i>
                        </button>
                        <div class="swiper-pagination testimonial-pagination relative! bottom-0! left-0! w-auto"></div>
                        <button class="testimonial-next btn btn-sm btn-circle border border-white/15 bg-base-100/70 hover:bg-base-100" aria-label="Next testimonial">
                            <i data-lucide="chevron-right" class="h-4 w-4"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="px-4 pb-16 pt-14 sm:px-8 sm:pb-24 sm:pt-20">
            <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-2">
                <article data-aos="fade-up" data-aos-duration="800" class="portfolio-glass rounded-4xl border border-white/10 p-6 shadow-2xl sm:p-8">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">{{ $contactBadge }}</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content">{{ $contactTitle }}</h2>
                    <p class="mt-4 text-base leading-relaxed text-base-content/75">{{ $contactDescription }}</p>

                    <div class="mt-8 space-y-4">
                        @foreach ($contactLinks as $contact)
                            <a href="{{ $contact['href'] }}" target="_blank" rel="noreferrer" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-base-100/50 px-4 py-3 transition hover:border-info/40 hover:bg-info/10">
                                <div class="flex items-center gap-3">
                                    @php($contactIcon = trim((string) ($contact['icon'] ?? 'link-2')))
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-info/20 text-info">
                                        @if ($contactIcon === 'brand-linkedin')
                                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 fill-current" role="img">
                                                <path d="M20.45 20.45h-3.56v-5.58c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.15 1.45-2.15 2.94v5.68H9.33V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 1 1 0-4.14 2.07 2.07 0 0 1 0 4.14zM7.12 20.45H3.55V9h3.57v11.45zM22.23 0H1.77A1.76 1.76 0 0 0 0 1.74v20.52C0 23.23.79 24 1.77 24h20.46c.98 0 1.77-.77 1.77-1.74V1.74A1.76 1.76 0 0 0 22.23 0z" />
                                            </svg>
                                        @elseif ($contactIcon === 'brand-github' || $contactIcon === 'github')
                                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 fill-current" role="img">
                                                <path d="M12 .5a12 12 0 0 0-3.79 23.39c.6.1.82-.26.82-.58v-2.04c-3.34.73-4.04-1.61-4.04-1.61-.55-1.39-1.33-1.77-1.33-1.77-1.09-.74.08-.72.08-.72 1.2.09 1.84 1.23 1.84 1.23 1.07 1.84 2.8 1.31 3.49 1 .11-.78.42-1.31.76-1.61-2.66-.3-5.46-1.33-5.46-5.92 0-1.31.47-2.38 1.23-3.22-.12-.3-.53-1.52.12-3.17 0 0 1-.32 3.3 1.23a11.5 11.5 0 0 1 6 0c2.29-1.55 3.29-1.23 3.29-1.23.65 1.65.24 2.87.12 3.17.77.84 1.23 1.91 1.23 3.22 0 4.6-2.8 5.61-5.47 5.91.43.37.82 1.1.82 2.22v3.29c0 .32.21.69.83.58A12 12 0 0 0 12 .5z" />
                                            </svg>
                                        @else
                                            <i data-lucide="{{ $contactIcon !== '' ? $contactIcon : 'link-2' }}" class="h-4 w-4"></i>
                                        @endif
                                    </span>
                                    <div>
                                        <p class="text-sm text-base-content/65">{{ $contact['label'] }}</p>
                                        <p class="text-sm font-medium text-base-content">{{ $contact['value'] }}</p>
                                    </div>
                                </div>
                                <i data-lucide="arrow-up-right" class="h-4 w-4 text-base-content/60 transition group-hover:text-info"></i>
                            </a>
                        @endforeach
                    </div>
                </article>

                <article data-aos="fade-up" data-aos-duration="800" data-aos-delay="120" class="portfolio-glass rounded-4xl border border-white/10 p-6 shadow-2xl sm:p-8">
                    <h3 class="text-2xl font-semibold text-base-content">{{ $contactFormTitle }}</h3>
                    <form id="contact-form" class="mt-6 space-y-4" novalidate>
                        <x-ui.input-field
                            :label="__('common.name')"
                            name="name"
                            type="text"
                            placeholder="Your name"
                            :required="true"
                            labelClass="label-text text-sm text-base-content/70"
                            inputClass="border-white/15 bg-base-100/60"
                        />

                        <x-ui.input-field
                            :label="__('common.email')"
                            name="email"
                            type="email"
                            placeholder="you@example.com"
                            :required="true"
                            labelClass="label-text text-sm text-base-content/70"
                            inputClass="border-white/15 bg-base-100/60"
                        />

                        <x-ui.textarea-field
                            :label="__('common.message')"
                            name="message"
                            :rows="5"
                            placeholder="Tell me about your project"
                            :required="true"
                            labelClass="label-text text-sm text-base-content/70"
                            textareaClass="border-white/15 bg-base-100/60"
                        />

                        <button id="contact-submit" type="submit" class="btn btn-info w-full rounded-xl text-base-content">
                            <span class="submit-label">{{ $contactSubmitText }}</span>
                            <span class="loading loading-spinner loading-sm hidden"></span>
                        </button>
                    </form>
                </article>
            </div>
        </section>
        </main>

        <footer class="border-t border-white/10 px-4 py-8 sm:px-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-lg font-semibold text-base-content">{{ $logoText }}</p>
                <p class="text-sm text-base-content/65">{{ $footerTagline }}</p>
                <p class="mt-1 text-xs text-info">{{ $footerCta }}</p>
            </div>

            <div class="flex items-center gap-3">
                @foreach ($footerSocials as $social)
                    @php($socialIcon = trim((string) ($social['icon'] ?? 'link-2')))
                    <a href="{{ $social['link'] ?? '#' }}" target="_blank" rel="noreferrer" class="btn btn-circle btn-ghost" aria-label="{{ $social['label'] ?? 'Social' }}">
                        @if ($socialIcon === 'brand-linkedin')
                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 fill-current" role="img">
                                <path d="M20.45 20.45h-3.56v-5.58c0-1.33-.03-3.04-1.85-3.04-1.86 0-2.15 1.45-2.15 2.94v5.68H9.33V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.07 2.07 0 1 1 0-4.14 2.07 2.07 0 0 1 0 4.14zM7.12 20.45H3.55V9h3.57v11.45zM22.23 0H1.77A1.76 1.76 0 0 0 0 1.74v20.52C0 23.23.79 24 1.77 24h20.46c.98 0 1.77-.77 1.77-1.74V1.74A1.76 1.76 0 0 0 22.23 0z" />
                            </svg>
                        @elseif ($socialIcon === 'brand-x')
                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 fill-current" role="img">
                                <path d="M18.9 2H21l-6.53 7.46L22.2 22h-6.05l-4.74-6.2L5.94 22H3.84l6.98-7.97L2 2h6.2l4.28 5.66L18.9 2zm-1.06 18h1.67L7.28 3.9H5.48L17.84 20z" />
                            </svg>
                        @elseif ($socialIcon === 'brand-github' || $socialIcon === 'github')
                            <svg aria-hidden="true" viewBox="0 0 24 24" class="h-4 w-4 fill-current" role="img">
                                <path d="M12 .5a12 12 0 0 0-3.79 23.39c.6.1.82-.26.82-.58v-2.04c-3.34.73-4.04-1.61-4.04-1.61-.55-1.39-1.33-1.77-1.33-1.77-1.09-.74.08-.72.08-.72 1.2.09 1.84 1.23 1.84 1.23 1.07 1.84 2.8 1.31 3.49 1 .11-.78.42-1.31.76-1.61-2.66-.3-5.46-1.33-5.46-5.92 0-1.31.47-2.38 1.23-3.22-.12-.3-.53-1.52.12-3.17 0 0 1-.32 3.3 1.23a11.5 11.5 0 0 1 6 0c2.29-1.55 3.29-1.23 3.29-1.23.65 1.65.24 2.87.12 3.17.77.84 1.23 1.91 1.23 3.22 0 4.6-2.8 5.61-5.47 5.91.43.37.82 1.1.82 2.22v3.29c0 .32.21.69.83.58A12 12 0 0 0 12 .5z" />
                            </svg>
                        @else
                            <i data-lucide="{{ $socialIcon !== '' ? $socialIcon : 'link-2' }}" class="h-4 w-4"></i>
                        @endif
                    </a>
                @endforeach
            </div>

            <p class="text-xs text-base-content/55">© {{ now()->year }} {{ $logoText }}. {{ $footerCopyright }}</p>
        </div>
        </footer>

        <button id="back-to-top" class="btn btn-info btn-circle fixed bottom-[calc(env(safe-area-inset-bottom)+2rem)] right-5 z-50 translate-y-6 opacity-0 shadow-xl transition-all duration-300" aria-label="Back to top">
            <i data-lucide="arrow-up" class="h-4 w-4"></i>
        </button>

        <div id="contact-toast" class="toast toast-top toast-end z-50 hidden">
            <div class="alert alert-success shadow-lg">
                <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                <span>Message sent successfully. I will get back to you soon.</span>
            </div>
        </div>
</div>
