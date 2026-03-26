<?php

namespace App\Livewire\Kasir;

use App\Models\Sale;
use App\Models\ShiftReport;
use App\Models\DebtPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('components.layouts.kasir', ['title' => 'Dashboard'])]
class KasirDashboard extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $stockSearch = '';

    #[Url(as: 'sale_q')]
    public $saleSearch = '';

    #[Url(as: 'sale_status')]
    public $saleStatus = 'all';

    public $openingCash = 0;
    public $cashInDrawer = 0;
    public $note = '';
    
    public $showOpeningModal = false;
    public $showClosingModal = false;
    public $isShiftOverdue = false;

    // Sales History Detail
    public $showDetailModal = false;
    public $selectedSale = null;

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
            $this->isShiftOverdue = false; // Reset if no active shift
        } else {
            // Check if shift is older than 24 hours
            $this->isShiftOverdue = $activeShift->start_time->diffInHours(now()) >= 24;
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
        $this->isShiftOverdue = false; // Brand new shift is never overdue
        $this->dispatch('notify', message: 'Shift dimulai!', type: 'success');

        // Refresh state
        $this->checkActiveShift();
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
        $this->checkActiveShift(); // This will set showOpeningModal to true and reset isShiftOverdue
    }

    public function openDetail($invoiceNumber)
    {
        $this->selectedSale = Sale::with(['items.product', 'customer', 'user', 'debt'])
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if ($this->selectedSale) {
            $this->showDetailModal = true;
        } else {
            $this->dispatch('notify', message: 'Invoice tidak ditemukan.', type: 'error');
        }
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedSale = null;
    }

    public function printInvoice()
    {
        if (!$this->selectedSale) return;

        try {
            app(\App\Services\PrinterService::class)->printInvoice($this->selectedSale);
            $this->dispatch('notify', message: 'Struk berhasil dicetak.', type: 'success');
        } catch (\Exception $e) {
            $printerUrl = config('app.printer_url');
            $this->dispatch('notify', message: "Gagal mencetak: " . $e->getMessage(), type: 'error');
        }
    }

    public function updatingSaleSearch()
    {
        $this->resetPage('sales');
    }
    public function updatingSaleStatus()
    {
        $this->resetPage('sales');
    }
    public function updatingStockSearch()
    {
        $this->resetPage('stock');
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

        $recentTransactions = Sale::with(['customer', 'user'])
            ->when($this->saleSearch, function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->saleSearch . '%');
            })
            ->when($this->saleStatus !== 'all', function ($q) {
                $q->where('status', $this->saleStatus);
            })
            ->latest()
            ->paginate(10, pageName: 'sales');

        $stockData = \App\Models\Product::active()
            ->search($this->stockSearch)
            ->with(['batches' => function($q) {
                $q->where('qty_sisa_base', '>', 0)->latest();
            }])
            ->withSum('batches', 'qty_sisa_base')
            ->orderBy('batches_sum_qty_sisa_base', 'asc')
            ->paginate(10, pageName: 'stock');

        return view('livewire.kasir.kasir-dashboard', [
            'todayTransactionCount' => $todayTransactionCount,
            'todayTotalSales' => $todayTotalSales,
            'recentTransactions' => $recentTransactions,
            'stockData' => $stockData,
            'activeShift' => $this->activeShift,
            'systemCash' => $this->calculateSystemCash()
        ]);
    }
}
