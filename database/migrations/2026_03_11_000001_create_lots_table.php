<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('species');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->decimal('starting_price', 12, 2)->default(0);
            $table->date('harvest_date');
            $table->string('storage_temperature')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft');
            $table->string('image_path')->nullable();
            $table->string('health_certificate_path')->nullable();
            $table->string('documents_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
