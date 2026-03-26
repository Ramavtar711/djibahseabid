<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settlements')) {
            return;
        }

        Schema::table('settlements', function (Blueprint $table) {
            if (! Schema::hasColumn('settlements', 'manual_payment_sender')) {
                $table->string('manual_payment_sender')->nullable()->after('payment_reference');
            }
            if (! Schema::hasColumn('settlements', 'manual_payment_account')) {
                $table->string('manual_payment_account')->nullable()->after('manual_payment_sender');
            }
            if (! Schema::hasColumn('settlements', 'manual_payment_note')) {
                $table->text('manual_payment_note')->nullable()->after('manual_payment_account');
            }
            if (! Schema::hasColumn('settlements', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('manual_payment_note');
            }
        });

        DB::statement("ALTER TABLE settlements MODIFY status ENUM('pending', 'processing', 'paid', 'failed') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (! Schema::hasTable('settlements')) {
            return;
        }

        Schema::table('settlements', function (Blueprint $table) {
            if (Schema::hasColumn('settlements', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
            if (Schema::hasColumn('settlements', 'manual_payment_note')) {
                $table->dropColumn('manual_payment_note');
            }
            if (Schema::hasColumn('settlements', 'manual_payment_account')) {
                $table->dropColumn('manual_payment_account');
            }
            if (Schema::hasColumn('settlements', 'manual_payment_sender')) {
                $table->dropColumn('manual_payment_sender');
            }
        });

        DB::statement("ALTER TABLE settlements MODIFY status ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending'");
    }
};
