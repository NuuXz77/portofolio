<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('portfolio_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('type', 20)->index(); // skill | project
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();

            $table->unique(['type', 'slug']);
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('category')->constrained('portfolio_categories')->nullOnDelete();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('category')->constrained('portfolio_categories')->nullOnDelete();
        });

        $now = now();

        $skillCategories = DB::table('skills')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->map(static fn ($value): string => trim((string) $value))
            ->filter()
            ->values();

        foreach ($skillCategories as $name) {
            $slug = Str::slug($name);

            if ($slug === '') {
                continue;
            }

            DB::table('portfolio_categories')->updateOrInsert(
                ['type' => 'skill', 'slug' => $slug],
                [
                    'name' => $name,
                    'description' => null,
                    'sort_order' => 0,
                    'is_visible' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $categoryId = DB::table('portfolio_categories')
                ->where('type', 'skill')
                ->where('slug', $slug)
                ->value('id');

            DB::table('skills')
                ->where('category', $name)
                ->update(['category_id' => $categoryId]);
        }

        $projectCategories = DB::table('projects')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->map(static fn ($value): string => trim((string) $value))
            ->filter()
            ->values();

        foreach ($projectCategories as $name) {
            $slug = Str::slug($name);

            if ($slug === '') {
                continue;
            }

            DB::table('portfolio_categories')->updateOrInsert(
                ['type' => 'project', 'slug' => $slug],
                [
                    'name' => $name,
                    'description' => null,
                    'sort_order' => 0,
                    'is_visible' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $categoryId = DB::table('portfolio_categories')
                ->where('type', 'project')
                ->where('slug', $slug)
                ->value('id');

            DB::table('projects')
                ->where('category', $name)
                ->update(['category_id' => $categoryId]);
        }

        if (! DB::table('portfolio_categories')->where('type', 'skill')->exists()) {
            $defaults = [
                ['name' => 'Frontend', 'slug' => 'frontend'],
                ['name' => 'Backend', 'slug' => 'backend'],
                ['name' => 'Database', 'slug' => 'database'],
                ['name' => 'DevOps', 'slug' => 'devops'],
            ];

            foreach ($defaults as $index => $default) {
                DB::table('portfolio_categories')->insert([
                    'name' => $default['name'],
                    'slug' => $default['slug'],
                    'type' => 'skill',
                    'description' => null,
                    'sort_order' => $index,
                    'is_visible' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (! DB::table('portfolio_categories')->where('type', 'project')->exists()) {
            $defaults = [
                ['name' => 'Web App', 'slug' => 'web-app'],
                ['name' => 'API', 'slug' => 'api'],
                ['name' => 'Dashboard', 'slug' => 'dashboard'],
            ];

            foreach ($defaults as $index => $default) {
                DB::table('portfolio_categories')->insert([
                    'name' => $default['name'],
                    'slug' => $default['slug'],
                    'type' => 'project',
                    'description' => null,
                    'sort_order' => $index,
                    'is_visible' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });

        Schema::table('skills', function (Blueprint $table) {
            if (Schema::hasColumn('skills', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });

        Schema::dropIfExists('portfolio_categories');
    }
};
