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
        Schema::create('lead_moms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->string('attendee');
            $table->string('topic');
            $table->string('place_time');
            $table->string('next_plan')->nullable();
            $table->string('interaction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_moms');
    }
};
