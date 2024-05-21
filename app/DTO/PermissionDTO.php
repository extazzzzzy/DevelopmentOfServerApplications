<?php

namespace App\DTO;

class PermissionDTO
{
    public $name;
    public $description;

    public function __construct($data)
    {
        $this->name = $data['name'];
        $this->description = $data['description'];
    }
}
