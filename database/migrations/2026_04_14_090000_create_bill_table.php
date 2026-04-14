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
        Schema::create('bill', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff');
            $table->date('date');
            $table->foreignId('company_id')->constrained('company');
            $table->string('invoice_number');
            $table->foreignId('category_id')->constrained('category');
            $table->decimal('price', 12, 2);
            $table->decimal('liters', 12, 2);
            $table->decimal('bill_value', 12, 2);
            $table->timestamps();

            $table->index('date');
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill');
    }
};
