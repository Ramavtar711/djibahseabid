<?php

namespace Database\Seeders;

use App\Models\Lot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sellerId = DB::table('users')->where('type', 'seller')->value('id');

        if (! $sellerId) {
            $sellerId = DB::table('users')->insertGetId([
                'name' => 'Demo Seller',
                'email' => 'seller@example.com',
                'password' => Hash::make('password'),
                'type' => 'seller',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $now = Carbon::now();

        Lot::upsert([
            [
                'seller_id' => $sellerId,
                'title' => 'Yellowfin Tuna Batch A',
                'species' => 'Tuna',
                'quantity' => 150,
                'starting_price' => 5.25,
                'harvest_date' => $now->copy()->subDays(2)->toDateString(),
                'storage_temperature' => '-18°C',
                'notes' => 'Fresh catch from the Atlantic, packed in ice.',
                'status' => 'draft',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'seller_id' => $sellerId,
                'title' => 'Organic Salmon Lot',
                'species' => 'Salmon',
                'quantity' => 85,
                'starting_price' => 6.5,
                'harvest_date' => $now->copy()->subDays(5)->toDateString(),
                'storage_temperature' => '-12°C',
                'notes' => 'Certified organic and HACCP compliant.',
                'status' => 'pending qc',
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ], ['title'], [
            'species',
            'quantity',
            'starting_price',
            'harvest_date',
            'storage_temperature',
            'notes',
            'status',
            'updated_at',
        ]);
    }
}
