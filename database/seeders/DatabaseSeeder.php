<?php

namespace Database\Seeders;

use App\Models\MenuItem;
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
            ['label' => 'Contact', 'href' => '#contact', 'sort_order' => 6],
        ] as $menu) {
            MenuItem::query()->updateOrCreate(
                ['label' => $menu['label']],
                $menu + ['is_visible' => true]
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

        foreach ([
            ['title' => 'Inventory Hub Pro', 'category' => 'web-app', 'description' => 'Enterprise inventory management with real-time stock tracking and role-based access.', 'tech_stack' => ['Laravel', 'Livewire', 'MySQL', 'Redis'], 'demo_link' => '#', 'github_link' => '#', 'is_featured' => true, 'sort_order' => 1],
            ['title' => 'Payment API Gateway', 'category' => 'api', 'description' => 'Secure API gateway handling payment orchestration and webhook pipelines.', 'tech_stack' => ['Laravel', 'JWT', 'PostgreSQL'], 'demo_link' => '#', 'github_link' => '#', 'is_featured' => true, 'sort_order' => 2],
            ['title' => 'Ops Monitoring Dashboard', 'category' => 'dashboard', 'description' => 'Observability dashboard for logs, alerts, and deployment health metrics.', 'tech_stack' => ['Next.js', 'Node.js', 'Prometheus'], 'demo_link' => '#', 'github_link' => '#', 'is_featured' => false, 'sort_order' => 3],
        ] as $project) {
            Project::query()->updateOrCreate(
                ['title' => $project['title']],
                $project + [
                    'slug' => Str::slug($project['title']),
                    'image_path' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1200&q=80',
                    'is_visible' => true,
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
    }
}
