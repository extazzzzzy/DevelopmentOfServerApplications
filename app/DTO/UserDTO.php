<?php

namespace App\DTO;

class UserDTO
{
    public $username;
    public $email;
    public $password;
    public $birthday;

    public function __construct($data)
    {
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->birthday = $data['birthday'];
    }
}
