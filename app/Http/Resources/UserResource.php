<?php

namespace App\Http\Resources;

class UserResource
{
    public $id;
    public $username;
    public $email;
    public $birthday;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->birthday = $data['birthday'];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'birthday' => $this->birthday,
        ];
    }
}
