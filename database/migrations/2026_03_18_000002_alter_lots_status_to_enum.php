<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('lots')) {
            return;
        }

        DB::statement(
            "ALTER TABLE lots MODIFY status ENUM(
                'draft',
                'pending qc',
                'approved',
                'rejected',
                'needs modification',
                'scheduled auction',
                'active auction',
                'active',
                'sold'
            ) NOT NULL DEFAULT 'draft'"
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('lots')) {
            return;
        }

        DB::statement("ALTER TABLE lots MODIFY status VARCHAR(255) NOT NULL DEFAULT 'draft'");
    }
};
