<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audience_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('audience_segment_lot', function (Blueprint $table) {
            $table->foreignId('audience_segment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            $table->primary(['audience_segment_id', 'lot_id']);
        });

        $segments = collect([
            ['Hotels', 'Prime buyers from hospitality chains'],
            ['Restaurants', 'Chef-owned restaurants'],
            ['Supermarkets', 'Regional supermarkets'],
            ['Catering', 'Event caterers'],
            ['Bulk Importer', 'Large-volume importers'],
            ['Distributor', 'Ocean freight distributors'],
            ['Trader / Reseller', 'Wholesale resellers'],
            ['Processing Co.', 'Value-added processors'],
        ]);

        DB::table('audience_segments')->insert(
            $segments->map(function ($item) {
                return [
                    'name' => $item[0],
                    'slug' => Str::slug($item[0]),
                    'description' => $item[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('audience_segment_lot');
        Schema::dropIfExists('audience_segments');
    }
};
