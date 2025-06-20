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
        Schema::table('all_tables', function (Blueprint $table) {

            Schema::table('suppliers', function (Blueprint $table) {
            $table->timestamps();
            });
            Schema::table('deliveries', function (Blueprint $table) {
                $table->timestamps();
            });
            Schema::table('customers', function (Blueprint $table) {
                $table->timestamps();
            });
            Schema::table('inventories', function (Blueprint $table) {
                $table->timestamps();
            });
            Schema::table('sales', function (Blueprint $table) {
            $table->timestamps();
            });

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_tables', function (Blueprint $table) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropTimestamps();
            });
            Schema::table('deliveries', function (Blueprint $table) {
                $table->dropTimestamps();
            });
            Schema::table('customers', function (Blueprint $table) {
                $table->dropTimestamps();
            });
            Schema::table('inventories', function (Blueprint $table) {
                $table->dropTimestamps();
            });
            Schema::table('sales', function (Blueprint $table) {
                $table->dropTimestamps();
            });
        });
    }
};
