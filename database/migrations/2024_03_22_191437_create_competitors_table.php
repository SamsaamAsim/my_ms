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
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('competitor_selling_price', 15, 2)->nullable();
            $table->decimal('postage', 15, 2)->nullable();
            $table->decimal('total', 15, 2)->nullable();
            $table->decimal('ourless', 15, 2)->nullable();
            $table->decimal('profit_competitors', 15, 2)->nullable();
            $table->decimal('profit_margin_competitors', 15, 2)->nullable();



            $table->string('competitors_name')->nullable();
            $table->string('dropdown')->nullable();
            $table->integer('30_day_sale')->nullable();
            $table->integer('90_day_sale')->nullable();
            $table->integer('6_month_sale')->nullable();
            $table->integer('1_year_sale')->nullable();
            $table->integer('3_year_sale')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitors');
    }
};
