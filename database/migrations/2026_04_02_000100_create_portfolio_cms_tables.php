<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('admin')->after('email')->index();
            });
        }

        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->string('type')->default('json');
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('href');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->index();
            $table->unsignedTinyInteger('level')->default(50);
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->text('description');
            $table->json('tech_stack')->nullable();
            $table->string('image_path')->nullable();
            $table->string('demo_link')->nullable();
            $table->string('github_link')->nullable();
            $table->string('category')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('role');
            $table->string('company')->nullable();
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->text('message');
            $table->string('avatar_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->boolean('is_read')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('services');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('site_settings');

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
