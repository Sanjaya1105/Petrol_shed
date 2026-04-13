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
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign('sales_pump_id_foreign');
            $table->dropUnique('sales_pump_id_unique');
            $table->index('pump_id', 'sales_pump_id_index');
            $table->unique(['pump_id', 'date'], 'sales_pump_id_date_unique');
            $table->foreign('pump_id', 'sales_pump_id_foreign')->references('id')->on('pumps');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign('sales_pump_id_foreign');
            $table->dropUnique('sales_pump_id_date_unique');
            $table->dropIndex('sales_pump_id_index');
            $table->unique('pump_id');
            $table->foreign('pump_id', 'sales_pump_id_foreign')->references('id')->on('pumps');
        });
    }
};
