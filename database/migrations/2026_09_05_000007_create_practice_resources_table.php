<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200);
            $table->string('kind', 20)->default('practice');
            $table->string('resource_type', 40)->default('exercise');
            $table->string('short_description', 255)->nullable();
            $table->text('instructions')->nullable();
            $table->longText('content')->nullable();
            $table->longText('answer_key')->nullable();
            $table->string('resource_url', 2048)->nullable();
            $table->unsignedInteger('estimated_duration_minutes')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->unique(['lesson_id', 'slug'], 'practice_resources_lesson_slug_unique');
            $table->index(['lesson_id', 'sort_order']);
            $table->index(['lesson_id', 'is_published']);
            $table->index(['kind', 'resource_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_resources');
    }
};
