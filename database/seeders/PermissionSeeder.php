<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            Permission::PERMISSION_CAN_TRANSFER => 'Can Transfer',
        ];

        foreach ($permissions as $key => $permission) {
            Permission::firstOrCreate([
                'name' => $key,
                'description' => $permission,
            ]);
        }
    }
}
