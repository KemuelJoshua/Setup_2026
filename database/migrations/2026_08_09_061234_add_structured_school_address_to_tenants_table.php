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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('school_address_line_1')->nullable();
            $table->string('school_address_line_2')->nullable();
            $table->string('school_barangay')->nullable();
            $table->string('school_city_municipality')->nullable();
            $table->string('school_province')->nullable();
            $table->string('school_region')->nullable();
            $table->string('school_postal_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'school_address_line_1',
                'school_address_line_2',
                'school_barangay',
                'school_city_municipality',
                'school_province',
                'school_region',
                'school_postal_code',
            ]);
        });
    }
};
