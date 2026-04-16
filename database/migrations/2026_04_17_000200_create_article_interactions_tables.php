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
        Schema::create('article_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('article_comments')->cascadeOnDelete();
            $table->unsignedTinyInteger('depth')->default(0);
            $table->string('guest_name', 80);
            $table->string('guest_email', 120)->nullable();
            $table->text('body');
            $table->string('owner_token_hash', 64)->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->boolean('is_edited')->default(false);
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();

            $table->index(['article_id', 'parent_id', 'created_at'], 'article_comments_thread_index');
        });

        Schema::create('article_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->string('device_token_hash', 64);
            $table->timestamps();

            $table->unique(['article_id', 'device_token_hash'], 'article_likes_unique_device');
        });

        Schema::create('article_comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_comment_id')->constrained('article_comments')->cascadeOnDelete();
            $table->string('device_token_hash', 64);
            $table->timestamps();

            $table->unique(['article_comment_id', 'device_token_hash'], 'article_comment_likes_unique_device');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_comment_likes');
        Schema::dropIfExists('article_likes');
        Schema::dropIfExists('article_comments');
    }
};
