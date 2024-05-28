<?php

namespace App\DTO;

class LogRequestCollectionDTO
{
    public $logs;

    public function __construct($logs)
    {
        $this->logs = array_map(function($log) {
            return new LogRequestDTO($log);
        }, $logs->toArray());
    }
}
