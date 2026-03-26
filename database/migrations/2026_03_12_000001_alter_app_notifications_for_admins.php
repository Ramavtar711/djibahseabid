<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('app_notifications')) {
            return;
        }

        if (Schema::hasColumn('app_notifications', 'user_id')) {
            $foreignKey = DB::selectOne(
                "SELECT CONSTRAINT_NAME as name
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = 'app_notifications'
                   AND COLUMN_NAME = 'user_id'
                   AND REFERENCED_TABLE_NAME IS NOT NULL
                 LIMIT 1"
            );

            if ($foreignKey && isset($foreignKey->name)) {
                DB::statement("ALTER TABLE app_notifications DROP FOREIGN KEY {$foreignKey->name}");
            }
            Schema::table('app_notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
            Schema::table('app_notifications', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        Schema::table('app_notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('app_notifications', 'admin_id')) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('user_id')->index();
            }
        });

        if (Schema::hasTable('admins') && Schema::hasColumn('app_notifications', 'admin_id')) {
            Schema::table('app_notifications', function (Blueprint $table) {
                $table->foreign('admin_id')->references('id')->on('admins')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('app_notifications')) {
            return;
        }

        if (Schema::hasColumn('app_notifications', 'admin_id')) {
            Schema::table('app_notifications', function (Blueprint $table) {
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
            });
        }

        if (Schema::hasColumn('app_notifications', 'user_id')) {
            Schema::table('app_notifications', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });

            Schema::table('app_notifications', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable(false)->change();
            });

            Schema::table('app_notifications', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }
};
