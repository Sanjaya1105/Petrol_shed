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
        if (! Schema::hasTable('bill')) {
            return;
        }

        DB::statement('ALTER TABLE `bill` MODIFY `liters` DECIMAL(12,4) NOT NULL');
        DB::statement('ALTER TABLE `bill` MODIFY `bill_value` DECIMAL(12,4) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('bill')) {
            return;
        }

        DB::statement('ALTER TABLE `bill` MODIFY `liters` DECIMAL(12,2) NOT NULL');
        DB::statement('ALTER TABLE `bill` MODIFY `bill_value` DECIMAL(12,2) NOT NULL');
    }
};
