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
        Schema::create('pf_deposits', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('own_pf_type');
            $table->string('own_pf_value');
            $table->string('own_pf');
            $table->string('organization_pf_type');
            $table->string('organization_pf_value');
            $table->string('organization_pf');
            $table->string('total_pf');
            $table->string('provident_month');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pf_deposits');
    }
};
