<?php

namespace App\Repositories\Interfaces;

use App\DTOS\RoleDtos\CreateRoleDto;
use App\DTOS\RoleDtos\UpdateRoleDto;
use App\Models\Role;

interface RoleRepositoryInterface
{
    public function getRoleById(int $id): Role;
    public function getRoleByName(string $name): Role;
    public function getRoleByPermissions(array $permissions): array;

    public function createRole(CreateRoleDto $role): Role;
    public function updateRole(int $id, UpdateRoleDto $role): Role;
    public function deleteRole(int $id): void;
    public function restoreRole(int $id): void;

    public function getRoles(): array;
    public function getRolesWithPermissions(array $permissions): array;
}
