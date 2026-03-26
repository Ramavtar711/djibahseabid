<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->upsert([
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'QC User',
                'email' => 'qc@gmail.com',
                'password' => Hash::make('123456'),
                'role' => 'qc',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['email'], ['name', 'password', 'role', 'updated_at']);
    }
}
