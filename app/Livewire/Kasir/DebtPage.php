<?php

namespace App\Livewire\Kasir;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Debt;
use App\Services\DebtPaymentService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Exception;

#[Layout('components.layouts.kasir', ['title' => 'Pembayaran Hutang'])]
class DebtPage extends Component
{
    // Search & Selection State
    public $searchCustomer = '';
    
    #[Url(as: 'customer_id')]
    public $selectedCustomerId = null;
    
    public $activeDebts = [];

    // Payment Modal State
    public $showPaymentModal = false;
    public $selectedDebtId = null;
    public $paymentAmount = 0;
    public $paymentMethod = 'cash';
    public $paymentNote = '';
    public $selectedDebt = null;

    // History Modal State
    public $showHistoryModal = false;
    public $historyDebt = null;

    // Feedback States
    public $showSuccessModal = false;
    public $showErrorModal = false;
    public $errorMessage = '';
    public $successData = [];

    public function mount()
    {
        if ($this->selectedCustomerId) {
            $this->loadActiveDebts();
        }
    }

    public function selectCustomer($customerId)
    {
        $this->selectedCustomerId = $customerId;
        $this->loadActiveDebts();
    }

    public function loadActiveDebts()
    {
        if (!$this->selectedCustomerId) return;

        $this->activeDebts = Debt::where('customer_id', $this->selectedCustomerId)
            ->whereIn('status', ['OPEN', 'PARTIAL', 'PAID'])
            ->with(['sale', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // If no more debts for this customer, clear selection
        if ($this->activeDebts->isEmpty()) {
            $this->selectedCustomerId = null;
        }
    }

    public function openPaymentModal($debtId)
    {
        $this->selectedDebtId = $debtId;
        $this->selectedDebt = Debt::with('sale')->find($debtId);
        $this->paymentAmount = (int) $this->selectedDebt->remaining_balance;
        $this->paymentMethod = 'cash';
        $this->paymentNote = '';
        $this->showPaymentModal = true;
    }

    public function openHistoryModal($debtId)
    {
        $this->historyDebt = Debt::with(['payments' => function($q) {
            $q->orderBy('paid_at', 'desc');
        }])->find($debtId);
        $this->showHistoryModal = true;
    }

    public function processPayment()
    {
        if (!$this->selectedDebt) return;

        try {
            // BUG FIX: Strict integer validation
            if (!is_numeric($this->paymentAmount) || $this->paymentAmount <= 0) {
                 throw new Exception("Nominal pembayaran harus berupa angka bulat positif.");
            }

            $service = app(DebtPaymentService::class);
            $payment = $service->processPayment($this->selectedDebt, [
                'amount' => (int) $this->paymentAmount,
                'payment_method' => 'cash', // Force cash only
                'paid_at' => now(),
                'note' => $this->paymentNote,
            ]);

            $remaining = $this->selectedDebt->fresh()->remaining_balance;

            $this->successData = [
                'invoice' => $this->selectedDebt->sale->invoice_number,
                'amount_paid' => $payment->amount,
                'remaining' => $remaining,
            ];

            $this->showPaymentModal = false;
            $this->showSuccessModal = true;
            
            // Refresh Data
            $this->loadActiveDebts();
            
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showPaymentModal = false;
            $this->showErrorModal = true;
        }
    }

    public function handleBack()
    {
        return $this->redirect(route('kasir.dashboard'), navigate: true);
    }

    public function render()
    {
        $customers = Customer::whereHas('debts')
            ->where(function($q) {
                $q->where('nama', 'like', '%' . $this->searchCustomer . '%')
                  ->orWhere('no_hp', 'like', '%' . $this->searchCustomer . '%');
            })
            ->orderBy('nama', 'asc')
            ->get();

        $selectedCustomer = $this->selectedCustomerId ? Customer::find($this->selectedCustomerId) : null;

        return view('livewire.kasir.debt-page', [
            'customers' => $customers,
            'selectedCustomer' => $selectedCustomer
        ]);
    }
}
