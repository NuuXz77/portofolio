<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\MenuItem;
use App\Models\PortfolioCategory;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Testimonial;
use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@wisnu.dev'],
            [
                'name' => 'Wisnu Admin',
                'role' => 'admin',
                'password' => 'password',
            ]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'seo'],
            ['value' => [
                'title' => 'Wisnu.dev | Fullstack Web Developer',
                'description' => 'Portfolio and CMS powered website for fullstack engineering services.',
            ]]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navbar'],
            ['value' => [
                'logo_text' => 'Wisnu.dev',
                'cta_text' => 'Hire Me',
                'cta_link' => '#contact',
            ]]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'hero'],
            ['value' => [
                'headline' => 'Fullstack Web Developer & Problem Solver',
                'subheadline' => 'Building scalable web applications with modern technologies like Laravel, Next.js, and DevOps practices.',
                'roles' => ['Web Developer', 'DevOps Engineer', 'IT Support'],
                'primary_cta_text' => 'View Projects',
                'primary_cta_link' => '#projects',
                'secondary_cta_text' => 'Download CV',
                'secondary_cta_link' => '#',
                'image' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1200&q=80',
            ]]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'about'],
            ['value' => [
                'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1000&q=80',
                'title' => 'From digital business to engineering impactful products',
                'description' => '<p>My journey started in the digital product business, where I learned how to understand users and build value that lasts.</p><p>I have delivered multiple web applications, designed backend services, and handled deployment workflows.</p>',
                'stats' => [
                    ['label' => 'Projects Deployed', 'value' => '7+'],
                    ['label' => 'Experience', 'value' => '5 Years'],
                    ['label' => 'Tech Mastery', 'value' => 'Multi Stack'],
                ],
            ]]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact_info'],
            ['value' => [
                'email' => 'wisnu.dev@example.com',
                'whatsapp' => '+62 812-3456-7890',
                'linkedin' => 'https://www.linkedin.com',
                'github' => 'https://github.com',
            ]]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'footer'],
            ['value' => [
                'tagline' => 'Open for freelance & collaboration',
                'copyright' => 'Crafted with precision.',
                'cta' => 'Open for freelance & collaboration',
                'socials' => [
                    ['label' => 'GitHub', 'link' => 'https://github.com'],
                    ['label' => 'LinkedIn', 'link' => 'https://www.linkedin.com'],
                    ['label' => 'Email', 'link' => 'mailto:wisnu.dev@example.com'],
                ],
            ]]
        );

        foreach ([
            ['label' => 'Home', 'href' => '#home', 'sort_order' => 1],
            ['label' => 'About', 'href' => '#about', 'sort_order' => 2],
            ['label' => 'Skills', 'href' => '#skills', 'sort_order' => 3],
            ['label' => 'Projects', 'href' => '#projects', 'sort_order' => 4],
            ['label' => 'Experience', 'href' => '#experience', 'sort_order' => 5],
            ['label' => 'Journal', 'href' => '/journal', 'sort_order' => 6],
            ['label' => 'Contact', 'href' => '#contact', 'sort_order' => 7],
        ] as $menu) {
            MenuItem::query()->updateOrCreate(
                ['label' => $menu['label']],
                $menu + ['is_visible' => true]
            );
        }

        $journalCategories = [
            ['name' => 'Engineering', 'slug' => 'engineering', 'description' => 'Technical notes and architecture insights.'],
            ['name' => 'DevOps', 'slug' => 'devops', 'description' => 'Deployment and infrastructure journey.'],
            ['name' => 'Daily Log', 'slug' => 'daily-log', 'description' => 'Daily progress and working notes.'],
        ];

        foreach ($journalCategories as $categoryData) {
            ArticleCategory::query()->updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        foreach ([
            ['name' => 'Next.js', 'category' => 'Frontend', 'level' => 92, 'icon' => 'layout-template', 'sort_order' => 1],
            ['name' => 'React', 'category' => 'Frontend', 'level' => 90, 'icon' => 'component', 'sort_order' => 2],
            ['name' => 'Laravel', 'category' => 'Backend', 'level' => 95, 'icon' => 'server-cog', 'sort_order' => 3],
            ['name' => 'MySQL', 'category' => 'Database', 'level' => 90, 'icon' => 'database', 'sort_order' => 4],
            ['name' => 'Docker', 'category' => 'DevOps', 'level' => 86, 'icon' => 'container', 'sort_order' => 5],
        ] as $skill) {
            Skill::query()->updateOrCreate(
                ['name' => $skill['name']],
                $skill + ['is_visible' => true]
            );
        }

        $projectCategories = [
            ['name' => 'HR & Attendance Systems', 'slug' => 'hr-attendance', 'description' => 'Attendance, employee management, and leave workflows.', 'sort_order' => 1],
            ['name' => 'Education Management Systems', 'slug' => 'education-management', 'description' => 'School discipline and student information systems.', 'sort_order' => 2],
            ['name' => 'Production & POS Systems', 'slug' => 'production-pos', 'description' => 'Production, inventory, and cashier operations.', 'sort_order' => 3],
            ['name' => 'Company Profile Websites', 'slug' => 'company-profile', 'description' => 'Corporate websites and content management.', 'sort_order' => 4],
            ['name' => 'Rental & Booking Platforms', 'slug' => 'rental-booking', 'description' => 'Rental transactions, booking, and payment workflows.', 'sort_order' => 5],
        ];

        $projectCategoryMap = [];

        foreach ($projectCategories as $category) {
            $model = PortfolioCategory::query()->updateOrCreate(
                ['type' => 'project', 'slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'sort_order' => $category['sort_order'],
                    'is_visible' => true,
                ]
            );

            $projectCategoryMap[$category['slug']] = $model;
        }

        foreach ([
            [
                'title' => 'Kusen (Kudu Absen)',
                'category_slug' => 'hr-attendance',
                'description' => 'Digital attendance platform with face recognition, location validation, leave request workflow, and structured employee management by department and position.',
                'tech_stack' => ['Laravel', 'Tailwind CSS', 'Livewire', 'DaisyUI', 'PHP 8.2'],
                'demo_link' => '#',
                'github_link' => '#',
                'is_featured' => true,
                'sort_order' => 1,
                'image_path' => 'https://picsum.photos/seed/kusen-pplg/1200/800',
            ],
            [
                'title' => 'Siska (Sistem Informasi Ketertiban Siswa)',
                'category_slug' => 'education-management',
                'description' => 'School discipline management system with multi-role access, violation logging via barcode/manual input, class scoring, and export-ready reports.',
                'tech_stack' => ['Laravel', 'Tailwind CSS', 'Livewire', 'DaisyUI', 'PHP 8.2'],
                'demo_link' => '#',
                'github_link' => '#',
                'is_featured' => true,
                'sort_order' => 2,
                'image_path' => 'https://picsum.photos/seed/siska-pplg/1200/800',
            ],
            [
                'title' => 'Rosemary',
                'category_slug' => 'production-pos',
                'description' => 'Integrated platform for production planning, inventory movement, POS cashier transactions, purchases, and waste tracking with role-based controls.',
                'tech_stack' => ['Laravel 12', 'Livewire 3', 'Tailwind CSS 4', 'DaisyUI 5', 'MySQL', 'ApexCharts', 'Spatie Permission'],
                'demo_link' => '#',
                'github_link' => '#',
                'is_featured' => true,
                'sort_order' => 3,
                'image_path' => 'https://picsum.photos/seed/rosemary-pplg/1200/800',
            ],
            [
                'title' => 'Compro TMS',
                'category_slug' => 'company-profile',
                'description' => 'Dynamic company profile website with centralized CMS, chatbot support, vacancy publication, and admin analytics dashboard for content operations.',
                'tech_stack' => ['Laravel 10', 'Tailwind CSS', 'Vite', 'AdminLTE 3', 'Chart.js', 'MySQL'],
                'demo_link' => '#',
                'github_link' => '#',
                'is_featured' => false,
                'sort_order' => 4,
                'image_path' => 'https://picsum.photos/seed/compro-tms/1200/800',
            ],
            [
                'title' => 'Sewa Motor (UJIKOM)',
                'category_slug' => 'rental-booking',
                'description' => 'Motorbike rental web app with booking flow, payment processing, return management, role-based dashboards, and revenue sharing reports.',
                'tech_stack' => ['Laravel 12', 'Livewire 3', 'Volt', 'Tailwind CSS 4', 'DaisyUI 5', 'Mary UI', 'MySQL'],
                'demo_link' => '#',
                'github_link' => '#',
                'is_featured' => true,
                'sort_order' => 5,
                'image_path' => 'https://picsum.photos/seed/sewa-motor-ujikom/1200/800',
            ],
        ] as $project) {
            $category = $projectCategoryMap[$project['category_slug']] ?? null;

            Project::query()->updateOrCreate(
                ['title' => $project['title']],
                [
                    'slug' => Str::slug($project['title']),
                    'description' => $project['description'],
                    'tech_stack' => $project['tech_stack'],
                    'image_path' => $project['image_path'],
                    'demo_link' => $project['demo_link'],
                    'github_link' => $project['github_link'],
                    'category' => $category?->slug ?? $project['category_slug'],
                    'category_id' => $category?->id,
                    'is_featured' => $project['is_featured'],
                    'is_visible' => true,
                    'sort_order' => $project['sort_order'],
                ]
            );
        }

        $engineeringCategory = ArticleCategory::query()->where('slug', 'engineering')->first();
        $devopsCategory = ArticleCategory::query()->where('slug', 'devops')->first();
        $dailyCategory = ArticleCategory::query()->where('slug', 'daily-log')->first();

        foreach ([
            [
                'title' => 'Designing a Scalable Laravel Module Structure',
                'slug' => 'designing-a-scalable-laravel-module-structure',
                'excerpt' => 'How I keep a large CMS codebase maintainable with modular Livewire and reusable components.',
                'content' => '<h2>Context</h2><p>As the codebase grows, one of the biggest risks is placing too many responsibilities in a single component.</p><h2>Approach</h2><p>I split each CMS domain into focused Livewire components and shared UI components for consistency.</p><pre><code class="language-php">return view(\'admin.cms.journal.articles-manager\');</code></pre>',
                'tags' => ['laravel', 'livewire', 'architecture'],
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subDays(6),
                'author_name' => 'Wisnu Admin',
                'read_time' => 4,
                'category_id' => $engineeringCategory?->id,
            ],
            [
                'title' => 'Practical CI/CD Checklist for Portfolio Projects',
                'slug' => 'practical-ci-cd-checklist-for-portfolio-projects',
                'excerpt' => 'A practical deployment checklist to reduce regressions and speed up release confidence.',
                'content' => '<h2>Deployment Flow</h2><p>Small projects still need guardrails: lint, tests, migration checks, and health probes.</p><ul><li>Run tests</li><li>Build assets</li><li>Smoke test key routes</li></ul>',
                'tags' => ['devops', 'ci-cd', 'deployment'],
                'status' => 'published',
                'visibility' => 'public',
                'published_at' => now()->subDays(3),
                'author_name' => 'Wisnu Admin',
                'read_time' => 3,
                'category_id' => $devopsCategory?->id,
            ],
            [
                'title' => 'Internal Notes: Private Delivery Plan',
                'slug' => 'internal-notes-private-delivery-plan',
                'excerpt' => 'Private planning notes for upcoming implementation milestones.',
                'content' => '<h2>Private Notes</h2><p>This content is intended for private preview only.</p><p>Use the generated tokenized URL for controlled access.</p>',
                'tags' => ['private', 'planning'],
                'status' => 'published',
                'visibility' => 'private',
                'published_at' => now()->subDay(),
                'author_name' => 'Wisnu Admin',
                'read_time' => 2,
                'access_token' => Str::random(40),
                'category_id' => $dailyCategory?->id,
            ],
        ] as $article) {
            Article::query()->updateOrCreate(
                ['slug' => $article['slug']],
                $article + [
                    'thumbnail_path' => 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?auto=format&fit=crop&w=1200&q=80',
                    'content' => $article['content'],
                    'view_count' => 0,
                ]
            );
        }

        foreach ([
            ['title' => 'Web Development', 'description' => 'Custom, scalable web apps from concept to deployment.', 'icon' => 'code-xml', 'sort_order' => 1],
            ['title' => 'API Development', 'description' => 'Secure API architecture for integrations and dashboards.', 'icon' => 'waypoints', 'sort_order' => 2],
            ['title' => 'Deployment & DevOps', 'description' => 'Automation-ready deployment and infra optimization.', 'icon' => 'rocket', 'sort_order' => 3],
            ['title' => 'IT Support', 'description' => 'Reliable troubleshooting and operational support.', 'icon' => 'shield-check', 'sort_order' => 4],
        ] as $service) {
            Service::query()->updateOrCreate(
                ['title' => $service['title']],
                $service + ['is_visible' => true]
            );
        }

        foreach ([
            ['year' => '2024 - Now', 'role' => 'Web Developer (Projects)', 'company' => 'Freelance', 'description' => 'Building production-grade web applications with Laravel and modern frontend stacks.', 'sort_order' => 1],
            ['year' => '2022 - Now', 'role' => 'DevOps & Deployment', 'company' => 'Freelance', 'description' => 'Managing CI/CD pipelines, server provisioning, and production deployments.', 'sort_order' => 2],
            ['year' => '2019 - Now', 'role' => 'Digital Product Seller', 'company' => 'Independent', 'description' => 'Running digital product business with data-driven product improvements.', 'sort_order' => 3],
        ] as $experience) {
            Experience::query()->updateOrCreate(
                ['role' => $experience['role']],
                $experience + ['is_visible' => true]
            );
        }

        foreach ([
            ['name' => 'Arif Nugraha', 'role' => 'Startup Founder', 'message' => 'Architecture is clean and delivery was excellent.', 'sort_order' => 1],
            ['name' => 'Dina Prameswari', 'role' => 'Operations Manager', 'message' => 'Our deployment process became structured and stable.', 'sort_order' => 2],
        ] as $testimonial) {
            Testimonial::query()->updateOrCreate(
                ['name' => $testimonial['name']],
                $testimonial + ['is_visible' => true]
            );
        }

        // Keep multilingual CMS defaults aligned with the latest dynamic schema.
        $this->call(PortfolioCmsI18nSeeder::class);
    }
}
