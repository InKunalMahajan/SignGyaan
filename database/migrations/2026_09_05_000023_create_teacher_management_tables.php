<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('employee_code', 50)->nullable()->unique();
            $table->string('qualification', 255)->nullable();
            $table->string('specialization', 255)->nullable();
            $table->unsignedSmallInteger('experience_years')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });

        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['subject_id', 'user_id']);
        });

        Schema::create('course_teacher', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['course_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_teacher');
        Schema::dropIfExists('subject_teacher');
        Schema::dropIfExists('teacher_profiles');
    }
};
