<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('school_code')->unique();
            $table->string('school_name');
            $table->string('school_address')->nullable();
            $table->string('school_email')->nullable();
            $table->string('school_contact_number')->nullable();
            $table->string('school_logo')->nullable();
            $table->string('school_favicon')->nullable();
            $table->string('school_motto')->nullable();
            $table->string('school_website')->nullable();
            $table->string('school_director')->nullable();
            $table->timestamps();
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
