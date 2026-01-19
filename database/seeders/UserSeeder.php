<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'cpf' => '12345678900',
                'amount' => 1000,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'cpf' => '12345678901',
                'amount' => 1000,
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $user) {
            $userModel = User::create($user);
            if ($user['email'] === 'john@example.com') {
                $userModel->roles()->attach(Role::where('name', Role::ROLE_SHOPMANAGER)->first());
            }
            if ($user['email'] === 'jane@example.com') {
                $userModel->roles()->attach(Role::where('name', Role::ROLE_DEFAULT)->first());
            }
        }
    }
}
