<?php

namespace App\Services;

use App\Repositories\PermissionRepository;

class PermissionService
{
    protected $permission_repository;

    public function __construct()
    {
        $this->permission_repository = new PermissionRepository;
    }

    public function createPermission($data)
    {
        return $this->permission_repository->createPermission($data);
    }

    public function createRole($data)
    {
        return $this->permission_repository->createRole($data);
    }
    public function findRole($id)
    {
        return $this->permission_repository->findRole($id);
    }
    public function updateRole($data,$id)
    {
        return $this->permission_repository->updateRole($data,$id);

    }
    public function deleteRole($id)
    {
        return $this->permission_repository->deleteRole($id);
    }

    public function deletePermission($id)
    {
        return $this->permission_repository->deletePermission($id);
    }

    public function getRoles()
    {
        return $this->permission_repository->getRoles();
    }
    public function findPermission($permission)
    {
        return $this->permission_repository->findPermission($permission);
    }
    public function getPermissions()
    {
        return $this->permission_repository->getPermissions();
    }

    public function updatePermission($data,$id)
    {
        return $this->permission_repository->updatePermission($data,$id);

    }

    public function viewAssignedPermissions()
    {
        return $this->permission_repository->viewAssignedPermissions();
    }

    public function viewClientAssignedPermissions($id)
    {
        return $this->permission_repository->viewClientAssignedPermissions($id);
    }
    

}
