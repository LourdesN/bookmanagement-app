<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Create ENUM type in PostgreSQL if it does not exist
            DB::statement("DO $$
            BEGIN
                IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'payment_status_enum') THEN
                    CREATE TYPE payment_status_enum AS ENUM ('Unpaid', 'Partially Paid', 'Paid');
                END IF;
            END$$;");

            // Change column type to ENUM
            DB::statement("ALTER TABLE sales 
                ALTER COLUMN payment_status TYPE payment_status_enum 
                USING payment_status::text::payment_status_enum;");
        } else {
            // For MySQL: just ensure varchar with default
            Schema::table('sales', function (Blueprint $table) {
                $table->enum('payment_status', ['Unpaid', 'Partially Paid', 'Paid'])
                      ->default('Unpaid')
                      ->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // Revert ENUM back to VARCHAR
            DB::statement("ALTER TABLE sales 
                ALTER COLUMN payment_status TYPE VARCHAR(50);");

            // Drop ENUM type if needed
            DB::statement("DROP TYPE IF EXISTS payment_status_enum;");
        } else {
            Schema::table('sales', function (Blueprint $table) {
                $table->string('payment_status', 50)->change();
            });
        }
    }
};
