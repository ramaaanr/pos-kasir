<?php

namespace App\Services;

use App\Models\BackupHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Exception;

class BackupService
{
    public function backup()
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $dbName = env('DB_DATABASE', 'kasir_fifo');
        $filename = "backup_mysql_{$timestamp}_{$dbName}.sql";

        $destinations = [
            'App Storage' => storage_path('app/backups'),
            'Local Documents' => config('custom_backup.destinations.local'),
        ];

        $mainPath = $destinations['App Storage'] . DIRECTORY_SEPARATOR . $filename;
        $logs = [];

        try {
            // --- 1. VALIDASI FILE MYSQLDUMP ---
            $mysqldumpPath = config('custom_backup.mysqldump_path');
            if (!file_exists($mysqldumpPath)) {
                throw new Exception("Tool mysqldump tidak ditemukan di: {$mysqldumpPath}");
            }
            $mysqldump = '"' . $mysqldumpPath . '"';

            // --- 2. VALIDASI & PEMBUATAN FOLDER ---
foreach ($destinations as $label => $folder) {
    try {
        if (!file_exists($folder)) {
            // Gunakan @ untuk meredam warning bawaan agar bisa kita handle manual
            if (!@mkdir($folder, 0755, true)) {
                $error = error_get_last();
                $sysMsg = $error['message'] ?? 'Tidak ada pesan sistem';
                
                // Log detail ke laravel.log
                Log::error("Gagal mkdir di {$label}. Path: {$folder}. Error: {$sysMsg}");
                
                // Lempar exception agar muncul di UI/Console
                throw new Exception("Izin Ditolak saat membuat folder [{$label}]. Lokasi: {$folder}. Pesan OS: {$sysMsg}");
            }
            $logs[] = "Folder {$label} berhasil dibuat.";
        }

        if (!is_writable($folder)) {
            throw new Exception("Folder [{$label}] ada tapi TIDAK BISA DITULIS: {$folder}");
        }
    } catch (Exception $e) {
        // Langsung lempar ke catch utama agar tercatat di tabel BackupHistory
        throw $e;
    }
}

            // --- 3. EKSEKUSI MYSQLDUMP ---
            $dbUser = env('DB_USERNAME', 'root');
            $dbPass = env('DB_PASSWORD', '');
            $dbHost = env('DB_HOST', '127.0.0.1');
            $dbPort = env('DB_PORT', '3307');

            // Gunakan --result-file agar lebih stabil di Windows daripada operator >
            $command = "{$mysqldump} --host={$dbHost} --port={$dbPort} --user={$dbUser} --password=\"{$dbPass}\" {$dbName} --no-tablespaces --result-file=\"{$mainPath}\" 2>&1";

            $output = [];
            $resultCode = null;
            exec($command, $output, $resultCode);

            if ($resultCode !== 0) {
                $cmdError = implode("\n", $output);
                throw new Exception("MySQLDump Exit Code {$resultCode}. Pesan: {$cmdError}");
            }

            if (!file_exists($mainPath) || filesize($mainPath) === 0) {
                throw new Exception("File backup berhasil dibuat tapi kosong atau tidak ditemukan di: {$mainPath}");
            }

            $logs[] = "Master dump berhasil dibuat.";

            // --- 4. PROSES PENYALINAN (COPY) ---
            foreach ($destinations as $label => $folder) {
                if ($label === 'App Storage') continue;

                $targetPath = $folder . DIRECTORY_SEPARATOR . $filename;
                if (copy($mainPath, $targetPath)) {
                    $logs[] = "Berhasil ke {$label}";
                } else {
                    $error = error_get_last();
                    $logs[] = "Gagal ke {$label} (Cek Google Drive/Permissions: " . ($error['message'] ?? 'Unknown') . ")";
                }
            }

            // Simpan riwayat sukses
            BackupHistory::create([
                'filename' => $filename,
                'path'     => 'backups/' . $filename,
                'size'     => $this->formatSize(filesize($mainPath)),
                'status'   => 'success',
                'notes'    => implode(" | ", $logs)
            ]);

            return ['success' => true, 'message' => 'Backup tuntas!', 'details' => $logs];

        } catch (Exception $e) {
            $errorDetail = "ERROR BACKUP: " . $e->getMessage();
            Log::error($errorDetail);

            BackupHistory::create([
                'filename' => $filename,
                'path'     => 'backups/' . $filename,
                'size'     => '0 B',
                'status'   => 'failed',
                'notes'    => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage()
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