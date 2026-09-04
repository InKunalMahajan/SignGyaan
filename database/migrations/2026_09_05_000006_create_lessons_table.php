<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200);
            $table->string('short_description', 255)->nullable();
            $table->longText('learning_objectives')->nullable();
            $table->longText('content')->nullable();
            $table->longText('key_points')->nullable();
            $table->longText('example_content')->nullable();
            $table->string('isl_video_url', 2048)->nullable();
            $table->unsignedInteger('estimated_duration_minutes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->unique(['unit_id', 'slug'], 'lessons_unit_slug_unique');
            $table->index(['unit_id', 'sort_order']);
            $table->index(['unit_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
