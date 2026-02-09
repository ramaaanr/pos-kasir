<?php

namespace App\Services;

use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use App\Models\Sale;
use Exception;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    protected $printer;
    protected $paperWidth = 32; // 58mm paper = 32 characters

    public function printInvoice(Sale $sale)
    {
        try {
            // PENTING: Gunakan nama printer lokal, BUKAN network path
            // Nama printer harus sesuai dengan yang ada di "Devices and Printers"
            $printerName = 'POS-58';
            
            $connector = new WindowsPrintConnector($printerName);
            $this->printer = new Printer($connector);

            $this->printHeader();
            $this->printInfo($sale);
            $this->printItems($sale);
            $this->printTotals($sale);
            $this->printFooter();

            // Cut paper
            $this->printer->cut();
            
            // Close printer connection
            $this->printer->close();
            
            return true;
            
        } catch (Exception $e) {
            Log::error("Printing failed: " . $e->getMessage());
            Log::error("Printer name: " . ($printerName ?? 'not set'));
            
            // Pastikan printer ditutup meskipun error
            if (isset($this->printer)) {
                try {
                    $this->printer->close();
                } catch (Exception $closeError) {
                    // Ignore close error
                }
            }
            
            throw new Exception("Gagal mencetak: " . $e->getMessage());
        }
    }

    protected function printHeader()
    {
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->setEmphasis(true);
        $this->printer->text("UD.SEPAN\n");
        $this->printer->setEmphasis(false);
        $this->printer->setTextSize(1, 1);
        $this->printer->text("JL. ANTANG JUNGAN\n");
        $this->printer->text("KECAMATAN RUNGAN HULU\n");
        $this->printer->text("KABUPATEN GUNUNG MAS\n");
        $this->printer->text(str_repeat('-', $this->paperWidth) . "\n");
        $this->printer->setEmphasis(true);
        $this->printer->text("INVOICE\n");
        $this->printer->setEmphasis(false);
        $this->printer->feed();
    }

    protected function printInfo(Sale $sale)
    {
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        $this->printer->text("No : " . ($sale->invoice_number ?? '-') . "\n");
        $this->printer->text("Tgl: " . $sale->created_at->format('d/m/Y H:i') . "\n");
        $this->printer->text("Ksr: " . ($sale->user->name ?? 'System') . "\n");
        $this->printer->text("Byr: " . strtoupper($sale->payment_method ?? 'CASH') . "\n");
        $this->printer->text(str_repeat('-', $this->paperWidth) . "\n");
    }

    protected function printItems(Sale $sale)
    {
        $this->printer->setJustification(Printer::JUSTIFY_LEFT);
        
        foreach ($sale->items as $item) {
            $name = $item->product->nama ?? 'Produk';
            $qty = ($item->qty_base ?? 0) / ($item->unit_multiplier ?? 1);
            $unit = $item->unit_label ?? 'pcs';
            $price = ($item->harga_jual_per_unit ?? 0) * ($item->unit_multiplier ?? 1);
            $subtotal = $item->subtotal ?? 0;

            // Print product name (bold)
            $this->printer->setEmphasis(true);
            
            // Potong nama produk jika terlalu panjang
            if (strlen($name) > $this->paperWidth) {
                $name = substr($name, 0, $this->paperWidth - 3) . "...";
            }
            
            $this->printer->text("$name\n");
            $this->printer->setEmphasis(false);
            
            // Format qty dan price - FIX: Handle null values
            $qtyStr = number_format((float)$qty, 0, ',', '.');
            $priceStr = number_format((float)$price, 0, ',', '.');
            $subtotalStr = number_format((float)$subtotal, 0, ',', '.');
            
            // Format: "2 Pcs x 50.000      100.000"
            $leftPart = "$qtyStr $unit x $priceStr";
            
            $this->textToRight($leftPart, $subtotalStr);
        }
        
        $this->printer->text(str_repeat('-', $this->paperWidth) . "\n");
    }

    protected function printTotals(Sale $sale)
    {
        // FIX: Handle null values dengan default 0
        $total = number_format((float)($sale->total ?? 0), 0, ',', '.');
        $this->textToRight("TOTAL", "Rp $total", true);

        if ($sale->payment_method === 'cash') {
            $paid = number_format((float)($sale->cash_received ?? 0), 0, ',', '.');
            $change = number_format((float)($sale->cash_change ?? 0), 0, ',', '.');
            
            $this->textToRight("BAYAR", "Rp $paid");
            $this->textToRight("KEMBALI", "Rp $change", true);
        } else {
            // Debt
            $customer = $sale->customer->nama ?? '-';
            $paid = number_format((float)($sale->total_paid ?? 0), 0, ',', '.');
            $debt = number_format((float)(($sale->total ?? 0) - ($sale->total_paid ?? 0)), 0, ',', '.');

            $this->printer->text("Pelanggan: $customer\n");
            $this->textToRight("BAYAR (DP)", "Rp $paid");
            $this->textToRight("SISA HUTANG", "Rp $debt", true);
        }
    }

    protected function printFooter()
    {
        $this->printer->feed();
        $this->printer->setJustification(Printer::JUSTIFY_CENTER);
        $this->printer->text("Terima Kasih\n");
        $this->printer->text("Barang yang sudah dibeli\n");
        $this->printer->text("tidak dapat ditukar/dikembalikan\n");
        $this->printer->feed(3);
    }

    /**
     * Helper untuk align text ke kanan
     */
    private function textToRight($left, $right, $bold = false)
    {
        if ($bold) {
            $this->printer->setEmphasis(true);
        }
        
        // Hitung panjang text
        $lenLeft = mb_strlen($left);
        $lenRight = mb_strlen($right);
        
        // Hitung jumlah spasi yang dibutuhkan
        $spaces = $this->paperWidth - $lenLeft - $lenRight;
        
        // Minimal 1 spasi
        if ($spaces < 1) {
            $spaces = 1;
        }
        
        $this->printer->text($left . str_repeat(' ', $spaces) . $right . "\n");
        
        if ($bold) {
            $this->printer->setEmphasis(false);
        }
    }
}