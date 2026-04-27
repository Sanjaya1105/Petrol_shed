<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('sales') || ! Schema::hasColumn('sales', 'meter_amount')) {
            return;
        }

        DB::statement('ALTER TABLE `sales` MODIFY `meter_amount` DECIMAL(12,5) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('sales') || ! Schema::hasColumn('sales', 'meter_amount')) {
            return;
        }

        DB::statement('ALTER TABLE `sales` MODIFY `meter_amount` DECIMAL(12,2) NOT NULL');
    }
};
