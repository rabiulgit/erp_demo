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
        Schema::create('lead_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->string('created_by');
            $table->string('cad')->nullable();
            $table->string('boq')->nullable();
            $table->string('description')->nullable();
            $table->string('price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_prices');
    }
};
