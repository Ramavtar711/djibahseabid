<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'company_legal_name')) {
                $table->string('company_legal_name')->nullable()->after('job_title');
            }
            if (! Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('company_legal_name');
            }
            if (! Schema::hasColumn('users', 'business_address')) {
                $table->string('business_address', 500)->nullable()->after('city');
            }
            if (! Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('business_address');
            }
            if (! Schema::hasColumn('users', 'website')) {
                $table->string('website')->nullable()->after('country');
            }
            if (! Schema::hasColumn('users', 'company_registration_number')) {
                $table->string('company_registration_number')->nullable()->after('website');
            }
            if (! Schema::hasColumn('users', 'business_type')) {
                $table->json('business_type')->nullable()->after('company_registration_number');
            }
            if (! Schema::hasColumn('users', 'interested_in')) {
                $table->json('interested_in')->nullable()->after('business_type');
            }
            if (! Schema::hasColumn('users', 'monthly_volume')) {
                $table->string('monthly_volume')->nullable()->after('interested_in');
            }
            if (! Schema::hasColumn('users', 'preferred_delivery')) {
                $table->json('preferred_delivery')->nullable()->after('monthly_volume');
            }
            if (! Schema::hasColumn('users', 'preferred_payment')) {
                $table->json('preferred_payment')->nullable()->after('preferred_delivery');
            }
            if (! Schema::hasColumn('users', 'bank_country')) {
                $table->string('bank_country')->nullable()->after('preferred_payment');
            }
            if (! Schema::hasColumn('users', 'company_registration_file')) {
                $table->string('company_registration_file')->nullable()->after('bank_country');
            }
            if (! Schema::hasColumn('users', 'id_file')) {
                $table->string('id_file')->nullable()->after('company_registration_file');
            }
            if (! Schema::hasColumn('users', 'import_license_file')) {
                $table->string('import_license_file')->nullable()->after('id_file');
            }
            if (! Schema::hasColumn('users', 'is_registered_business')) {
                $table->boolean('is_registered_business')->default(false)->after('import_license_file');
            }
            if (! Schema::hasColumn('users', 'accepted_terms')) {
                $table->boolean('accepted_terms')->default(false)->after('is_registered_business');
            }
            if (! Schema::hasColumn('users', 'bank_transfer_validated')) {
                $table->boolean('bank_transfer_validated')->default(false)->after('accepted_terms');
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'phone',
            'job_title',
            'company_legal_name',
            'city',
            'business_address',
            'country',
            'website',
            'company_registration_number',
            'business_type',
            'interested_in',
            'monthly_volume',
            'preferred_delivery',
            'preferred_payment',
            'bank_country',
            'company_registration_file',
            'id_file',
            'import_license_file',
            'is_registered_business',
            'accepted_terms',
            'bank_transfer_validated',
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
