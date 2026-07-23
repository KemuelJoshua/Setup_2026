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
        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
        });

        Schema::rename('semesters', 'academic_terms');

        Schema::table('academic_terms', function (Blueprint $table) {
            $table->string('type')->default('Semester')->after('code');
        });

        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->renameColumn('semester_id', 'academic_term_id');
        });

        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->foreign('academic_term_id')
                ->references('id')
                ->on('academic_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->dropForeign(['academic_term_id']);
        });

        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->renameColumn('academic_term_id', 'semester_id');
        });

        Schema::table('academic_terms', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::rename('academic_terms', 'semesters');

        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->foreign('semester_id')->references('id')->on('semesters');
        });
    }
};
