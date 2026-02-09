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

    public function prepareExportData()
    {
        $service = new ReportService();
        $data = [];
        $filename = "Laporan_{$this->selectedReport}_" . date('Ymd_His') . ".xlsx";

        switch ($this->selectedReport) {
            case 'stok_realtime':
                $filename = "Stok Realtime - " . date('Y-m-d H-i') . ".xlsx";
                $data = $service->getRealtimeStockReport()->map(fn($p) => [
                    'Produk' => $p->nama,
                    'Kode' => $p->kode_produk,
                    'Stok' => $p->total_stock,
                    'Unit' => $p->base_unit,
                    'Status' => strtoupper($p->stock_status)
                ])->toArray();
                break;
            case 'penjualan_periode':
                $filename = "Penjualan Periode - " . date('Y-m-d', strtotime($this->startDate)) . " to " . date('Y-m-d', strtotime($this->endDate)) . ".xlsx";
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
                $filename = "PnL FIFO - " . date('Y-m-d', strtotime($this->startDate)) . " to " . date('Y-m-d', strtotime($this->endDate)) . ".xlsx";
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
                $filename = "Hutang Outstanding - " . date('Y-m-d H-i') . ".xlsx";
                $data = $service->getOutstandingDebtReport()->map(fn($d) => [
                    'Nama Customer' => $d->customer->nama ?? 'Guest',
                    'Jaminan' => $d->jaminan,
                    'Umur (Hari)' => $d->days_old,
                    'Total Hutang' => $d->amount,
                    'Status' => $d->status
                ])->toArray();
                break;
            case 'pembayaran_hutang':
                $filename = "Pembayaran Hutang - " . date('Y-m-d', strtotime($this->startDate)) . " to " . date('Y-m-d', strtotime($this->endDate)) . ".xlsx";
                $data = $service->getDebtPaymentReport($this->startDate, $this->endDate)->map(fn($p) => [
                    'Waktu' => $p->paid_at->format('d/m/Y H:i'),
                    'Customer' => $p->customer->nama ?? '-',
                    'Metode' => strtoupper($p->payment_method),
                    'Nominal' => $p->amount,
                    'Penerima' => $p->user->name ?? '-'
                ])->toArray();
                break;
            case 'rekap_shift':
                $filename = "Rekap Shift - " . date('Y-m-d', strtotime($this->startDate)) . ".xlsx";
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
                $filename = "Valuasi Inventory - " . date('Y-m-d H-i') . ".xlsx";
                $data = $service->getInventoryValuationReport()->map(fn($r) => [
                    'Produk' => $r['nama'],
                    'Stok' => $r['total_stock'],
                    'Hrg Beli Rata-rata' => $r['avg_buying_price'],
                    'Total Nilai' => $r['total_valuation'],
                ])->toArray();
                break;
            case 'debt_aging':
                $filename = "Aging Hutang - " . date('Y-m-d H-i') . ".xlsx";
                $data = $service->getDebtAgingReport()->map(fn($r) => [
                    'Customer' => $r['customer_name'],
                    'Hutang 0-30 Hari' => $r['aging']['current'],
                    'Hutang 31-60 Hari' => $r['aging']['at_risk'],
                    'Hutang >60 Hari' => $r['aging']['danger'],
                    'Total Piutang' => $r['aging']['total'],
                ])->toArray();
                break;
            case 'cashier_performance':
                 $filename = "Performa Kasir - " . date('Y-m-d', strtotime($this->startDate)) . " to " . date('Y-m-d', strtotime($this->endDate)) . ".xlsx";
                $data = $service->getCashierPerformanceReport($this->startDate, $this->endDate)->map(fn($r) => [
                    'Kasir' => $r->name,
                    'JML Transaksi' => $r->transaction_count,
                    'Total Penjualan' => $r->total_sales,
                    'Rata-rata Transaksi' => $r->avg_transaction_value,
                    'Selisih Kas' => $r->total_difference,
                ])->toArray();
                break;
            case 'slow_moving_stock':
                $filename = "Slow Moving Stock - " . date('Y-m-d H-i') . ".xlsx";
                $data = $service->getSlowMovingReport()->map(fn($r) => [
                    'Produk' => $r['nama'],
                    'Stok Saat Ini' => $r['total_stock'],
                    'Tgl Terakhir Laku' => $r['last_sold_at'] ? date('d/m/Y', strtotime($r['last_sold_at'])) : 'Belum Pernah',
                    'Umur Diam (Hari)' => $r['days_inactive'],
                    'Status' => strtoupper($r['status']),
                ])->toArray();
                break;
            case 'fifo_compliance':
                $filename = "FIFO Compliance - " . date('Y-m-d H-i') . ".xlsx";
                $data = collect($service->getFIFOComplianceReport())->map(fn($r) => [
                    'Waktu Jual' => date('d/m/Y H:i', strtotime($r->sale_date)),
                    'Invoice' => $r->invoice_number,
                    'Batch' => $r->batch_code,
                    'Tgl Masuk Batch' => date('d/m/Y', strtotime($r->batch_date)),
                    'Qty' => $r->qty_base
                ])->toArray();
                break;
        }

        return ['data' => $data, 'filename' => $filename];
    }

    public function exportExcel()
    {
        $export = $this->prepareExportData();
        if (empty($export['data'])) return;

        return Excel::download(new ReportExport($export['data']), $export['filename']);
    }

    public function backupToLocal()
    {
        try {
            $export = $this->prepareExportData();
            if (empty($export['data'])) {
                $this->dispatch('upload-error', message: 'Tidak ada data untuk dibackup.');
                return;
            }

            // 1. Generate Excel content
            // Use Excel::raw to get content strings, then write manually to ensure path correctness
            $fileName = $export['filename'];
            $tempPath = storage_path('app/temp/' . $fileName);
            
            // Ensure temp dir exists
            $tempDir = dirname($tempPath);
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $content = Excel::raw(new ReportExport($export['data']), \Maatwebsite\Excel\Excel::XLSX);
            file_put_contents($tempPath, $content);

            if (!file_exists($tempPath)) {
                throw new \Exception("Gagal membuat file temporary: $tempPath");
            }

            // 2. Determine Local Destination Path
            // Mapping Report Key to Folder Name
            $folderMap = [
                'shift_harian' => 'Shift Harian Kasir',
                'stok_realtime' => 'Stok Real-time',
                'penjualan_periode' => 'Penjualan Periode',
                'fifo_pnl' => 'PnL FIFO',
                'hutang_outstanding' => 'Hutang Outstanding',
                'pembayaran_hutang' => 'Riwayat Pembayaran',
                'rekap_shift' => 'Rekap Shift',
                'fifo_compliance' => 'FIFO Compliance',
                'inventory_valuation' => 'Inventory Valuation',
                'debt_aging' => 'Aging Hutang',
                'cashier_performance' => 'Performa Kasir',
                'slow_moving_stock' => 'Slow Moving Stock',
            ];

            $folderName = $folderMap[$this->selectedReport] ?? 'Lainnya';
            $localBasePath = config('custom_backup.destinations.local') . DIRECTORY_SEPARATOR . 'reports';
            $targetFolder = $localBasePath . DIRECTORY_SEPARATOR . $folderName;
            
            // 3. Create Target Directory if not exists
            if (!file_exists($targetFolder)) {
                if (!@mkdir($targetFolder, 0777, true)) {
                    $error = error_get_last();
                    throw new \Exception("Gagal membuat folder backup lokal. Path: $localBasePath. (" . ($error['message'] ?? '') . ")");
                }
            }

            // 4. Copy File
            $destination = $targetFolder . DIRECTORY_SEPARATOR . $fileName;

            if (copy($tempPath, $destination)) {
                // Cleanup temp
                @unlink($tempPath);
                $this->dispatch('upload-success', message: "Berhasil backup ke: $targetFolder");
            } else {
                throw new \Exception("Gagal menyalin file ke folder backup.");
            }

        } catch (\Exception $e) {
            $this->dispatch('upload-error', message: 'Backup Gagal: ' . $e->getMessage());
        }
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
