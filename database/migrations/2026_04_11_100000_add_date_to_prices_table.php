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
        Schema::table('prices', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->dropUnique(['category_id']);
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->date('date')->default(now()->toDateString());
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('category');
            $table->unique(['category_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prices', function (Blueprint $table) {
            $table->dropUnique(['category_id', 'date']);
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->dropColumn('date');
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->unique('category_id');
            $table->foreign('category_id')->references('id')->on('category');
        });
    }
};
