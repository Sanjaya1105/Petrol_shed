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
        Schema::create('gas_price', function (Blueprint $table) {
            $table->id();
            $table->string('gas_type');
            $table->decimal('price', 12, 2);
            $table->date('date');
            $table->timestamps();

            $table->unique(['gas_type', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_price');
    }
};
