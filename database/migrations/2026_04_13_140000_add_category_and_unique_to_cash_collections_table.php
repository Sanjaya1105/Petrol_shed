<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('cash_collections', 'category')) {
            Schema::table('cash_collections', function (Blueprint $table) {
                $table->string('category')->default('cash')->after('date');
            });
        }

        try {
            DB::statement('ALTER TABLE cash_collections DROP INDEX cash_collections_staff_id_date_unique');
        } catch (\Throwable) {
            // Old unique index may already be removed.
        }

        try {
            DB::statement('ALTER TABLE cash_collections ADD UNIQUE cash_collections_staff_id_date_category_unique (staff_id, date, category)');
        } catch (\Throwable) {
            // Composite unique may already exist.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE cash_collections DROP INDEX cash_collections_staff_id_date_category_unique');
        } catch (\Throwable) {
            // Composite unique may already be removed.
        }

        try {
            DB::statement('ALTER TABLE cash_collections ADD UNIQUE cash_collections_staff_id_date_unique (staff_id, date)');
        } catch (\Throwable) {
            // Legacy unique may already exist.
        }

        if (Schema::hasColumn('cash_collections', 'category')) {
            Schema::table('cash_collections', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
