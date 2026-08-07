<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (['grade_levels', 'sections', 'subjects', 'programs'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('educational_level_id')
                    ->nullable()
                    ->after('id')
                    ->constrained()
                    ->restrictOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['grade_levels', 'sections', 'subjects', 'programs'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('educational_level_id');
            });
        }
    }
};
