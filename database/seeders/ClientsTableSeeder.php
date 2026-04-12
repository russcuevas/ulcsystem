<?php

namespace Database\Seeders;

use App\Models\Clients;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Clients::create([
            'fullname' => 'Juan Dela Cruz',
            'phone' => '09918983072',
            'address' => 'Manila, Philippines',
            'area_id' => 1,
            'gender' => 'male',
            'created_by' => 'admin',
        ]);

        Clients::create([
            'fullname' => 'Russel Vincent Cuevas',
            'phone' => '09495748302',
            'address' => 'Manila, Philippines',
            'area_id' => 1,
            'gender' => 'male',
            'created_by' => 'admin',
        ]);

        Clients::create([
            'fullname' => 'Maria Santos',
            'phone' => '09987654321',
            'address' => 'Quezon City',
            'area_id' => 11,
            'gender' => 'female',
            'created_by' => 'admin',
        ]);
    }
}
