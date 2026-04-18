<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\SiteSetting;
use App\Support\LocalizedContent;
use Illuminate\Database\Seeder;

class PortfolioCmsI18nSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => 'seo'],
            [
                'value' => [
                    'title' => LocalizedContent::pack('Wisnu.dev | Fullstack Web Developer', 'Wisnu.dev | Fullstack Web Developer'),
                    'description' => LocalizedContent::pack(
                        'Portofolio dan website CMS untuk layanan engineering fullstack.',
                        'Portfolio and CMS-powered website for fullstack engineering services.'
                    ),
                    'keywords' => LocalizedContent::pack(
                        'fullstack developer, laravel developer, devops engineer, portfolio',
                        'fullstack developer, laravel developer, devops engineer, portfolio'
                    ),
                ],
                'type' => 'json',
            ]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navbar'],
            [
                'value' => [
                    'logo_text' => LocalizedContent::pack('Wisnu.dev', 'Wisnu.dev'),
                    'brand_mode' => 'text',
                    'brand_logo_type' => 'image',
                    'brand_logo_icon' => 'sparkles',
                    'brand_logo_image' => null,
                    'cta_text' => LocalizedContent::pack('Hubungi Saya', 'Hire Me'),
                    'cta_link' => '#contact',
                ],
                'type' => 'json',
            ]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'hero'],
            [
                'value' => [
                    'headline' => LocalizedContent::pack(
                        'Fullstack Web Developer & Problem Solver',
                        'Fullstack Web Developer & Problem Solver'
                    ),
                    'subheadline' => LocalizedContent::pack(
                        'Membangun aplikasi web scalable dengan teknologi modern.',
                        'Building scalable web applications with modern technologies.'
                    ),
                    'roles' => [
                        'id' => ['Web Developer', 'DevOps Engineer', 'IT Support'],
                        'en' => ['Web Developer', 'DevOps Engineer', 'IT Support'],
                    ],
                    'primary_cta_text' => LocalizedContent::pack('Lihat Proyek', 'View Projects'),
                    'primary_cta_link' => '#projects',
                    'secondary_cta_text' => LocalizedContent::pack('Unduh CV', 'Download CV'),
                    'secondary_cta_link' => '#',
                    'image' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1200&q=80',
                    'secondary_cta_file' => null,
                ],
                'type' => 'json',
            ]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'about'],
            [
                'value' => [
                    'profile_image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1000&q=80',
                    'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1000&q=80',
                    'title' => LocalizedContent::pack(
                        'Dari digital business ke engineering produk yang berdampak',
                        'From digital business to engineering impactful products'
                    ),
                    'description' => LocalizedContent::pack(
                        '<p>Perjalanan saya berawal dari bisnis produk digital, lalu berkembang ke pengembangan web aplikasi dan deployment production.</p>',
                        '<p>My journey started in digital product business, then grew into web application engineering and production deployment.</p>'
                    ),
                    'stats' => [
                        'id' => [
                            ['label' => 'Proyek Deploy', 'value' => '7+'],
                            ['label' => 'Pengalaman', 'value' => '5 Tahun'],
                            ['label' => 'Tech Mastery', 'value' => 'Multi Stack'],
                        ],
                        'en' => [
                            ['label' => 'Projects Deployed', 'value' => '7+'],
                            ['label' => 'Experience', 'value' => '5 Years'],
                            ['label' => 'Tech Mastery', 'value' => 'Multi Stack'],
                        ],
                    ],
                ],
                'type' => 'json',
            ]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'contact_info'],
            [
                'value' => [
                    'email' => 'wisnu.dev@example.com',
                    'whatsapp' => '+62 812-3456-7890',
                    'linkedin' => 'https://www.linkedin.com',
                    'github' => 'https://github.com',
                    'contact_badge' => LocalizedContent::pack('Kontak', 'Contact'),
                    'contact_title' => LocalizedContent::pack(
                        'Mari bangun sesuatu yang bermakna',
                        "Let's build something meaningful"
                    ),
                    'contact_description' => LocalizedContent::pack(
                        'Terbuka untuk freelance dan kolaborasi jangka panjang. Hubungi kapan saja.',
                        'Open for freelance and long-term collaboration. Reach out anytime.'
                    ),
                    'form_title' => LocalizedContent::pack('Kirim Pesan', 'Send a message'),
                    'submit_text' => LocalizedContent::pack('Kirim Pesan', 'Send Message'),
                ],
                'type' => 'json',
            ]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'footer'],
            [
                'value' => [
                    'tagline' => LocalizedContent::pack('Terbuka untuk freelance & kolaborasi', 'Open for freelance & collaboration'),
                    'copyright' => LocalizedContent::pack('Dirancang dengan presisi.', 'Crafted with precision.'),
                    'cta' => LocalizedContent::pack('Siap bantu proyek digital Anda', 'Ready to support your digital project'),
                    'socials' => [
                        [
                            'label' => LocalizedContent::pack('GitHub', 'GitHub'),
                            'link' => 'https://github.com',
                            'icon' => 'brand-github',
                        ],
                        [
                            'label' => LocalizedContent::pack('LinkedIn', 'LinkedIn'),
                            'link' => 'https://www.linkedin.com',
                            'icon' => 'brand-linkedin',
                        ],
                        [
                            'label' => LocalizedContent::pack('Email', 'Email'),
                            'link' => 'mailto:wisnu.dev@example.com',
                            'icon' => 'mail',
                        ],
                    ],
                ],
                'type' => 'json',
            ]
        );

        SiteSetting::query()->updateOrCreate(
            ['key' => 'landing_sections'],
            [
                'value' => [
                    'about_badge' => LocalizedContent::pack('Tentang Saya', 'About Me'),
                    'skills_badge' => LocalizedContent::pack('Keahlian', 'Skills'),
                    'skills_title' => LocalizedContent::pack(
                        'Tumpukan Teknologi yang Saya Gunakan untuk Membangun Produk Andal',
                        'Technology Stack I Use to Ship Reliable Products'
                    ),
                    'projects_badge' => LocalizedContent::pack('Proyek', 'Projects'),
                    'projects_title' => LocalizedContent::pack(
                        'Pilihan karya lintas produk dan sistem',
                        'Selected work across products and systems'
                    ),
                    'featured_badge' => LocalizedContent::pack('Unggulan', 'Featured'),
                    'featured_title' => LocalizedContent::pack('Proyek yang ditonjolkan', 'Highlighted projects'),
                    'journal_badge' => LocalizedContent::pack('Jurnal', 'Journal'),
                    'journal_title' => LocalizedContent::pack('Catatan dan artikel terbaru', 'Latest notes and articles'),
                    'journey_badge' => LocalizedContent::pack('Perjalanan', 'Journey'),
                    'journey_title' => LocalizedContent::pack('Pendidikan & Pengalaman', 'Education & Experience'),
                    'education_badge' => LocalizedContent::pack('Pendidikan', 'Education'),
                    'education_title' => LocalizedContent::pack('Riwayat Pendidikan', 'Education History'),
                    'experience_badge' => LocalizedContent::pack('Pengalaman', 'Experience'),
                    'experience_title' => LocalizedContent::pack(
                        'Milestone dari bisnis ke engineering',
                        'Milestones from business to engineering'
                    ),
                    'services_badge' => LocalizedContent::pack('Layanan', 'Services'),
                    'services_title' => LocalizedContent::pack('Cara saya membantu bisnis Anda', 'How I can help your business'),
                    'testimonials_badge' => LocalizedContent::pack('Testimoni', 'Testimonials'),
                    'testimonials_title' => LocalizedContent::pack('Apa kata klien', 'What clients say'),
                ],
                'type' => 'json',
            ]
        );

        $menus = [
            ['label_id' => 'Beranda', 'label_en' => 'Home', 'href' => '#home', 'sort_order' => 1],
            ['label_id' => 'Tentang', 'label_en' => 'About', 'href' => '#about', 'sort_order' => 2],
            ['label_id' => 'Pendidikan', 'label_en' => 'Education', 'href' => '#education', 'sort_order' => 3],
            ['label_id' => 'Keahlian', 'label_en' => 'Skills', 'href' => '#skills', 'sort_order' => 4],
            ['label_id' => 'Proyek', 'label_en' => 'Projects', 'href' => '#projects', 'sort_order' => 5],
            ['label_id' => 'Pengalaman', 'label_en' => 'Experience', 'href' => '#experience', 'sort_order' => 6],
            ['label_id' => 'Jurnal', 'label_en' => 'Journal', 'href' => '/journal', 'sort_order' => 7],
            ['label_id' => 'Kontak', 'label_en' => 'Contact', 'href' => '#contact', 'sort_order' => 8],
        ];

        foreach ($menus as $menu) {
            MenuItem::query()->updateOrCreate(
                ['href' => $menu['href']],
                [
                    'label' => json_encode(
                        LocalizedContent::pack($menu['label_id'], $menu['label_en']),
                        JSON_UNESCAPED_UNICODE
                    ),
                    'sort_order' => $menu['sort_order'],
                    'is_visible' => true,
                ]
            );
        }
    }
}
