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
        if (! Schema::hasTable('gas_details') || Schema::hasColumn('gas_details', 'staff_id')) {
            return;
        }

        Schema::table('gas_details', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->after('date')->constrained('staff')->nullOnDelete();
            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('gas_details') || ! Schema::hasColumn('gas_details', 'staff_id')) {
            return;
        }

        Schema::table('gas_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('staff_id');
        });
    }
};
