<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 180);
            $table->string('media_type', 30);
            $table->string('source', 20)->default('upload');
            $table->string('file_path', 500)->nullable();
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('external_url', 2048)->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->text('caption')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['media_type', 'is_published']);
            $table->index(['source', 'is_published']);
            $table->index('uploaded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
