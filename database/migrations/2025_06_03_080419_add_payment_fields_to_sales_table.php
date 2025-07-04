<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
              $table->decimal('amount_paid', 10, 2)->default(0);
        });

        // Use raw SQL to change the column to enum
     DB::statement("ALTER TABLE sales ADD CONSTRAINT chk_payment_status CHECK (payment_status IN ('Paid', 'Partially Paid', 'Unpaid'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
             // Revert enum to text if needed
            $table->text('payment_status')->change();
            // Remove the `amount_paid` column
            $table->dropColumn('amount_paid');
            
            // Drop the computed column
            $table->dropColumn('balance_due');
        });
    }
};
