<?php

namespace App\DTO;

class UserAndRoleCollectionDTO
{
    public $users_and_roles;
    public $total;

    public function __construct($users_and_roles, $total)
    {
        $this->users_and_roles = $users_and_roles;
        $this->total = $total;
    }
}
