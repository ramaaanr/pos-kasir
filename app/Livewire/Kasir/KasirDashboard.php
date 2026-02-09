<?php

namespace App\Livewire\Kasir;

use App\Models\Sale;
use App\Models\ShiftReport;
use App\Models\DebtPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.kasir', ['title' => 'Dashboard'])]
class KasirDashboard extends Component
{
    public $openingCash = 0;
    public $cashInDrawer = 0;
    public $note = '';
    
    public $showOpeningModal = false;
    public $showClosingModal = false;

    public function mount()
    {
        $this->checkActiveShift();
    }

    public function checkActiveShift()
    {
        $activeShift = ShiftReport::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();

        if (!$activeShift) {
            $this->showOpeningModal = true;
        }
    }

    public function getActiveShiftProperty()
    {
        return ShiftReport::where('user_id', Auth::id())
            ->where('status', 'open')
            ->first();
    }

    public function startShift()
    {
        $this->validate([
            'openingCash' => 'required|numeric|min:0',
        ]);

        ShiftReport::create([
            'user_id' => Auth::id(),
            'opening_cash' => $this->openingCash,
            'start_time' => now(),
            'status' => 'open',
        ]);

        $this->showOpeningModal = false;
        $this->dispatch('notify', message: 'Shift dimulai!', type: 'success');
    }

    public function calculateSystemCash()
    {
        $shift = $this->activeShift;
        if (!$shift) return 0;

        // 1. Cash Sales
        $cashSales = Sale::where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->where('payment_method', 'cash')
            ->where('created_at', '>=', $shift->start_time)
            ->sum('total');

        // 2. DP from Debt Sales
        $dpPayments = Sale::where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->where('payment_method', 'debt')
            ->where('created_at', '>=', $shift->start_time)
            ->sum('total_paid');

        // 3. Debt Payments
        $debtPayments = DebtPayment::where('user_id', Auth::id())
            ->where('payment_method', 'cash')
            ->where('created_at', '>=', $shift->start_time)
            ->sum('amount');

        return $cashSales + $dpPayments + $debtPayments;
    }

    public function closeShift()
    {
        $this->validate([
            'cashInDrawer' => 'required|numeric|min:0',
        ]);

        $shift = $this->activeShift;
        if (!$shift) return;

        $systemCash = $this->calculateSystemCash();
        $totalExpected = $shift->opening_cash + $systemCash;
        $difference = $this->cashInDrawer - $totalExpected;

        $shift->update([
            'cash_received' => $systemCash,
            'cash_in_drawer' => $this->cashInDrawer,
            'difference' => $difference,
            'note' => $this->note,
            'end_time' => now(),
            'status' => 'closed',
        ]);

        $this->showClosingModal = false;
        $this->openingCash = 0;
        $this->cashInDrawer = 0;
        $this->note = '';
        
        $this->dispatch('notify', message: 'Shift ditutup!', type: 'success');
        $this->showOpeningModal = true; // Ask for next shift if they refresh or stay
    }
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
            'activeShift' => $this->activeShift,
            'systemCash' => $this->calculateSystemCash()
        ]);
    }
}
