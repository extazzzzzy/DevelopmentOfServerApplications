<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function serverInfo()
    {
        $phpInfo = phpversion();
        return response()->json(['php_info' => $phpInfo]);
    }

    public function  clientInfo(Request $request)
    {
        $ip = $request -> ip();
        $useragent = $request->header('User-Agent');
        return response()->json(['ip' => $ip, 'useragent' => $useragent]);
    }

    public function databaseInfo()
    {
        $databaseInfo = config('database.connections.' . config('database.default'));
        return response()->json(['database_info' => $databaseInfo]);
    }
}
