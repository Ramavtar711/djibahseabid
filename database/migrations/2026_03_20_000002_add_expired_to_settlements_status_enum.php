<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE settlements MODIFY status ENUM('pending', 'processing', 'paid', 'failed', 'expired') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE settlements MODIFY status ENUM('pending', 'processing', 'paid', 'failed') NOT NULL DEFAULT 'pending'");
    }
};
