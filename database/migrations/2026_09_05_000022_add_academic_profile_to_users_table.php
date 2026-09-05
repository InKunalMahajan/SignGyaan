<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('education_board', 30)->nullable()->after('admin_note')->index();
            $table->string('standard', 20)->nullable()->after('education_board')->index();
            $table->string('academic_year', 20)->nullable()->after('standard')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['education_board']);
            $table->dropIndex(['standard']);
            $table->dropIndex(['academic_year']);
            $table->dropColumn([
                'education_board',
                'standard',
                'academic_year',
            ]);
        });
    }
};
