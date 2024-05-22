<?php

namespace App\DTO;

class RoleAndPermissionDTO
{
    public $role_id;
    public $permission_id;

    public function __construct($data)
    {
        $this->role_id = $data['role_id'];
        $this->permission_id = $data['permission_id'];
    }
}
