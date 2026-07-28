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
        Schema::create('startup_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('program')->index();
            $table->text('project_title');
            $table->text('proponent_name')->nullable();
            $table->text('contact_details')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('class')->default('STARTUP')->index();
            $table->string('status')->nullable()->index();
            $table->text('promotional_assistance')->nullable();
            $table->text('revenue_growth')->nullable();
            $table->text('jobs_created')->nullable();
            $table->text('investments_attracted')->nullable();
            $table->text('market_reach')->nullable();
            $table->text('high_tech_exports')->nullable();
            $table->text('social_impact')->nullable();
            $table->text('next_possible_intervention')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('startup_trackings');
    }
};
