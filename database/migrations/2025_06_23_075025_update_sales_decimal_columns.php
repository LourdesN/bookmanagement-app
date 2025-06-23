<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
        
            $table->decimal('total', 10, 2)->change();
          
          
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('total')->change();
           
        });
    }
};

