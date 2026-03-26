<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settlements')) {
            return;
        }

        Schema::table('settlements', function (Blueprint $table) {
            if (! Schema::hasColumn('settlements', 'payment_provider')) {
                $table->string('payment_provider')->nullable()->after('status');
            }
            if (! Schema::hasColumn('settlements', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_provider');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('settlements')) {
            return;
        }

        Schema::table('settlements', function (Blueprint $table) {
            if (Schema::hasColumn('settlements', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
            if (Schema::hasColumn('settlements', 'payment_provider')) {
                $table->dropColumn('payment_provider');
            }
        });
    }
};
