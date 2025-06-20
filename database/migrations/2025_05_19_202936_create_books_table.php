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
     Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->date('print_date');
            $table->decimal('unit_cost', 10, 2); // Assuming price format
            $table->string('isbn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
