<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\ReportService;
use App\Models\Debt;

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

        // Accrual Profit = Gross Profit - outstanding debt from this month's sales
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();
        $outstandingDebt = Debt::whereHas('sale', function($q) use ($startOfMonth, $endOfMonth) {
                $q->whereDate('created_at', '>=', $startOfMonth)
                  ->whereDate('created_at', '<=', $endOfMonth);
            })->whereIn('status', ['OPEN', 'PARTIAL'])
            ->with('payments')
            ->get()
            ->sum(fn($d) => $d->amount - $d->payments->sum('amount'));

        $this->monthlyStats['accrual_profit'] = $this->monthlyStats['profit'] - $outstandingDebt;
        $this->monthlyStats['outstanding_debt'] = $outstandingDebt;
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard');
    }
}
