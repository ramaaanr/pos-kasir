<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\Debt;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AdminDebtList extends Component
{
    use WithPagination;

    public $searchCustomer = '';
    
    #[Url(as: 'customer_id')]
    public $selectedCustomerId = null;
    
    public $activeDebts = [];
    public $selectedCustomer = null;

    protected $queryString = [
        'searchCustomer' => ['except' => '', 'as' => 'q'],
    ];

    public function mount()
    {
        if ($this->selectedCustomerId) {
            $this->selectCustomer($this->selectedCustomerId);
        }
    }

    public function selectCustomer($customerId)
    {
        $this->selectedCustomerId = $customerId;
        $this->selectedCustomer = Customer::find($customerId);
        
        if ($this->selectedCustomer) {
            $this->activeDebts = Debt::where('customer_id', $this->selectedCustomerId)
                ->with(['sale', 'payments'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $this->selectedCustomerId = null;
            $this->activeDebts = [];
        }
    }

    public function render()
    {
        $customers = Customer::where('total_debt', '>', 0)
            ->where(function($q) {
                $q->where('nama', 'like', '%' . $this->searchCustomer . '%')
                  ->orWhere('no_hp', 'like', '%' . $this->searchCustomer . '%');
            })
            ->orderBy('nama', 'asc')
            ->paginate(10);

        return view('livewire.admin.admin-debt-list', [
            'customers' => $customers
        ])->layout('components.layouts.admin', [
            'title' => 'Manajemen Piutang',
            'subtitle' => 'Pantau status hutang pelanggan dan history pembayaran'
        ]);
    }
}
