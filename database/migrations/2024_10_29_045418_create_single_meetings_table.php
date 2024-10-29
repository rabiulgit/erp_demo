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
        Schema::create('single_meetings', function (Blueprint $table) {
            $table->id();
            $table->integer('branch_id');
            $table->string('to_address')->nullable();
            $table->string('title');
            $table->date('date');
            $table->time('time');
            $table->text('note')->nullable();
            $table->integer('created_by');
            $table->foreignId('employee_id')->nullable()->constrained('employees')->onDelete('cascade');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('single_meetings');
    }
};
