<?php

namespace App\DTO;

class ChangeLogDTO
{
    public $entity;
    public $entity_id;
    public $old_value;
    public $new_value;
    public $user_id;
    public $created_at;

    public function __construct($log)
    {
        $this->entity = $log->entity;
        $this->entity_id = $log->entity_id;
        $this->old_value = $log->old_value;
        $this->new_value = $log->new_value;
        $this->user_id = $log->user_id;
        $this->created_at = $log->created_at;
    }
}
