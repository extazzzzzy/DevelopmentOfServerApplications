<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitWebhookController extends Controller
{
    private $updating = false;

    public function handle(Request $request)
    {
        $secretKey = $request->input('secret_key');
        $envSecretKey = env('SECRET_KEY');

        if ($secretKey !== $envSecretKey)
        {
            return response()->json(['error' => 'Неверный ключ!'], 403);
        }

        if ($this->updating)
        {
            return response()->json(['message' => 'Повторите попытку позже.'], 429);
        }

        $this->updating = true;

        $this->logRequest($request);

        try
        {
            $this->updateCodebase();
            $this->updating = false;
            return response()->json(['message' => 'Код успешно обновлён.']);
        }

        catch (\Exception $e)
        {
            $this->updating = false;
            return response()->json(['error' => 'Ошибка при обновлении кода.'], 500);
        }
    }

    private function logRequest(Request $request)
    {
        $logData = [
            'date' => now(),
            'ip' => $request->ip(),
        ];

        Log::info('Процесс обновления кода...', $logData);
    }

    private function updateCodebase()
    {
        $commands = [
            'git checkout main',
            'git fetch --all',
            'git reset --hard origin/main',
            'git pull origin main',
        ];

        foreach ($commands as $command) {
            Log::info("Выполнение команды: $command");
            $output = shell_exec($command);
            Log::info($output);
        }
    }
}
