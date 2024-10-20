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
        Schema::create('deal_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->index();
            $table->string('client_name');
            $table->string('email');
            $table->string('phone');
            $table->string('company_code');
            $table->string('address');
            $table->string('informations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_details');
    }
};
