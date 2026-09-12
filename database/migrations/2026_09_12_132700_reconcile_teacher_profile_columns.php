<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('teacher_profiles')) {
            return;
        }

        Schema::table('teacher_profiles', function (Blueprint $table): void {
            if (! Schema::hasColumn('teacher_profiles', 'phone')) {
                $table->string('phone', 30)->nullable();
            }

            if (! Schema::hasColumn('teacher_profiles', 'designation')) {
                $table->string('designation', 120)->nullable();
            }

            if (! Schema::hasColumn('teacher_profiles', 'qualification')) {
                $table->string('qualification', 160)->nullable();
            }

            if (! Schema::hasColumn('teacher_profiles', 'preferred_language')) {
                $table->string('preferred_language', 20)->default('english');
            }

            if (! Schema::hasColumn('teacher_profiles', 'communication_mode')) {
                $table->string('communication_mode', 20)->default('both');
            }

            if (! Schema::hasColumn('teacher_profiles', 'bio')) {
                $table->text('bio')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Compatibility migration: keep profile data intact on rollback.
    }
};
