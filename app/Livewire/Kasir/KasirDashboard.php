<?php

namespace App\Livewire\Kasir;

use App\Models\Sale;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.kasir', ['title' => 'Dashboard'])]
class KasirDashboard extends Component
{
    public function render()
    {
        $today = now()->startOfDay();

        $todayTransactionCount = Sale::where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->count();

        $todayTotalSales = Sale::where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->sum('total');

        $recentTransactions = Sale::with('customer')
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.kasir.kasir-dashboard', [
            'todayTransactionCount' => $todayTransactionCount,
            'todayTotalSales' => $todayTotalSales,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
