<?php

namespace Database\Seeders;

use App\Models\Permission;
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
            $role = Role::firstOrCreate([
                'name' => $key,
                'description' => $role,
            ]);

            if($key === Role::ROLE_DEFAULT)
                $role->permissions()->attach(Permission::where('name', Permission::PERMISSION_CAN_TRANSFER)->first());
        }
    }
}
