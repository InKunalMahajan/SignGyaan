<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('learner_profiles')) {
            return;
        }

        Schema::create('learner_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('avatar_path')->nullable();
            $table->string('education_level', 100)->nullable();
            $table->string('class_grade', 100)->nullable();
            $table->string('institution', 160)->nullable();
            $table->string('preferred_language', 20)->default('english');
            $table->string('communication_mode', 20)->default('both');
            $table->boolean('captions_enabled')->default(true);
            $table->boolean('high_contrast')->default(false);
            $table->boolean('reduced_motion')->default(false);
            $table->string('text_size', 20)->default('standard');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learner_profiles');
    }
};
