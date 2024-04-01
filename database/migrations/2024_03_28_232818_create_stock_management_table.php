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
        Schema::create('stock_management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('available_stock')->nullable();
            
            $table->string('dropdown')->nullable();
            $table->integer('minimum_stock')->nullable();
            $table->integer('maximum_stock')->nullable();
            $table->integer('spare_stock')->nullable();
            $table->integer('minimum_stock_required')->nullable();
            $table->integer('maximum_stock_required')->nullable();
            $table->integer('our_stock')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_management');
    }
};
