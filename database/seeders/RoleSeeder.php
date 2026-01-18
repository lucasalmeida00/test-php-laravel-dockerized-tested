<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'default' => 'Default',
            'shopmanager' => 'Shop Manager',
        ];

        foreach ($roles as $key => $role) {
            Role::firstOrCreate([
                'name' => $key,
                'description' => $role,
            ]);
        }
    }
}
