<?php

namespace App\Livewire\Admin;

use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class SalesPage extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedSale = null;
    public $showDetailModal = false;

    // Snapshot for printing
    public $printData = [];

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount($invoice = null)
    {
        if ($invoice) {
            $this->openDetail($invoice);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openDetail($invoiceNumber)
    {
        $this->selectedSale = Sale::with(['items.product', 'customer', 'user', 'debt'])
            ->where('invoice_number', $invoiceNumber)
            ->first();

        if ($this->selectedSale) {
            $this->showDetailModal = true;
            $this->preparePrintData();
        } else {
            session()->flash('error', 'Invoice tidak ditemukan.');
        }
    }

    private function preparePrintData()
    {
        if (!$this->selectedSale) return;

        $this->printData = [
            'invoice' => $this->selectedSale->invoice_number,
            'date' => $this->selectedSale->created_at->format('d/m/Y H:i'),
            'cashier' => $this->selectedSale->user->name ?? 'System',
            'method' => $this->selectedSale->payment_method,
            'customer' => $this->selectedSale->customer->nama ?? 'Umum',
            'total' => $this->selectedSale->total,
            'paid' => $this->selectedSale->total_paid,
            'cash_received' => $this->selectedSale->cash_received,
            'cash_change' => $this->selectedSale->cash_change,
            'items' => $this->selectedSale->items->map(function($item) {
                return [
                    'nama' => $item->product->nama,
                    'qty' => $item->qty_base / $item->unit_multiplier,
                    'unit' => $item->unit_label,
                    'harga' => $item->harga_jual_per_unit * $item->unit_multiplier,
                    'subtotal' => $item->subtotal
                ];
            })->toArray()
        ];
    }

    public function closeDetail()
    {
        $this->showDetailModal = false;
        $this->selectedSale = null;
        $this->printData = [];
        
        // If we were on a detail URL, go back to index without refreshing
        if (request()->routeIs('admin.sales.detail')) {
            return redirect()->route('admin.sales.index');
        }
    }

    public function render()
    {
        $sales = Sale::with(['customer', 'user', 'debt'])
            ->where('invoice_number', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(15);

        return view('livewire.admin.sales-page', [
            'sales' => $sales
        ])->layout('components.layouts.admin', [
            'title' => 'Riwayat Penjualan',
            'subtitle' => 'Kelola dan lihat detail semua transaksi penjualan'
        ]);
    }
    public function printInvoice()
    {
        if (!$this->selectedSale) return;

        try {
            app(\App\Services\PrinterService::class)->printInvoice($this->selectedSale);
            $this->dispatch('notify', message: 'Struk berhasil dicetak.', type: 'success');
        } catch (\Exception $e) {
            $printerUrl = config('app.printer_url');
            $this->dispatch('notify', message: "Gagal mencetak ke '$printerUrl': " . $e->getMessage(), type: 'error');
        }
    }
}
