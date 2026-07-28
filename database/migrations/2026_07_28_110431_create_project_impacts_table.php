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
        Schema::create('project_impacts', function (Blueprint $table) {
            $table->id();
            $table->string('source_sheet')->index();
            $table->string('record_number')->nullable();
            $table->text('project_title');
            $table->text('proponent')->nullable();
            $table->string('classification')->nullable()->index();
            $table->string('sub_classification')->nullable();
            $table->string('ip_type')->nullable();
            $table->string('field_of_technology')->nullable();
            $table->text('program_intervention')->nullable();
            $table->decimal('amount_assistance', 15, 2)->nullable();
            $table->date('date_assistance')->nullable();
            $table->string('project_status')->nullable()->index();
            $table->date('date_completed')->nullable();
            $table->string('readiness_before')->nullable();
            $table->string('readiness_after')->nullable();
            $table->text('other_interventions')->nullable();
            $table->text('revenue_amount')->nullable();
            $table->text('technology_commercialized')->nullable();
            $table->text('jobs_created')->nullable();
            $table->text('investment_leveraged')->nullable();
            $table->text('efficiency_improved')->nullable();
            $table->text('communities_served')->nullable();
            $table->text('priority_sectors_benefited')->nullable();
            $table->text('ip_assets_utilized')->nullable();
            $table->text('spin_offs_formed')->nullable();
            $table->text('human_capital_developed')->nullable();
            $table->text('other_impacts')->nullable();
            $table->longText('impact_narrative')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamps();

            $table->index(['source_sheet', 'record_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_impacts');
    }
};
