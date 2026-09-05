<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type', 40);
            $table->string('subject_slug', 100)->nullable();
            $table->string('course_slug', 140)->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();
            $table->string('lesson_key', 120)->nullable();
            $table->string('title', 180);
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['user_id', 'occurred_at']);
            $table->index(['user_id', 'activity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_activities');
    }
};
