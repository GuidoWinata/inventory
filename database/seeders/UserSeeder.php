<?php

namespace Database\Seeders;

use App\Constants\UserConst;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@inventory.test'],
            [
                'name' => 'Admin Inventory',
                'password' => Hash::make('password'),
                'access_type' => UserConst::ADMIN,
                'is_active' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@inventory.test'],
            [
                'name' => 'Staff Gudang',
                'password' => Hash::make('password'),
                'access_type' => UserConst::STAFF,
                'is_active' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@inventory.test'],
            [
                'name' => 'Manager Gudang',
                'password' => Hash::make('password'),
                'access_type' => UserConst::MANAGER,
                'is_active' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'asd@asd.asd'],
            [
                'name' => 'Test User',
                'password' => Hash::make('asdasd'),
                'access_type' => UserConst::ADMIN,
                'is_active' => 1,
            ]
        );
    }
}
