<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            $table->foreignId('program_id')
                ->nullable()
                ->after('name')
                ->constrained()
                ->restrictOnDelete();
            $table->foreignId('academic_term_structure_id')
                ->nullable()
                ->after('program_id')
                ->constrained()
                ->restrictOnDelete();
            $table->unsignedTinyInteger('number_of_years')
                ->default(1)
                ->after('academic_term_structure_id')
                ->index();
        });

        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->decimal('units', 5, 2)->nullable()->after('academic_period_id');
            $table->decimal('lecture_hours', 5, 2)->nullable()->after('units');
            $table->decimal('laboratory_hours', 5, 2)->nullable()->after('lecture_hours');
            $table->text('remarks')->nullable()->after('sort_order');

            $table->unique(
                ['curriculum_id', 'subject_id', 'year_level_id', 'academic_period_id'],
                'curriculum_subject_assignment_unique',
            );
            $table->index(
                ['curriculum_id', 'year_level_id', 'academic_period_id', 'sort_order'],
                'curriculum_subject_group_order_index',
            );
        });

        Schema::create('curriculum_subject_prerequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_subject_id')
                ->constrained(
                    'curriculum_subjects',
                    'id',
                    'curr_subj_prereq_subject_fk',
                )
                ->cascadeOnDelete();
            $table->foreignId('prerequisite_curriculum_subject_id')
                ->constrained(
                    'curriculum_subjects',
                    'id',
                    'curr_subj_prereq_target_fk',
                )
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['curriculum_subject_id', 'prerequisite_curriculum_subject_id'],
                'curriculum_subject_prerequisite_unique',
            );
        });

        Schema::create('curriculum_subject_corequisites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_subject_id')
                ->constrained(
                    'curriculum_subjects',
                    'id',
                    'curr_subj_coreq_subject_fk',
                )
                ->cascadeOnDelete();
            $table->foreignId('corequisite_curriculum_subject_id')
                ->constrained(
                    'curriculum_subjects',
                    'id',
                    'curr_subj_coreq_target_fk',
                )
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(
                ['curriculum_subject_id', 'corequisite_curriculum_subject_id'],
                'curriculum_subject_corequisite_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_subject_corequisites');
        Schema::dropIfExists('curriculum_subject_prerequisites');

        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->dropUnique('curriculum_subject_assignment_unique');
            $table->dropIndex('curriculum_subject_group_order_index');
            $table->dropColumn([
                'units',
                'lecture_hours',
                'laboratory_hours',
                'remarks',
            ]);
        });

        Schema::table('curricula', function (Blueprint $table) {
            $table->dropColumn('number_of_years');
            $table->dropConstrainedForeignId('academic_term_structure_id');
            $table->dropConstrainedForeignId('program_id');
        });
    }
};
