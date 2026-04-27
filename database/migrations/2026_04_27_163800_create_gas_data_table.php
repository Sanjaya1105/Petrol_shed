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
        if (! Schema::hasTable('gas_data')) {
            Schema::create('gas_data', function (Blueprint $table) {
                $table->id();
                $table->string('gas_type');
                $table->date('date');
                $table->foreignId('staff_id')->nullable()->constrained('staff')->nullOnDelete();
                $table->decimal('morning_balance', 12, 2)->nullable();
                $table->decimal('night_balance', 12, 2)->nullable();
                $table->decimal('today_sale', 12, 2)->nullable();
                $table->decimal('amount', 12, 2)->nullable();
                $table->timestamps();

                $table->unique(['gas_type', 'date']);
                $table->index('date');
                $table->index('staff_id');
            });
        }

        if (! Schema::hasTable('gas_details')) {
            return;
        }

        $hasStaffId = Schema::hasColumn('gas_details', 'staff_id');

        $rows = DB::table('gas_details')->get();
        foreach ($rows as $row) {
            $payload = [
                'gas_type' => $row->gas_type,
                'date' => $row->date,
                'morning_balance' => $row->morning_balance,
                'night_balance' => $row->night_balance,
                'today_sale' => $row->today_sale,
                'amount' => $row->amount,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ];

            if ($hasStaffId) {
                $payload['staff_id'] = $row->staff_id;
            }

            DB::table('gas_data')->updateOrInsert(
                ['gas_type' => $row->gas_type, 'date' => $row->date],
                $payload
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gas_data');
    }
};
