<?php

namespace App\DTO;

class UserCollectionDTO
{
    public $users;
    public $total;

    public function __construct($users, $total)
    {
        $this->users = $users;
        $this->total = $total;
    }
}
