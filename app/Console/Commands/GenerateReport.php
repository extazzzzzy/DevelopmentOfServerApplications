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
        $hoursInterval = env('REPORT_INTERVAL_HOURS', 1);
        $reportStartTime = Carbon::now()->subHours($hoursInterval);

        Log::info('Начало генерации отчёта...');

        $methodCalls = LogRequest::select('controller_method', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $reportStartTime)
            ->groupBy('controller_method')
            ->orderByDesc('total')
            ->get();

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $entityChanges = ChangeLog::select('entity', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $reportStartTime)
            ->groupBy('entity')
            ->orderByDesc('total')
            ->get();

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $userRequests = LogRequest::select('user_id', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $reportStartTime)
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $userAuthentications = LogRequest::select('user_id', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $reportStartTime)
            ->where('controller_method', 'login')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $userPermissions = User::select('users.id', 'users.username', DB::raw('count(*) as total_permissions'))
            ->join('user_and_roles', 'users.id', '=', 'user_and_roles.user_id')
            ->join('roles', 'user_and_roles.role_id', '=', 'roles.id')
            ->join('role_and_permissions', 'roles.id', '=', 'role_and_permissions.role_id')
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_permissions')
            ->get();

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $userChanges = ChangeLog::select('user_id', DB::raw('count(*) as total'))
            ->where('created_at', '>=', $reportStartTime)
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->get();

        $this->checkExecutionTime($startTime, $maxExecutionTime);

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

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $sheet->setCellValue('A1', 'Тип отчёта');
        $sheet->setCellValue('B1', $reportData['report_type']);
        $sheet->setCellValue('A2', 'Время отчёта');
        $sheet->setCellValue('B2', $reportData['report_time']);

        $sheet->setCellValue('A4', 'Метод');
        $sheet->setCellValue('B4', 'Количество вызовов');

        $row = 5;
        foreach ($reportData['method_calls'] as $methodCall) {
            $sheet->setCellValue('A' . $row, $methodCall->controller_method);
            $sheet->setCellValue('B' . $row, $methodCall->total);
            $row++;
        }

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $sheet->setCellValue('D4', 'Сущность');
        $sheet->setCellValue('E4', 'Количество изменений');

        $row = 5;
        foreach ($reportData['entity_changes'] as $entityChange) {
            $sheet->setCellValue('D' . $row, $entityChange->entity);
            $sheet->setCellValue('E' . $row, $entityChange->total);
            $row++;
        }

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $sheet->setCellValue('G4', 'Пользователь');
        $sheet->setCellValue('H4', 'Количество запросов');

        $row = 5;
        foreach ($reportData['user_requests'] as $userRequest) {
            $user = User::find($userRequest->user_id);
            $sheet->setCellValue('G' . $row, $user ? $user->username : '-');
            $sheet->setCellValue('H' . $row, $userRequest->total);
            $row++;
        }

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $sheet->setCellValue('J4', 'Пользователь');
        $sheet->setCellValue('K4', 'Количество авторизаций');

        $row = 5;
        foreach ($reportData['user_authentications'] as $userAuthentication) {
            $user = User::find($userAuthentication->user_id);
            $sheet->setCellValue('J' . $row, $user ? $user->username : '-');
            $sheet->setCellValue('K' . $row, $userAuthentication->total);
            $row++;
        }

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $sheet->setCellValue('M4', 'Пользователь');
        $sheet->setCellValue('N4', 'Количество разрешений');

        $row = 5;
        foreach ($reportData['user_permissions'] as $userPermission) {
            $sheet->setCellValue('M' . $row, $userPermission->username);
            $sheet->setCellValue('N' . $row, $userPermission->total_permissions);
            $row++;
        }

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $sheet->setCellValue('P4', 'Пользователь');
        $sheet->setCellValue('Q4', 'Количество изменений');

        $row = 5;
        foreach ($reportData['user_changes'] as $userChange) {
            $user = User::find($userChange->user_id);
            $sheet->setCellValue('P' . $row, $user ? $user->username : '-');
            $sheet->setCellValue('Q' . $row, $userChange->total);
            $row++;
        }

        $this->checkExecutionTime($startTime, $maxExecutionTime);

        $writer = new Xlsx($spreadsheet);
        $filePath = storage_path('app/public/' . $fileName);
        $writer->save($filePath);

        $admins = User::whereIn('id', function ($query) {
            $query->select('user_id')
                ->from('user_and_roles')
                ->where('role_id', 1);
        })->get();

        foreach ($admins as $admin) {
            $this->checkExecutionTime($startTime, $maxExecutionTime);

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

    private function checkExecutionTime($startTime, $maxExecutionTime)
    {
        if ((Carbon::now()->diffInSeconds($startTime)) > $maxExecutionTime) {
            Log::warning('Время для выполнения задачи истекло!');
            return;
        }
    }
}
