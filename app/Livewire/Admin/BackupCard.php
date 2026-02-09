<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\BackupHistory;
use App\Services\BackupService;

class BackupCard extends Component
{
    public function backup(BackupService $backupService)
    {
        try {
            $result = $backupService->backup();

            if ($result['success']) {
                session()->flash('success', $result['message']);
            } else {
                session()->flash('error', $result['message']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat backup: ' . $e->getMessage());
        }

        $this->dispatch('backup-completed');
    }

    public function getLastBackupProperty()
    {
        return BackupHistory::latest()->first();
    }

    public function render()
    {
        return view('livewire.admin.backup-card');
    }
}
