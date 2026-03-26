<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('company_legal_name');
            }
            if (! Schema::hasColumn('users', 'landing_site_port')) {
                $table->string('landing_site_port')->nullable()->after('company_name');
            }
            if (! Schema::hasColumn('users', 'address')) {
                $table->string('address', 500)->nullable()->after('landing_site_port');
            }
            if (! Schema::hasColumn('users', 'supply_type')) {
                $table->json('supply_type')->nullable()->after('address');
            }
            if (! Schema::hasColumn('users', 'processing_status')) {
                $table->json('processing_status')->nullable()->after('supply_type');
            }
            if (! Schema::hasColumn('users', 'estimated_weekly_volume')) {
                $table->string('estimated_weekly_volume')->nullable()->after('processing_status');
            }
            if (! Schema::hasColumn('users', 'trade_license_file')) {
                $table->string('trade_license_file')->nullable()->after('estimated_weekly_volume');
            }
            if (! Schema::hasColumn('users', 'facility_photos_file')) {
                $table->string('facility_photos_file')->nullable()->after('trade_license_file');
            }
            if (! Schema::hasColumn('users', 'certificates_file')) {
                $table->string('certificates_file')->nullable()->after('facility_photos_file');
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'company_name',
            'landing_site_port',
            'address',
            'supply_type',
            'processing_status',
            'estimated_weekly_volume',
            'trade_license_file',
            'facility_photos_file',
            'certificates_file',
        ];

        $existingColumns = array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn('users', $column)
        ));

        if (! empty($existingColumns)) {
            Schema::table('users', function (Blueprint $table) use ($existingColumns) {
                $table->dropColumn($existingColumns);
            });
        }
    }
};

