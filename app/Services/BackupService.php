<?php

namespace App\Services;

use App\Models\BackupHistory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class BackupService
{
    public function backup()
    {
        // Format Nama File: backup_mysql_2026-02-09_14-30-05_kasir_fifo.sql
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $dbName = env('DB_DATABASE', 'kasir_fifo');
        $filename = "backup_mysql_{$timestamp}_{$dbName}.sql";

        // --- DEFINISI 3 LOKASI PENYIMPANAN ---
        $destinations = [
            'App Storage' => storage_path('app/backups'),
            'Local Documents' => 'C:\Documents\TOKPOS\Backups',
            'Google Drive' => 'G:\My Drive\TOKPOS'
        ];

        // Path utama untuk proses mysqldump (kita pakai lokasi pertama sebagai master)
        $mainPath = $destinations['App Storage'] . DIRECTORY_SEPARATOR . $filename;

        // Pastikan semua folder tujuan tersedia
        foreach ($destinations as $label => $folder) {
            if (!file_exists($folder)) {
                mkdir($folder, 0755, true);
            }
        }

        // --- PROSES MYSQLDUMP ---
        $mysqldump = '"C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe"';
        $dbUser = env('DB_USERNAME', 'root');
        $dbPass = env('DB_PASSWORD', '');
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3307');

        $command = "{$mysqldump} --host={$dbHost} --port={$dbPort} --user={$dbUser} --password=\"{$dbPass}\" {$dbName} --no-tablespaces > \"{$mainPath}\" 2>&1";

        $output = [];
        $resultCode = null;

        try {
            exec($command, $output, $resultCode);

            if ($resultCode === 0 && file_exists($mainPath) && filesize($mainPath) > 0) {
                
                // --- PROSES COPY KE LOKASI LAIN ---
                $successLogs = ["Proses dump berhasil."];
                
                foreach ($destinations as $label => $folder) {
                    if ($label === 'App Storage') continue; // Lewati karena ini file master

                    $targetPath = $folder . DIRECTORY_SEPARATOR . $filename;
                    
                    if (copy($mainPath, $targetPath)) {
                        $successLogs[] = "Berhasil disalin ke: {$label}";
                    } else {
                        $successLogs[] = "GAGAL salin ke: {$label}";
                        Log::warning("Gagal menyalin backup ke {$label}");
                    }
                }

                // Catat di Database
                BackupHistory::create([
                    'filename' => $filename,
                    'path'     => 'backups/' . $filename,
                    'size'     => $this->formatSize(filesize($mainPath)),
                    'status'   => 'success',
                    'notes'    => implode(" | ", $successLogs)
                ]);

                return [
                    'success' => true,
                    'message' => 'Backup sukses tersimpan di 3 lokasi!',
                    'details' => $successLogs
                ];

            } else {
                $errorMessage = implode("\n", $output);
                Log::error("Backup Gagal: " . $errorMessage);
                return ['success' => false, 'message' => 'Gagal membuat dump: ' . $errorMessage];
            }

        } catch (\Exception $e) {
            Log::error('Exception Backup: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
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