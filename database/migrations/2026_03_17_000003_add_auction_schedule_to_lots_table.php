<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dateTime('auction_start_at')->nullable()->after('qc_notes');
            $table->dateTime('auction_end_at')->nullable()->after('auction_start_at');
            $table->unsignedInteger('auction_duration_minutes')->nullable()->after('auction_end_at');
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn([
                'auction_start_at',
                'auction_end_at',
                'auction_duration_minutes',
            ]);
        });
    }
};
