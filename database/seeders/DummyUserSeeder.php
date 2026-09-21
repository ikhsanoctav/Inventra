<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        User::updateOrCreate(['email' => 'superadmin@inventra.go.id'], [
            'name' => 'Super Admin',
            'password' => $password,
            'role' => 'super_admin',
        ]);

        User::updateOrCreate(['email' => 'manager@inventra.go.id'], [
            'name' => 'Budi Manager',
            'password' => $password,
            'role' => 'manager',
        ]);

        User::updateOrCreate(['email' => 'gudang@inventra.go.id'], [
            'name' => 'Agus Gudang',
            'password' => $password,
            'role' => 'admin_gudang',
        ]);

        User::updateOrCreate(['email' => 'purchasing@inventra.go.id'], [
            'name' => 'Siti Purchasing',
            'password' => $password,
            'role' => 'purchasing',
        ]);

        User::updateOrCreate(['email' => 'kasir@inventra.go.id'], [
            'name' => 'Dewi Kasir',
            'password' => $password,
            'role' => 'kasir',
        ]);
    }
}
