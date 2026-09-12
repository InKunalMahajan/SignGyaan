<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('class_course_assignments')) {
            return;
        }

        Schema::create('class_course_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_class_id')->constrained('learning_classes')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['learning_class_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_course_assignments');
    }
};
