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
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('article_categories')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail_path')->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->json('tags')->nullable();
            $table->string('status')->default('draft')->index(); // draft|published
            $table->string('visibility')->default('public')->index(); // public|private
            $table->timestamp('published_at')->nullable()->index();
            $table->string('author_name')->nullable();
            $table->unsignedSmallInteger('read_time')->nullable();
            $table->unsignedBigInteger('view_count')->default(0);
            $table->string('access_token')->nullable()->unique();
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
        Schema::dropIfExists('article_categories');
    }
};
