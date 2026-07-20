<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CustomerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        User::updateOrCreate(
            ['email' => 'admin@tokoemas.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Create Dummy Customer
        $customer = User::updateOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'name' => 'Budi Pelanggan',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        // Create Customer Profile
        CustomerProfile::updateOrCreate(
            ['user_id' => $customer->id],
            [
                'phone' => '089876543210',
                'address' => 'Jl. Pelanggan Setia No 2',
                'customer_since' => now(),
                'segment' => 'new',
            ]
        );
    }
}
