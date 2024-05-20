<?php

namespace App\Http\Resources;

class LoginResource
{
    public $username;
    public $password;

    public function __construct($data)
    {
        $this->username = $data['username'];
        $this->password = $data['password'];
    }
}
