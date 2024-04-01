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
        Schema::create('price_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('buying', 15, 2)->nullable();
            $table->decimal('selling', 15, 2)->nullable();
            $table->decimal('add_rate', 15, 2)->nullable();
            $table->decimal('catigory_rate', 15, 2)->nullable();
            $table->decimal('value_fee', 15, 2)->nullable();
            $table->decimal('postage', 15, 2)->nullable();
            $table->decimal('add_rate_ans', 15, 2)->nullable();
            $table->decimal('add_rate_gst', 15, 2)->nullable();
            $table->decimal('total_add_rate', 15, 2)->nullable();
            $table->decimal('catigory_rate_ans', 15, 2)->nullable();
            $table->decimal('catigory_rate_gst', 15, 2)->nullable();
            $table->decimal('catigory_add_rate', 15, 2)->nullable();
            $table->decimal('ebay_expenses', 15, 2)->nullable();
            $table->decimal('earning_from_ebay', 15, 2)->nullable();
            $table->decimal('gst_on_earning', 15, 2)->nullable();
            $table->decimal('earning_in_hand', 15, 2)->nullable();
            $table->decimal('total_cost', 15, 2)->nullable();
            $table->decimal('profit', 15, 2)->nullable();
            $table->decimal('profit_margin', 15, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_calculations');
    }
};
