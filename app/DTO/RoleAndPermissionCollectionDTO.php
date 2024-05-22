<?php

namespace App\DTO;

class RoleAndPermissionCollectionDTO
{
    public $roles_and_permissions;
    public $total;

    public function __construct($roles_and_permissions, $total)
    {
        $this->roles_and_permissions = $roles_and_permissions;
        $this->total = $total;
    }
}
