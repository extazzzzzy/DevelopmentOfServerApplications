<?php

namespace App\DTO;

class RoleDTO
{
    public $name;
    public $description;
    public $cipher;

    public function __construct($name, $description, $cipher)
    {
        $this->name = $name;
        $this->description = $description;
        $this->cipher = $cipher;
    }
}
