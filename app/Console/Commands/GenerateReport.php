<?php

namespace App\Console\Commands;

use App\Models\ChangeLog;
use App\Models\LogRequest;
use App\Models\TwoFactorCode;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерация и отправка полного отчёта администраторам';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = Carbon::now();
        $maxExecutionTime = env('REPORT_MAX_EXECUTION_TIME_MINUTES', 30);
        $hoursInterval = env('REPORT_INTERVAL_HOURS', 24);
        $reportStartTime = Carbon::now()->subHours($hoursInterval);

        Log::info('Начало генерации отчёта...');

        $methodCalls = LogRequest::select('controller_method', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_interaction'))
            ->where('created_at', '>=', $reportStartTime)
            ->groupBy('controller_method')
            ->orderByDesc('total')
            ->get();

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $entityChanges = ChangeLog::select('entity', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_interaction'))
            ->where('created_at', '>=', $reportStartTime)
            ->groupBy('entity')
            ->orderByDesc('total')
            ->get();

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $userRequests = LogRequest::select('user_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_interaction'))
            ->where('created_at', '>=', $reportStartTime)
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $userAuthentications = LogRequest::select('user_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_interaction'))
            ->where('created_at', '>=', $reportStartTime)
            ->where('controller_method', 'login')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $userPermissions = User::select('users.id', 'users.username', DB::raw('count(*) as total_permissions'))
            ->join('user_and_roles', 'users.id', '=', 'user_and_roles.user_id')
            ->join('roles', 'user_and_roles.role_id', '=', 'roles.id')
            ->join('role_and_permissions', 'roles.id', '=', 'role_and_permissions.role_id')
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_permissions')
            ->get();

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $userChanges = ChangeLog::select('user_id', DB::raw('count(*) as total'), DB::raw('max(created_at) as last_interaction'))
            ->where('created_at', '>=', $reportStartTime)
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $reportData = [
            'method_calls' => $methodCalls,
            'entity_changes' => $entityChanges,
            'user_requests' => $userRequests,
            'user_authentications' => $userAuthentications,
            'user_permissions' => $userPermissions,
            'user_changes' => $userChanges,
            'report_type' => 'полный',
            'report_time' => Carbon::now()->toDateTimeString()
        ];

        $fileName = 'report_' . Carbon::now()->format('Y_m_d_H_i_s') . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Полный отчёт');

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $sheet->setCellValue('A1', 'Тип отчёта');
        $sheet->setCellValue('B1', $reportData['report_type']);
        $sheet->setCellValue('A2', 'Время отчёта');
        $sheet->setCellValue('B2', $reportData['report_time']);

        $sheet->setCellValue('A4', 'Метод');
        $sheet->setCellValue('B4', 'Количество вызовов');
        $sheet->setCellValue('C4', 'Последний вызов');

        $row = 5;
        foreach ($reportData['method_calls'] as $methodCall) {
            $sheet->setCellValue('A' . $row, $methodCall->controller_method);
            $sheet->setCellValue('B' . $row, $methodCall->total);
            $sheet->setCellValue('C' . $row, $methodCall->last_interaction);
            $row++;
        }

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $sheet->setCellValue('E4', 'Сущность');
        $sheet->setCellValue('F4', 'Количество изменений');
        $sheet->setCellValue('G4','Последнее изменение');

        $row = 5;
        foreach ($reportData['entity_changes'] as $entityChange) {
            $sheet->setCellValue('E' . $row, $entityChange->entity);
            $sheet->setCellValue('F' . $row, $entityChange->total);
            $sheet->setCellValue('G' . $row, $entityChange->last_interaction);
            $row++;
        }

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $sheet->setCellValue('I4', 'Пользователь');
        $sheet->setCellValue('J4', 'Количество запросов');
        $sheet->setCellValue('K4', 'Последний запрос');

        $row = 5;
        foreach ($reportData['user_requests'] as $userRequest) {
            $user = User::find($userRequest->user_id);
            $sheet->setCellValue('I' . $row, $user ? $user->username : '-');
            $sheet->setCellValue('J' . $row, $userRequest->total);
            $sheet->setCellValue('K' . $row, $userRequest->last_interaction);
            $row++;
        }

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $sheet->setCellValue('M4', 'Пользователь');
        $sheet->setCellValue('N4', 'Количество авторизаций');
        $sheet->setCellValue('O4', 'Последняя авторизация');

        $row = 5;
        foreach ($reportData['user_authentications'] as $userAuthentication) {
            $user = User::find($userAuthentication->user_id);
            $sheet->setCellValue('M' . $row, $user ? $user->username : '-');
            $sheet->setCellValue('N' . $row, $userAuthentication->total);
            $sheet->setCellValue('O' . $row, $userAuthentication->last_interaction);
            $row++;
        }

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $sheet->setCellValue('Q4', 'Пользователь');
        $sheet->setCellValue('R4', 'Количество разрешений');

        $row = 5;
        foreach ($reportData['user_permissions'] as $userPermission) {
            $sheet->setCellValue('Q' . $row, $userPermission->username);
            $sheet->setCellValue('R' . $row, $userPermission->total_permissions);
            $row++;
        }

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $sheet->setCellValue('T4', 'Пользователь');
        $sheet->setCellValue('U4', 'Количество изменений');
        $sheet->setCellValue('V4', 'Последнее изменение пользователем');

        $row = 5;
        foreach ($reportData['user_changes'] as $userChange) {
            $user = User::find($userChange->user_id);
            $sheet->setCellValue('T' . $row, $user ? $user->username : '-');
            $sheet->setCellValue('U' . $row, $userChange->total);
            $sheet->setCellValue('V' . $row, $userChange->last_interaction);
            $row++;
        }

        if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

        $writer = new Xlsx($spreadsheet);
        $filePath = storage_path('app/public/' . $fileName);
        $writer->save($filePath);

        $admins = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('user_and_roles')
                ->where('role_id', 1);
        })->get();

        foreach ($admins as $admin) {
            if ($this->isExecutionTime($startTime, $maxExecutionTime)) return;

            $this->sendReport($admin, $filePath);
        }

        Storage::delete('public/' . $fileName);

        Log::info('Отчёт отправлен всем администраторам');
    }

    private function sendReport($admin, $filePath)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.mail.ru';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ugrasu_auth@mail.ru';
            $mail->Password   = 'VjQsL98Uu72AYhD0VjJg';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('ugrasu_auth@mail.ru');
            $mail->addAddress($admin->email);

            $mail->isHTML(false);
            $mail->Subject = 'Full Report';
            $mail->Body = 'Отчёт находится в закреплённом приложении в формате .xslx';
            $mail->addAttachment($filePath);

            $mail->send();
        }
        catch (Exception $e) {
            Log::info('Произошла ошибка при отправке сообщения!');
        }
    }

    private function isExecutionTime($startTime, $maxExecutionTime)
    {
        if ((Carbon::now()->diffInSeconds($startTime)) > $maxExecutionTime) {
            Log::warning('Время для выполнения задачи истекло!');
            return True;
        }
    }
}
