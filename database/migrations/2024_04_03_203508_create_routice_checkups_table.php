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
        Schema::create('routice_checkups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            $table->string('note_h')->nullable();

            $table->string('note_k')->nullable();
            $table->date('checked_on')->nullable();
            $table->date('check_again')->nullable();
            $table->string('resent_update')->nullable(); 
            $table->string('check_again_dropdown')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routice_checkups');
    }
};
