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
        Schema::create('sales', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('book_id')->index('fk_sales_books');
            $table->integer('customer_id')->index('fk_sales_customers');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->integer('total');
            $table->text('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
