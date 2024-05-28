<?php

namespace App\DTO;

class LogRequestCollectionDTO
{
    public $logs;

    public function __construct($logs)
    {
        $this->logs = $logs->map(function ($log) {
            return [
                'url' => $log['url'],
                'controller' => $log['controller'],
                'controller_method' => $log['controller_method'],
                'response_status' => $log['response_status'],
                'called_at' => $log['called_at'],
            ];
        });
    }
}
