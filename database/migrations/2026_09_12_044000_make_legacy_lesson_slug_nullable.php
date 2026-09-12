<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lessons') || ! Schema::hasColumn('lessons', 'slug')) {
            return;
        }

        // Some older SignGyaan lesson schemas required a legacy `slug` value.
        // The current Lesson model does not use this column, so keep it only for
        // backward compatibility and allow new current-schema lessons to omit it.
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('slug')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally non-destructive. Restoring NOT NULL would break lessons
        // created by the current schema where the legacy slug is null.
    }
};
