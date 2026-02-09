<?php

namespace App\Services;

use App\Models\BackupHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class BackupService
{
    public function backup()
    {
        $filename = 'backup_mysql_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        // Store in storage/app/backups
        $path = storage_path('app/backups/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $dbUser = env('DB_USERNAME');
        $dbPass = env('DB_PASSWORD');
        $dbName = env('DB_DATABASE');
        $dbHost = env('DB_HOST');

        // Construct mysqldump command
        // Note: Using --no-tablespaces to avoid permission issues in some environments
        // Use MYSQL_PWD to handle passwords with special characters safely
        $command = "MYSQL_PWD='{$dbPass}' mysqldump -h {$dbHost} -u {$dbUser} {$dbName} --no-tablespaces --skip-ssl-verify-server-cert > \"{$path}\"";

        $output = null;
        $resultCode = null;

        try {
            // Using exec to run the command
            exec($command, $output, $resultCode);

            if ($resultCode === 0 && file_exists($path) && filesize($path) > 0) {
                BackupHistory::create([
                    'filename' => $filename,
                    'path' => 'backups/' . $filename,
                    'size' => $this->formatSize(filesize($path)),
                    'status' => 'success',
                ]);

                return [
                    'success' => true,
                    'message' => 'Database backup created successfully.',
                    'filename' => $filename
                ];
            } else {
                Log::error('Backup failed. Command: ' . $command . ' Result Code: ' . $resultCode);
                BackupHistory::create([
                    'filename' => $filename,
                    'path' => 'backups/' . $filename, // Path might not exist but we log the attempt
                    'size' => '0 B',
                    'status' => 'failed',
                    'notes' => 'Exit code: ' . $resultCode
                ]);

                return [
                    'success' => false,
                    'message' => 'Backup failed. Check logs for details.'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Backup exception: ' . $e->getMessage());

            BackupHistory::create([
                'filename' => $filename,
                'path' => 'backups/' . $filename,
                'size' => '0 B',
                'status' => 'failed',
                'notes' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Backup failed with exception: ' . $e->getMessage()
            ];
        }
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
