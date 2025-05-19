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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->foreign(['book_id'], 'fk_deliveries_books')->references(['id'])->on('books')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['supplier_id'], 'fk_deliveries_suppliers')->references(['id'])->on('suppliers')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropForeign('fk_deliveries_books');
            $table->dropForeign('fk_deliveries_suppliers');
        });
    }
};
