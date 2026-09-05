<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_content_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->string('type', 40);
            $table->string('title', 180)->nullable();
            $table->longText('body')->nullable();
            $table->foreignId('media_asset_id')->nullable()->constrained('media_assets')->nullOnDelete();
            $table->foreignId('practice_resource_id')->nullable()->constrained('practice_resources')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['lesson_id', 'sort_order']);
            $table->index(['lesson_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_content_blocks');
    }
};
