<?php

namespace App\Http\Resources;

class RegisterResource
{
    public $username;
    public $email;
    public $password;
    public $c_password;
    public $birthday;

    public function __construct($data)
    {
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->c_password = $data['c_password'];
        $this->birthday = $data['birthday'];
    }
}
