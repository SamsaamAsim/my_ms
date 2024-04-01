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
        Schema::create('market_research', function (Blueprint $table) {
            $table->id();

            $table->integer('Last_30_day');
            $table->integer('Last_90_day');
            $table->integer('Last_6_month');
            $table->integer('Last_1_year');
            $table->integer('Last_2_year');
            $table->integer('Last_3_year');
            $table->integer('total_seller_30_day');
            $table->integer('top_seller_30_day');
            $table->integer('2nd_seller_30_day');
            $table->integer('total_seller_3_year');
            $table->integer('top_seller_3_year');
            $table->integer('1_month_sale');
            $table->integer('2_month_sale');
            $table->integer('3_month_sale');
            $table->integer('6_month_sale');
            $table->integer('1_year_sale');
            $table->integer('2_year_sale');;





            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_research');
    }
};
