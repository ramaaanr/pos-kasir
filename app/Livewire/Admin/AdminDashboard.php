<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\ReportService;

class AdminDashboard extends Component
{
    public $monthlyStats = [];
    public $recentSales = [];
    public $recentDebts = [];
    public $part = null; // 'stats' or 'activity'

    public function mount($part = null)
    {
        $this->part = $part;
        $service = new ReportService();
        $this->monthlyStats = $service->getMonthlyStats();
        $dashboardData = $service->getDashboardData();
        
        $this->recentSales = $dashboardData['recent_sales'];
        $this->recentDebts = $dashboardData['recent_debts'];
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard');
    }
}
