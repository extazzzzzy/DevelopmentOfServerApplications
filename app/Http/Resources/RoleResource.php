<?php

namespace App\Http\Resources;

class RoleResource
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
