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
        Schema::create('gas_details', function (Blueprint $table) {
            $table->id();
            $table->string('gas_type');
            $table->date('date');
            $table->decimal('morning_balance', 12, 2)->nullable();
            $table->decimal('night_balance', 12, 2)->nullable();
            $table->decimal('today_sale', 12, 2)->nullable();
            $table->decimal('amount', 12, 2)->nullable();
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
        Schema::dropIfExists('gas_details');
    }
};
