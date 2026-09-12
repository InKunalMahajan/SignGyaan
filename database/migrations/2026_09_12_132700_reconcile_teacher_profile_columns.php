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

        if (! Schema::hasColumn('teacher_profiles', 'phone')) {
            Schema::table('teacher_profiles', function (Blueprint $table): void {
                $table->string('phone', 30)->nullable();
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'designation')) {
            Schema::table('teacher_profiles', function (Blueprint $table): void {
                $table->string('designation', 120)->nullable();
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'qualification')) {
            Schema::table('teacher_profiles', function (Blueprint $table): void {
                $table->string('qualification', 160)->nullable();
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'preferred_language')) {
            Schema::table('teacher_profiles', function (Blueprint $table): void {
                $table->string('preferred_language', 20)->default('english');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'communication_mode')) {
            Schema::table('teacher_profiles', function (Blueprint $table): void {
                $table->string('communication_mode', 20)->default('both');
            });
        }

        if (! Schema::hasColumn('teacher_profiles', 'bio')) {
            Schema::table('teacher_profiles', function (Blueprint $table): void {
                $table->text('bio')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Compatibility migration: do not remove columns or existing profile data.
    }
};
