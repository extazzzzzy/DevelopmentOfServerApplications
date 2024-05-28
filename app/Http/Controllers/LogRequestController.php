<?php

namespace App\Http\Controllers;

use App\DTO\LogRequestCollectionDTO;
use App\DTO\LogRequestDTO;
use App\Models\LogRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LogRequestController extends Controller
{
    public function getCollectionLogsRequests(Request $request)
    {
        $this->clearOldLogs();

        $logs = LogRequest::query();

        if ($request->has('filter')) {
            $filter = $request->filter;
            $logs->where($filter['key'], $filter['value']);
        }

        if ($request->has('sortBy')) {
            $sort = $request->sortBy;
            $logs->orderBy($sort['key'], $sort['order']);
        }

        $count = $request->input('count', 10);
        $logs = $logs->paginate($count);

        $logs->getCollection()->transform(function ($log) {
            return [
                'url' => $log->url,
                'controller' => $log->controller,
                'controller_method' => $log->controller_method,
                'response_status' => $log->response_status,
                'called_at' => $log->called_at,
            ];
        });

        return response()->json($logs);
    }



    public function getLogRequest($id)
    {
        $this->clearOldLogs();

        $log = LogRequest::findOrFail($id);
        $logDTO = new LogRequestDTO($log->toArray());

        return response()->json($logDTO);
    }

    public function deleteLogRequest($id)
    {
        $this->clearOldLogs();

        $log = LogRequest::findOrFail($id);
        $log->forceDelete();

        return response()->json(['message' => 'Удаление выполнено успешно']);
    }

    public function clearOldLogs()
    {
        $limit = Carbon::now()->subHours(73);
        LogRequest::where('created_at', '<', $limit)->delete();
    }
}
