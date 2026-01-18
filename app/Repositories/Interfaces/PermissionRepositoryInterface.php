<?php

namespace App\Repositories\Interfaces;

use App\DTOS\PermissionDtos\CreatePermissionDto;
use App\DTOS\PermissionDtos\UpdatePermissionDto;
use App\Models\Permission;

interface PermissionRepositoryInterface
{
    public function getPermissionById(int $id): Permission;
    public function getPermissionByName(string $name): Permission;
    public function getPermissionByRoles(array $roles): array;

    public function createPermission(CreatePermissionDto $permission): Permission;
    public function updatePermission(int $id, UpdatePermissionDto $permission): Permission;
    public function deletePermission(int $id): void;
    public function restorePermission(int $id): void;

    public function getPermissions(): array;
}
