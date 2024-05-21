<?php

namespace App\DTO;

class UserAndRoleDTO
{
    public $user_id;
    public $role_id;

    public function __construct($data)
    {
        $this->user_id = $data['user_id'];
        $this->role_id = $data['role_id'];
    }
}
