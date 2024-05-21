<?php

namespace App\DTO;

class RoleCollectionDTO
{
    public $roles;
    public $total;

    public function __construct($roles, $total)
    {
        $this->roles = $roles;
        $this->total = $total;
    }
}
