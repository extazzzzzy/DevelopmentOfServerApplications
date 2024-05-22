<?php

namespace App\DTO;

class UserAndRoleCollectionDTO
{
    public $user_id;
    public $roles;

    public function __construct($user)
    {
        $this->user_id = $user->id;
        $this->roles = $user->roles->map(function ($role) {
            return [
                'role_id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
            ];
        });
    }
}
