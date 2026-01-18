<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\Interfaces\RoleRepositoryInterface;
use App\DTOS\RoleDtos\CreateRoleDto;
use App\DTOS\RoleDtos\UpdateRoleDto;
use Illuminate\Support\Collection;

class RoleRepository implements RoleRepositoryInterface
{
    public function getRoleById(int $id): Role
    {
        return Role::findOrFail($id);
    }

    public function getRoleByName(string $name): Role
    {
        return Role::where('name', $name)->firstOrFail()->toArray();
    }

    public function getRoleByPermissions(array $permissions): array
    {
        return Role::with('permissions')
            ->whereHas('permissions', fn($query) => $query->whereIn('name', $permissions))
            ->firstOrFail()->toArray();
    }

    public function createRole(CreateRoleDto $data): Role
    {
        return Role::create($data->toArray());
    }

    public function updateRole(int $id, UpdateRoleDto $data): Role
    {
        $role = Role::findOrFail($id);
        $role->update($data->toArray());
        return $role->refresh()->toArray();
    }

    public function deleteRole(int $id): void
    {
        Role::findOrFail($id)->delete();
    }

    public function restoreRole(int $id): void
    {
        Role::findOrFail($id)->restore();
    }

    public function getRoles(): array
    {
        return Role::all()->toArray();
    }

    public function getRolesWithPermissions(array $permissions): array
    {
        return Role::with('permissions')
            ->whereHas('permissions', fn($query) => $query->whereIn('name', $permissions))
            ->get()
            ->toArray();
    }
}
