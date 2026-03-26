<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->decimal('qc_verified_boxes', 12, 2)->nullable()->after('quantity');
            $table->decimal('qc_actual_weight', 12, 2)->nullable()->after('qc_verified_boxes');
            $table->decimal('qc_temperature', 5, 2)->nullable()->after('storage_temperature');
            $table->boolean('qc_documents_verified')->default(false)->after('documents_path');
            $table->string('qc_decision')->nullable()->after('qc_documents_verified');
            $table->text('qc_notes')->nullable()->after('qc_decision');
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropColumn([
                'qc_verified_boxes',
                'qc_actual_weight',
                'qc_temperature',
                'qc_documents_verified',
                'qc_decision',
                'qc_notes',
            ]);
        });
    }
};
