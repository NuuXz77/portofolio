<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title');
            $table->json('excerpt_translations')->nullable()->after('excerpt');
            $table->json('content_translations')->nullable()->after('content');
            $table->json('seo_title_translations')->nullable()->after('seo_title');
            $table->json('seo_description_translations')->nullable()->after('seo_description');
        });

        Schema::table('article_categories', function (Blueprint $table) {
            $table->json('name_translations')->nullable()->after('name');
            $table->json('description_translations')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'title_translations',
                'excerpt_translations',
                'content_translations',
                'seo_title_translations',
                'seo_description_translations',
            ]);
        });

        Schema::table('article_categories', function (Blueprint $table) {
            $table->dropColumn([
                'name_translations',
                'description_translations',
            ]);
        });
    }
};
