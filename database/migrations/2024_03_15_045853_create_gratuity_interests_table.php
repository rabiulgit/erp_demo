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
        Schema::create('gratuity_interests', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('total_gratuity');
            $table->string('interest_type');
            $table->string('interest_value');
            $table->string('interest_amount');
            $table->string('interest_month');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gratuity_interests');
    }
};
