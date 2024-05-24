<?php

namespace App\Http\Controllers;

use App\DTO\ChangeLogCollectionDTO;
use App\Models\ChangeLog;
use Illuminate\Http\Request;

class ChangeLogController extends Controller
{
    public function getCollectionLogs()
    {
        $logs = ChangeLog::all();
        return response()->json(new ChangeLogCollectionDTO($logs));
    }

    public function getUserLogs($id)
    {
        $logs = ChangeLog::where('entity', 'App\Models\User')->where('entity_id', $id)->get();
        return response()->json(new ChangeLogCollectionDTO($logs));
    }

    public function getRoleLogs($id)
    {
        $logs = ChangeLog::where('entity', 'App\Models\Role')->where('entity_id', $id)->get();
        return response()->json(new ChangeLogCollectionDTO($logs));
    }

    public function getPermissionLogs($id)
    {
        $logs = ChangeLog::where('entity', 'App\Models\Permission')->where('entity_id', $id)->get();
        return response()->json(new ChangeLogCollectionDTO($logs));
    }
}
