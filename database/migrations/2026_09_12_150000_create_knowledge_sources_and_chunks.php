<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('knowledge_sources')) {
            Schema::create('knowledge_sources', function (Blueprint $table) {
                $table->id();
                $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title');
                $table->string('source_type', 32)->default('manual');
                $table->string('source_ref')->nullable();
                $table->longText('content');
                $table->string('content_hash', 64);
                $table->string('status', 24)->default('draft');
                $table->json('metadata')->nullable();
                $table->timestamp('indexed_at')->nullable();
                $table->text('last_error')->nullable();
                $table->timestamps();

                $table->index(['owner_id', 'status']);
                $table->index(['course_id', 'status']);
                $table->unique(['owner_id', 'source_type', 'source_ref'], 'knowledge_source_ref_unique');
            });
        }

        if (! Schema::hasTable('knowledge_chunks')) {
            Schema::create('knowledge_chunks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('knowledge_source_id')->constrained('knowledge_sources')->cascadeOnDelete();
                $table->unsignedInteger('chunk_index');
                $table->text('content');
                $table->json('embedding');
                $table->unsignedInteger('character_count')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->unique(['knowledge_source_id', 'chunk_index']);
                $table->index('knowledge_source_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_chunks');
        Schema::dropIfExists('knowledge_sources');
    }
};
