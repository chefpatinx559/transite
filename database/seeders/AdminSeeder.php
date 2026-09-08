<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'first_name' => 'Elysée',
                'last_name'  => 'Ouattara',
                'email'      => 'admin@netspring.ci',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
            ],
            [
                'first_name' => 'Kassamba Ben Idriss',
                'last_name'  => 'Diaby',
                'email'      => 'kassambaben75@gmail.com',
                'password'   => Hash::make('NetSpring@2026!'),
                'role'       => 'super_admin',
            ],
        ];

        foreach ($admins as $admin) {
            User::updateOrCreate(
                ['email' => $admin['email']],
                $admin,
            );
        }

        // S'assurer qu'il n'y a que ces 2 comptes admin/super_admin
        User::whereIn('role', ['admin', 'super_admin'])
            ->whereNotIn('email', array_column($admins, 'email'))
            ->delete();
    }
}
