<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_term_structure_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('academic_periods')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->unsignedInteger('sequence');
            $table->string('status')->default('active')->index();
            $table->timestamps();

            $table->unique(
                ['academic_term_structure_id', 'parent_id', 'name'],
                'academic_periods_scope_name_unique',
            );
            $table->unique(
                ['academic_term_structure_id', 'parent_id', 'sequence'],
                'academic_periods_scope_sequence_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_periods');
    }
};
