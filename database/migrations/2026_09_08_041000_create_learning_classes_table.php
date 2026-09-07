<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('code', 20)->unique();
            $table->string('subject', 120)->nullable();
            $table->string('level', 100)->nullable();
            $table->string('academic_year', 30)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['teacher_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_classes');
    }
};
