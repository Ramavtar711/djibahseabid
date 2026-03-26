<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lots')) {
            return;
        }

        Schema::table('lots', function (Blueprint $table) {
            if (! Schema::hasColumn('lots', 'winner_id')) {
                $table->unsignedBigInteger('winner_id')->nullable()->after('seller_id');
            }
            if (! Schema::hasColumn('lots', 'final_price')) {
                $table->decimal('final_price', 12, 2)->nullable()->after('starting_price');
            }
            if (! Schema::hasColumn('lots', 'settlement_status')) {
                $table->enum('settlement_status', ['pending', 'paid', 'failed'])->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('lots')) {
            return;
        }

        Schema::table('lots', function (Blueprint $table) {
            if (Schema::hasColumn('lots', 'winner_id')) {
                $table->dropColumn('winner_id');
            }
            if (Schema::hasColumn('lots', 'final_price')) {
                $table->dropColumn('final_price');
            }
            if (Schema::hasColumn('lots', 'settlement_status')) {
                $table->dropColumn('settlement_status');
            }
        });
    }
};
