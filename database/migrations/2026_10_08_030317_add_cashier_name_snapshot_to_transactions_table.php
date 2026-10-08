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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('cashier_name')->nullable()->after('user_id');
        });

        DB::table('transactions')
            ->whereNotNull('user_id')
            ->update([
                'cashier_name' => DB::raw('(SELECT name FROM users WHERE users.id = transactions.user_id)'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('cashier_name');
        });
    }
};
