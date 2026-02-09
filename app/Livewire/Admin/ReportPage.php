<?php

namespace App\Livewire\Admin;

use App\Services\ReportService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\ShiftReport;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('components.layouts.admin', ['title' => 'Laporan & Analitik', 'subtitle' => 'Pusat data operasional dan pertumbuhan bisnis'])]
class ReportPage extends Component
{
    use WithPagination;

    public $activeTab = 'operasional';
    public $selectedReport = 'shift_harian'; // Default report
    public $filterType = 'daily';
    public $startDate;
    public $endDate;
    
    // Data Storage (Cached for the request)
    protected $currentReportData = null; 

    protected $queryString = [
        'selectedReport' => ['except' => 'shift_harian'],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function mount()
    {
        $this->startDate = date('Y-m-d');
        $this->endDate = date('Y-m-d');
        
        $this->setDefaultDates();
    }

    public function setDefaultDates()
    {
        if (in_array($this->selectedReport, ['penjualan_periode', 'fifo_pnl'])) {
            $this->startDate = date('Y-m-d', strtotime('-1 month'));
            $this->endDate = date('Y-m-d');
        } else {
            // For shift_harian, stok_realtime, hutang_outstanding, we pick just one date for recap
            // But we'll handle the actual filtering logic in loadReport/render
        }
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'selectedReport') {
            $this->setDefaultDates();
            $this->resetPage();
        }
    }

    public function selectReport($reportKey)
    {
        $this->selectedReport = $reportKey;
        $this->setDefaultDates();
        $this->resetPage();
    }

    public function exportExcel()
    {
        $service = new ReportService();
        $data = [];
        $filename = "Laporan_{$this->selectedReport}_" . date('Ymd_His') . ".xlsx";

        switch ($this->selectedReport) {
            case 'stok_realtime':
                $data = $service->getRealtimeStockReport()->map(fn($p) => [
                    'Produk' => $p->nama,
                    'Kode' => $p->kode_produk,
                    'Stok' => $p->total_stock,
                    'Unit' => $p->base_unit,
                    'Status' => strtoupper($p->stock_status)
                ])->toArray();
                break;
            case 'penjualan_periode':
                $data = $service->getPeriodicSalesReport($this->startDate, $this->endDate)->map(fn($s) => [
                    'Tanggal' => $s->created_at->format('d/m/Y H:i'),
                    'Invoice' => $s->invoice_number,
                    'Kasir' => $s->user->name ?? '-',
                    'Metode' => strtoupper($s->payment_method),
                    'Items' => $s->items->map(fn($i) => ($i->product->nama ?? 'Unknown') . " (x" . number_format($i->qty_base / ($i->unit_multiplier ?: 1), 0) . ")")->implode(', '),
                    'Total' => $s->total,
                ])->toArray();
                break;
            case 'fifo_pnl':
                $data = collect($service->getProfitMarginReport($this->startDate, $this->endDate))->map(fn($r) => [
                    'Tanggal' => date('d/m/Y H:i', strtotime($r->created_at)),
                    'Invoice' => $r->invoice_number,
                    'Revenue' => $r->total_revenue,
                    'COGS' => $r->total_cogs,
                    'Profit' => $r->gross_profit,
                    'Margin %' => number_format($r->margin_percent, 2) . '%'
                ])->toArray();
                break;
            case 'hutang_outstanding':
                $data = $service->getOutstandingDebtReport()->map(fn($d) => [
                    'Nama Customer' => $d->customer->nama ?? 'Guest',
                    'Jaminan' => $d->jaminan,
                    'Umur (Hari)' => $d->days_old,
                    'Total Hutang' => $d->amount,
                    'Status' => $d->status
                ])->toArray();
                break;
            case 'pembayaran_hutang':
                $data = $service->getDebtPaymentReport($this->startDate, $this->endDate)->map(fn($p) => [
                    'Waktu' => $p->paid_at->format('d/m/Y H:i'),
                    'Customer' => $p->customer->nama ?? '-',
                    'Metode' => strtoupper($p->payment_method),
                    'Nominal' => $p->amount,
                    'Penerima' => $p->user->name ?? '-'
                ])->toArray();
                break;
            case 'rekap_shift':
                $data = ShiftReport::whereDate('start_time', $this->startDate)
                    ->with('user')
                    ->get()
                    ->map(fn($s) => [
                        'Waktu' => $s->start_time->format('d/m/Y H:i'),
                        'Kasir' => $s->user->name ?? '-',
                        'Saldo Awal' => $s->opening_cash,
                        'Masuk (Sistem)' => $s->cash_received,
                        'Fisik (Laci)' => $s->cash_in_drawer,
                        'Selisih' => $s->difference,
                        'Status' => strtoupper($s->status),
                        'Catatan' => $s->note
                    ])->toArray();
                break;
            case 'inventory_valuation':
                $data = $service->getInventoryValuationReport()->map(fn($r) => [
                    'Produk' => $r['nama'],
                    'Stok' => $r['total_stock'],
                    'Hrg Beli Rata-rata' => $r['avg_buying_price'],
                    'Total Nilai' => $r['total_valuation'],
                ])->toArray();
                break;
            case 'debt_aging':
                $data = $service->getDebtAgingReport()->map(fn($r) => [
                    'Customer' => $r['customer_name'],
                    'Hutang 0-30 Hari' => $r['aging']['current'],
                    'Hutang 31-60 Hari' => $r['aging']['at_risk'],
                    'Hutang >60 Hari' => $r['aging']['danger'],
                    'Total Piutang' => $r['aging']['total'],
                ])->toArray();
                break;
            case 'cashier_performance':
                $data = $service->getCashierPerformanceReport($this->startDate, $this->endDate)->map(fn($r) => [
                    'Kasir' => $r->name,
                    'JML Transaksi' => $r->transaction_count,
                    'Total Penjualan' => $r->total_sales,
                    'Rata-rata Transaksi' => $r->avg_transaction_value,
                    'Selisih Kas' => $r->total_difference,
                ])->toArray();
                break;
            case 'slow_moving_stock':
                $data = $service->getSlowMovingReport()->map(fn($r) => [
                    'Produk' => $r['nama'],
                    'Stok Saat Ini' => $r['total_stock'],
                    'Tgl Terakhir Laku' => $r['last_sold_at'] ? date('d/m/Y', strtotime($r['last_sold_at'])) : 'Belum Pernah',
                    'Umur Diam (Hari)' => $r['days_inactive'],
                    'Status' => strtoupper($r['status']),
                ])->toArray();
                break;
            case 'fifo_compliance':
                $data = collect($service->getFIFOComplianceReport())->map(fn($r) => [
                    'Waktu Jual' => date('d/m/Y H:i', strtotime($r->sale_date)),
                    'Invoice' => $r->invoice_number,
                    'Batch' => $r->batch_code,
                    'Tgl Masuk Batch' => date('d/m/Y', strtotime($r->batch_date)),
                    'Qty' => $r->qty_base
                ])->toArray();
                break;
        }

        if (empty($data)) return;

        return Excel::download(new ReportExport($data), $filename);
    }

