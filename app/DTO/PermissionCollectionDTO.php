<?php

namespace App\DTO;

class PermissionCollectionDTO
{
    public $permissions;
    public $total;

    public function __construct($permissions, $total)
    {
        $this->permissions = $permissions;
        $this->total = $total;
    }
}
