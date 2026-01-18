<?php

namespace App\Repositories;

use App\Models\Permission;
use App\Repositories\Interfaces\PermissionRepositoryInterface;
use App\DTOS\PermissionDtos\CreatePermissionDto;
use App\DTOS\PermissionDtos\UpdatePermissionDto;

class PermissionRepository implements PermissionRepositoryInterface
{
    public function getPermissionById(int $id): Permission
    {
        return Permission::findOrFail($id);
    }

    public function getPermissionByName(string $name): Permission
    {
        return Permission::where('name', $name)->firstOrFail();
    }

    public function getPermissionByRoles(array $roles): array
    {
        return Permission::with('roles')
            ->whereHas('roles', fn($query) => $query->whereIn('name', $roles))
            ->firstOrFail()
            ->toArray();
    }

    public function createPermission(CreatePermissionDto $data): Permission
    {
        return Permission::create($data->toArray());
    }

    public function updatePermission(int $id, UpdatePermissionDto $data): Permission
    {
        $permission = Permission::findOrFail($id);
        $permission->update($data->toArray());
        return $permission->refresh();
    }

    public function deletePermission(int $id): void
    {
        Permission::findOrFail($id)->delete();
    }

    public function restorePermission(int $id): void
    {
        Permission::findOrFail($id)->restore();
    }

    public function getPermissions(): array
    {
        return Permission::all()->toArray();
    }

    public function getPermissionsWithRoles(array $roles): array
    {
        return Permission::with('roles')
            ->whereHas('roles', fn($query) => $query->whereIn('name', $roles))
            ->get()
            ->toArray();
    }
}
