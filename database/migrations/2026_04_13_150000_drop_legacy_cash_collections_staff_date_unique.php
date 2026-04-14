<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE cash_collections DROP INDEX cash_collections_staff_id_date_unique');
        } catch (\Throwable) {
            // Legacy index may already be absent.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE cash_collections ADD UNIQUE cash_collections_staff_id_date_unique (staff_id, date)');
        } catch (\Throwable) {
            // No-op if it already exists or cannot be added.
        }
    }
};
