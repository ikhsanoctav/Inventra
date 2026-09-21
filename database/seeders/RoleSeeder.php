<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'manager' => 'Manager',
            'admin_gudang' => 'Admin Gudang',
            'purchasing' => 'Purchasing',
            'kasir' => 'Kasir',
        ];

        foreach ($roles as $role => $name) {
            User::updateOrCreate(
                ['email' => $role.'@inventra.go.id'],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => $role,
                ]
            );
        }
    }
}
