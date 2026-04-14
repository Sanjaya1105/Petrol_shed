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
        Schema::table('bill', function (Blueprint $table) {
            $table->unique(['company_id', 'date', 'invoice_number'], 'bill_company_date_invoice_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill', function (Blueprint $table) {
            $table->dropUnique('bill_company_date_invoice_unique');
        });
    }
};
