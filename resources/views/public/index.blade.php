@php
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
        ? $menuItems->map(fn ($item) => ['href' => $item->href, 'label' => $item->label])->all()
        : [
            ['href' => '#home', 'label' => 'Home'],
            ['href' => '#about', 'label' => 'About'],
            ['href' => '#skills', 'label' => 'Skills'],
            ['href' => '#projects', 'label' => 'Projects'],
            ['href' => '#experience', 'label' => 'Experience'],
            ['href' => '#contact', 'label' => 'Contact'],
        ];

    $logoText = $navbar['logo_text'] ?? 'Wisnu.dev';
    $ctaText = $navbar['cta_text'] ?? 'Hire Me';
    $ctaLink = $navbar['cta_link'] ?? '#contact';

    $heroHeadline = $hero['headline'] ?? 'Fullstack Web Developer & Problem Solver';
    $heroSubheadline = $hero['subheadline'] ?? 'Building scalable web applications with modern technologies.';
    $heroRoles = $hero['roles'] ?? ['Web Developer', 'DevOps Engineer'];
    $heroPrimaryText = $hero['primary_cta_text'] ?? 'View Projects';
    $heroPrimaryLink = $hero['primary_cta_link'] ?? '#projects';
    $heroSecondaryText = $hero['secondary_cta_text'] ?? 'Download CV';
    $heroSecondaryLink = $hero['secondary_cta_link'] ?? '#';
    $heroImage = $resolveAsset($hero['image'] ?? null, 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1200&q=80');

    $aboutTitle = $about['title'] ?? 'About Me';
    $aboutDescription = $about['description'] ?? '<p>Dynamic about description can be managed from admin panel.</p>';
    $aboutStats = $about['stats'] ?? [];
    $aboutImage = $resolveAsset($about['image'] ?? null, 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1000&q=80');

    $projectCategories = $projects->pluck('category')->unique()->values();

    $contactLinks = [
        ['label' => 'Email', 'value' => $contactInfo['email'] ?? '-', 'href' => isset($contactInfo['email']) ? 'mailto:'.$contactInfo['email'] : '#', 'icon' => 'mail'],
        ['label' => 'WhatsApp', 'value' => $contactInfo['whatsapp'] ?? '-', 'href' => isset($contactInfo['whatsapp']) ? 'https://wa.me/'.preg_replace('/\D+/', '', $contactInfo['whatsapp']) : '#', 'icon' => 'message-circle'],
        ['label' => 'LinkedIn', 'value' => $contactInfo['linkedin'] ?? '-', 'href' => $contactInfo['linkedin'] ?? '#', 'icon' => 'linkedin'],
        ['label' => 'GitHub', 'value' => $contactInfo['github'] ?? '-', 'href' => $contactInfo['github'] ?? '#', 'icon' => 'github'],
    ];

    $footerTagline = $footer['tagline'] ?? 'Open for freelance & collaboration';
    $footerCta = $footer['cta'] ?? 'Open for freelance & collaboration';
    $footerCopyright = $footer['copyright'] ?? 'Crafted with precision.';
    $footerSocials = $footer['socials'] ?? [];
@endphp

<div id="portfolio" data-portfolio-root class="relative overflow-x-clip bg-base-100 text-base-content">
    <div aria-hidden="true" class="pointer-events-none portfolio-glow"></div>
    <div aria-hidden="true" class="pointer-events-none portfolio-grid"></div>

    <div id="scroll-progress" class="fixed left-0 top-0 z-50 h-1 w-0 bg-linear-to-r from-cyan-400 via-blue-500 to-violet-500 shadow-[0_0_14px_rgba(80,170,255,0.75)] transition-[width] duration-150"></div>

    <header id="top" class="sticky top-0 z-50 px-4 pt-4 sm:px-8">
        <nav class="portfolio-glass mx-auto flex max-w-7xl items-center justify-between rounded-2xl border border-white/10 px-4 py-3 shadow-2xl backdrop-blur-xl sm:px-6" aria-label="Primary navigation">
            <a href="#home" class="text-xl font-semibold tracking-tight text-base-content">{{ $logoText }}</a>

            <div class="hidden items-center gap-2 lg:flex">
                @foreach ($navItems as $item)
                    <a href="{{ $item['href'] }}" class="btn btn-ghost btn-sm rounded-xl text-base-content/80 hover:bg-white/10 hover:text-base-content">{{ $item['label'] }}</a>
                @endforeach
            </div>

            <div class="hidden items-center gap-2 lg:flex">
                <label class="swap swap-rotate btn btn-circle btn-ghost" aria-label="Toggle color theme">
                    <input id="theme-toggle" type="checkbox" aria-label="Toggle color theme">
                    <i data-lucide="sun" class="swap-on h-5 w-5"></i>
                    <i data-lucide="moon" class="swap-off h-5 w-5"></i>
                </label>
                <a href="{{ $ctaLink }}" class="btn btn-info rounded-xl px-5 text-base-content">{{ $ctaText }}</a>
            </div>

            <button id="mobile-menu-toggle" class="btn btn-ghost btn-circle lg:hidden" aria-label="Open mobile menu" aria-expanded="false" aria-controls="mobile-menu-panel">
                <i data-lucide="menu" class="h-5 w-5"></i>
            </button>
        </nav>

        <div id="mobile-menu-panel" class="portfolio-glass mx-auto mt-2 max-h-0 max-w-7xl overflow-hidden rounded-2xl border border-transparent px-4 opacity-0 transition-all duration-300 lg:hidden">
            <div class="py-4">
                <div class="mb-4 flex items-center justify-between">
                    <span class="text-sm font-medium text-base-content/70">Navigation</span>
                    <label class="swap swap-rotate btn btn-circle btn-ghost btn-sm" aria-label="Toggle color theme">
                        <input id="theme-toggle-mobile" type="checkbox" aria-label="Toggle color theme">
                        <i data-lucide="sun" class="swap-on h-4 w-4"></i>
                        <i data-lucide="moon" class="swap-off h-4 w-4"></i>
                    </label>
                </div>
                <div class="grid gap-2">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['href'] }}" class="mobile-nav-link btn btn-ghost justify-start rounded-xl text-base-content/80 hover:bg-white/10 hover:text-base-content">{{ $item['label'] }}</a>
                    @endforeach
                    <a href="{{ $ctaLink }}" class="mobile-nav-link btn btn-info mt-1 rounded-xl text-base-content">{{ $ctaText }}</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="home" class="relative px-4 pb-20 pt-20 sm:px-8 sm:pt-24 lg:pt-28">
            <div class="mx-auto grid max-w-7xl items-center gap-12 lg:grid-cols-2">
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
                        <a href="{{ $heroSecondaryLink }}" class="btn btn-outline rounded-xl px-6">{{ $heroSecondaryText }}</a>
                    </div>
                </div>

                <div class="relative" data-aos="fade-left" data-aos-duration="900" data-aos-delay="130">
                    <div class="portfolio-glass relative overflow-hidden rounded-4xl border border-white/10 p-4 shadow-2xl">
                        <img loading="eager" src="{{ $heroImage }}" alt="Developer workspace" class="h-96 w-full rounded-3xl object-cover sm:h-120">
                        <div class="absolute inset-0 rounded-3xl bg-linear-to-tr from-base-300/40 via-transparent to-info/20"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="px-4 py-20 sm:px-8">
            <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[1fr,1.2fr] lg:items-center">
                <div data-aos="fade-up" data-aos-duration="800">
                    <div class="portfolio-glass relative overflow-hidden rounded-4xl border border-white/10 p-4 shadow-2xl">
                        <img loading="lazy" src="{{ $aboutImage }}" alt="Professional portrait" class="h-108 w-full rounded-3xl object-cover">
                    </div>
                </div>

                <div data-aos="fade-up" data-aos-duration="800" data-aos-delay="90">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">About Me</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">{{ $aboutTitle }}</h2>
                    <div class="prose prose-invert mt-6 max-w-none text-base-content/75">{!! $aboutDescription !!}</div>

                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                        @foreach ($aboutStats as $stat)
                            <article class="portfolio-glass rounded-2xl border border-white/10 p-4 text-center">
                                <p class="text-2xl font-semibold text-base-content">{{ $stat['value'] ?? '-' }}</p>
                                <p class="mt-1 text-sm text-base-content/70">{{ $stat['label'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section id="skills" class="px-4 py-20 sm:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 text-center" data-aos="fade-up" data-aos-duration="800">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">Skills</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">Technology stack I use to ship reliably</h2>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ($skillsByCategory as $category => $groupSkills)
                        <article data-aos="fade-up" data-aos-duration="800" class="skill-card card portfolio-glass border border-white/10 shadow-xl transition duration-300 hover:-translate-y-1 hover:shadow-info/20">
                            <div class="card-body">
                                <div class="mb-4 flex items-center gap-3">
                                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-info/20 text-info">
                                        <i data-lucide="star" class="h-5 w-5"></i>
                                    </span>
                                    <h3 class="text-xl font-semibold text-base-content">{{ $category }}</h3>
                                </div>

                                <div class="space-y-4">
                                    @foreach ($groupSkills as $skill)
                                        <div class="tooltip" data-tip="{{ $skill->name }} proficiency: {{ $skill->level }}%">
                                            <div class="mb-1 flex items-center justify-between text-sm">
                                                <span class="text-base-content/80">{{ $skill->name }}</span>
                                                <span class="text-base-content/65">{{ $skill->level }}%</span>
                                            </div>
                                            <progress class="progress progress-info h-2 w-full" value="{{ $skill->level }}" max="100"></progress>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="projects" class="px-4 py-20 sm:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-8 text-center" data-aos="fade-up" data-aos-duration="800">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">Projects</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">Selected work across products and systems</h2>
                </div>

                <div class="mb-8 flex flex-wrap justify-center gap-2" data-aos="fade-up" data-aos-duration="800" data-aos-delay="80">
                    <button class="project-filter is-active btn btn-sm rounded-xl border-white/15 bg-base-100/70 text-base-content" data-filter="all">All</button>
                    @foreach ($projectCategories as $category)
                        <button class="project-filter btn btn-sm rounded-xl border-white/15 bg-base-100/70 text-base-content" data-filter="{{ $category }}">{{ ucfirst(str_replace('-', ' ', $category)) }}</button>
                    @endforeach
                </div>

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3" id="project-grid">
                    @foreach ($projects as $project)
                        <article data-aos="fade-up" data-aos-duration="800" data-category="{{ $project->category }}" class="project-card card overflow-hidden rounded-3xl border border-white/10 bg-base-100/75 shadow-xl backdrop-blur-md transition-all duration-300 hover:-translate-y-1 hover:shadow-info/20">
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

        <section class="px-4 py-20 sm:px-8">
            <div class="mx-auto max-w-7xl" data-aos="fade-up" data-aos-duration="800">
                <div class="mb-8 text-center">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">Featured</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">Highlighted projects</h2>
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

        <section id="experience" class="px-4 py-20 sm:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-12 text-center" data-aos="fade-up" data-aos-duration="800">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">Experience</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">Milestones from business to engineering</h2>
                </div>

                <div class="relative mx-auto max-w-3xl">
                    <span aria-hidden="true" class="absolute inset-y-2 left-4 w-px bg-white/15"></span>
                    @foreach ($experiences as $experience)
                        <article data-aos="fade-up" data-aos-duration="800" class="timeline-item relative mb-10 pl-12 last:mb-0 sm:pl-14">
                            <span class="absolute left-4 top-1.5 inline-flex h-4 w-4 -translate-x-1/2 items-center justify-center rounded-full border border-info/50 bg-base-100 shadow-[0_0_0_6px_rgba(30,41,59,0.6)]"></span>
                            <p class="text-sm font-medium text-info">{{ $experience->year }}</p>
                            <h3 class="mt-2 text-xl font-semibold text-base-content">{{ $experience->role }}</h3>
                            <p class="mt-2 text-sm text-base-content/60">{{ $experience->company }}</p>
                            <p class="mt-2 leading-relaxed text-base-content/75">{{ $experience->description }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="services" class="px-4 py-20 sm:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="mb-10 text-center" data-aos="fade-up" data-aos-duration="800">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">Services</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">How I can help your business</h2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($services as $service)
                        <article data-aos="fade-up" data-aos-duration="800" class="card rounded-3xl border border-white/10 bg-base-100/75 shadow-xl backdrop-blur-md transition-all duration-300 hover:-translate-y-2 hover:shadow-info/20">
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

        <section class="px-4 py-20 sm:px-8">
            <div class="mx-auto max-w-7xl" data-aos="fade-up" data-aos-duration="800">
                <div class="mb-8 text-center">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">Testimonials</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content sm:text-4xl">What clients say</h2>
                </div>

                <div class="swiper testimonial-swiper rounded-4xl border border-white/10 bg-base-100/70 p-4 shadow-2xl backdrop-blur-lg sm:p-6">
                    <div class="swiper-wrapper">
                        @foreach ($testimonials as $testimonial)
                            <article class="swiper-slide rounded-3xl border border-white/10 bg-base-200/55 p-6">
                                <i data-lucide="quote" class="h-7 w-7 text-info"></i>
                                <p class="mt-4 text-base leading-relaxed text-base-content/80">{{ $testimonial->message }}</p>
                                <div class="mt-6 flex items-center gap-3">
                                    @if ($testimonial->avatar_path)
                                        <img src="{{ $resolveAsset($testimonial->avatar_path) }}" alt="{{ $testimonial->name }}" class="h-10 w-10 rounded-full object-cover">
                                    @endif
                                    <div>
                                        <p class="font-semibold text-base-content">{{ $testimonial->name }}</p>
                                        <p class="text-sm text-base-content/60">{{ $testimonial->role }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-6 flex items-center justify-between">
                        <div class="swiper-pagination testimonial-pagination relative! bottom-0! left-0!"></div>
                        <div class="flex gap-2">
                            <button class="btn btn-sm btn-circle testimonial-prev" aria-label="Previous testimonial"><i data-lucide="chevron-left" class="h-4 w-4"></i></button>
                            <button class="btn btn-sm btn-circle testimonial-next" aria-label="Next testimonial"><i data-lucide="chevron-right" class="h-4 w-4"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="px-4 pb-24 pt-20 sm:px-8">
            <div class="mx-auto grid max-w-7xl gap-8 lg:grid-cols-2">
                <article data-aos="fade-up" data-aos-duration="800" class="portfolio-glass rounded-4xl border border-white/10 p-6 shadow-2xl sm:p-8">
                    <p class="text-sm uppercase tracking-[0.24em] text-info">Contact</p>
                    <h2 class="mt-3 text-3xl font-semibold text-base-content">Let's build something meaningful</h2>
                    <p class="mt-4 text-base leading-relaxed text-base-content/75">Open for freelance and long-term collaboration. Reach out anytime and I will get back to you quickly.</p>

                    <div class="mt-8 space-y-4">
                        @foreach ($contactLinks as $contact)
                            <a href="{{ $contact['href'] }}" target="_blank" rel="noreferrer" class="group flex items-center justify-between rounded-2xl border border-white/10 bg-base-100/50 px-4 py-3 transition hover:border-info/40 hover:bg-info/10">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-info/20 text-info">
                                        <i data-lucide="{{ $contact['icon'] }}" class="h-4 w-4"></i>
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
                    <h3 class="text-2xl font-semibold text-base-content">Send a message</h3>
                    <form id="contact-form" class="mt-6 space-y-4" novalidate>
                        <x-ui.input-field
                            label="Name"
                            name="name"
                            type="text"
                            placeholder="Your name"
                            :required="true"
                            labelClass="label-text text-sm text-base-content/70"
                            inputClass="border-white/15 bg-base-100/60"
                        />

                        <x-ui.input-field
                            label="Email"
                            name="email"
                            type="email"
                            placeholder="you@example.com"
                            :required="true"
                            labelClass="label-text text-sm text-base-content/70"
                            inputClass="border-white/15 bg-base-100/60"
                        />

                        <x-ui.textarea-field
                            label="Message"
                            name="message"
                            :rows="5"
                            placeholder="Tell me about your project"
                            :required="true"
                            labelClass="label-text text-sm text-base-content/70"
                            textareaClass="border-white/15 bg-base-100/60"
                        />

                        <button id="contact-submit" type="submit" class="btn btn-info w-full rounded-xl text-base-content">
                            <span class="submit-label">Send Message</span>
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
                    <a href="{{ $social['link'] ?? '#' }}" target="_blank" rel="noreferrer" class="btn btn-circle btn-ghost" aria-label="{{ $social['label'] ?? 'Social' }}">
                        <i data-lucide="arrow-up-right" class="h-4 w-4"></i>
                    </a>
                @endforeach
            </div>

            <p class="text-xs text-base-content/55">© {{ now()->year }} {{ $logoText }}. {{ $footerCopyright }}</p>
        </div>
    </footer>

    <button id="back-to-top" class="btn btn-info btn-circle fixed bottom-5 right-5 z-50 translate-y-6 opacity-0 shadow-xl transition-all duration-300" aria-label="Back to top">
        <i data-lucide="arrow-up" class="h-4 w-4"></i>
    </button>

    <div id="contact-toast" class="toast toast-top toast-end z-50 hidden">
        <div class="alert alert-success shadow-lg">
            <i data-lucide="check-circle-2" class="h-5 w-5"></i>
            <span>Message sent successfully. I will get back to you soon.</span>
        </div>
    </div>
</div>
