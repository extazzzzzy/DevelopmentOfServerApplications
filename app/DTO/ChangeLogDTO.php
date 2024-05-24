<?php

namespace App\DTO;

class ChangeLogDTO
{
    public $entity;
    public $entity_id;
    public $property;
    public $old_value;
    public $new_value;
    public $created_at;

    public function __construct($log)
    {
        $this->entity = $log->entity;
        $this->entity_id = $log->entity_id;
        $this->property = $log->property;
        $this->created_at = $log->created_at;

        $this->old_value = $this->getChangedProperties($log->old_value, $log->new_value);
        $this->new_value = $this->getChangedProperties($log->new_value, $log->old_value);
    }

    private function getChangedProperties($new, $old)
    {
        if ($old === null && is_array($new))
        {
            return $new;
        }
        elseif (is_array($new) && is_array($old))
        {
            return array_filter($new, function ($key) use ($new, $old) {
                return array_key_exists($key, $old) && $new[$key] !== $old[$key];
            }, ARRAY_FILTER_USE_KEY);
        }
        else
        {
            return null;
        }
    }
}