    public function getReportDataProperty()
    {
        if ($this->currentReportData) return $this->currentReportData;

        $service = new ReportService();

        switch ($this->selectedReport) {
            case 'shift_harian':
                $result = $service->getDailyShiftReport(null, $this->startDate, $this->startDate);
                // Convert transactions to array for consistency in blade
                if (isset($result['transactions'])) {
                    $result['transactions'] = $result['transactions']->toArray();
                }
                return $this->currentReportData = $result;

            case 'stok_realtime':
                return $this->currentReportData = $service->getRealtimeStockReport()->toArray();

            case 'penjualan_periode':
                // Paginated Eloquent Query
                return $this->currentReportData = Sale::whereIn('status', ['completed', 'partial'])
                    ->whereDate('created_at', '>=', $this->startDate)
                    ->whereDate('created_at', '<=', $this->endDate)
                    ->with(['user', 'items.product'])
                    ->latest()
                    ->paginate(50);

            case 'fifo_pnl':
                return $this->currentReportData = $service->getProfitMarginReport($this->startDate, $this->endDate)
                    ->map(fn($item) => (array) $item) // Convert stdClass to array
                    ->toArray();

            case 'hutang_outstanding':
                return $this->currentReportData = $service->getOutstandingDebtReport()->toArray();

            case 'pembayaran_hutang':
                return $this->currentReportData = $service->getDebtPaymentReport($this->startDate, $this->endDate)->toArray();

            case 'rekap_shift':
                return $this->currentReportData = ShiftReport::whereDate('start_time', $this->startDate)
                    ->with('user')
                    ->latest()
                    ->get()
                    ->toArray();

            case 'inventory_valuation':
                return $this->currentReportData = $service->getInventoryValuationReport()->toArray();

            case 'debt_aging':
                return $this->currentReportData = $service->getDebtAgingReport()->toArray();

            case 'cashier_performance':
                return $this->currentReportData = $service->getCashierPerformanceReport($this->startDate, $this->endDate)
                    ->map(fn($item) => (array) $item)
                    ->toArray();

            case 'slow_moving_stock':
                return $this->currentReportData = $service->getSlowMovingReport()->toArray();

            case 'fifo_compliance':
                return $this->currentReportData = $service->getFIFOComplianceReport()
                    ->map(fn($item) => (array) $item)
                    ->toArray();
            default:
                return $this->currentReportData = [];
        }
    }

    public function render()
    {
        return view('livewire.admin.report-page', [
            'reportData' => $this->report_data
        ]);
    }
}
