<?php

namespace App\Services;

use App\Models\Sale;
use Exception;
use Illuminate\Support\Facades\Log;

class PrinterService
{
   protected $printerName = 'POS-58';
    protected $paperWidth = 28; // Tetap 28 agar ada sisa ruang di kanan
    protected $leftMargin = "  "; // Tambahkan 2 spasi sebagai margin kiri (~2mm)

    public function printInvoice($sale)
    {
        try {
            $content = $this->buildInvoiceContent($sale);
            return $this->printViaPowerShell($content);
        } catch (Exception $e) {
            Log::error("Printing failed: " . $e->getMessage());
            throw new Exception("Gagal mencetak: " . $e->getMessage());
        }
    }

    protected function buildInvoiceContent(Sale $sale)
    {
        $content = "";
        $m = $this->leftMargin; // Shortcut

        // Header (Center)
        $content .= $m . $this->centerText("UD.SEPAN");
        $content .= $m . $this->centerText("JL. ANTANG JUNGAN");
        $content .= $m . $this->centerText("KECAMATAN RUNGAN HULU");
        $content .= $m . $this->centerText("KABUPATEN GUNUNG MAS");
        $content .= $m . str_repeat('-', $this->paperWidth) . "\n";
        $content .= $m . $this->centerText("INVOICE");
        $content .= "\n";

        // Info
        $content .= $m . "No : " . ($sale->invoice_number ?? '-') . "\n";
        $content .= $m . "Tgl: " . $sale->created_at->format('d/m/Y H:i') . "\n";
        $content .= $m . "Ksr: " . ($sale->user->name ?? 'System') . "\n";
        $content .= $m . "Byr: " . strtoupper($sale->payment_method ?? 'CASH') . "\n";
        $content .= $m . str_repeat('-', $this->paperWidth) . "\n";

        // Items
        if ($sale->items && $sale->items->count() > 0) {
            foreach ($sale->items as $item) {
                $name = $item->product->nama ?? 'Produk';
                $qty = ($item->qty_base ?? 0) / ($item->unit_multiplier ?? 1);
                $unit = $item->unit_label ?? 'pcs';
                $price = ($item->harga_jual_per_unit ?? 0) * ($item->unit_multiplier ?? 1);
                $subtotal = $item->subtotal ?? 0;

                if (mb_strlen($name) > $this->paperWidth) {
                    $name = mb_substr($name, 0, $this->paperWidth - 3) . "...";
                }

                $content .= $m . $name . "\n";

                $qtyStr = number_format((float)$qty, 0, ',', '.');
                $priceStr = number_format((float)$price, 0, ',', '.');
                $subtotalStr = number_format((float)$subtotal, 0, ',', '.');

                $leftPart = "$qtyStr $unit x $priceStr";
                $content .= $m . $this->textToRight($leftPart, $subtotalStr);
            }
        } else {
            $content .= $m . "(Tidak ada item)\n";
        }

        $content .= $m . str_repeat('-', $this->paperWidth) . "\n";

        // Totals
        $total = number_format((float)($sale->total ?? 0), 0, ',', '.');
        $content .= $m . $this->textToRight("TOTAL", "Rp $total");

        if ($sale->payment_method === 'cash') {
            $paid = number_format((float)($sale->cash_received ?? 0), 0, ',', '.');
            $change = number_format((float)($sale->cash_change ?? 0), 0, ',', '.');

            $content .= $m . $this->textToRight("BAYAR", "Rp $paid");
            $content .= $m . $this->textToRight("KEMBALI", "Rp $change");
        } else {
            $customer = $sale->customer->nama ?? '-';
            $paid = number_format((float)($sale->total_paid ?? 0), 0, ',', '.');
            $debt = number_format((float)(($sale->total ?? 0) - ($sale->total_paid ?? 0)), 0, ',', '.');

            $content .= $m . "Pelanggan: $customer\n";
            $content .= $m . $this->textToRight("BAYAR (DP)", "Rp $paid");
            $content .= $m . $this->textToRight("SISA HUTANG", "Rp $debt");
        }

        // Footer
        $content .= "\n";
        $content .= $m . $this->centerText("Terima Kasih");
        $content .= $m . $this->centerText("Barang yang sudah dibeli");
        $content .= $m . $this->centerText("tidak dapat ditukar/dikembalikan");
        $content .= "\n\n\n";

        return $content;
    }

   protected function printViaPowerShell($content)
{
    try {
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'invoice.txt';
        
        // Gunakan encoding CP437 agar karakter garis (-) dan spasi dibaca tepat oleh printer thermal
        $encodedContent = iconv("UTF-8", "CP437//IGNORE", $content);
        file_put_contents($tempFile, $encodedContent . "\n\n\n\n");

        $printerPath = "\\\\127.0.0.1\\{$this->printerName}";
        
        // Jalankan COPY /B (Ini bypass margin Windows sepenuhnya)
        $command = "cmd /c copy /b \"$tempFile\" \"$printerPath\"";
        exec($command, $output, $return);

        // JIKA COPY /B gagal (Access Denied), gunakan cara ini sebagai cadangan:
        if ($return !== 0) {
            // Kita gunakan [System.IO.File]::WriteAllText untuk mengirim RAW data
            $command = "powershell -Command \"get-content '$tempFile' | Out-Printer -Name '{$this->printerName}'\"";
            exec($command, $output, $return);
        }

        if (file_exists($tempFile)) { unlink($tempFile); }
        return true;
    } catch (Exception $e) {
        throw new Exception("Print error: " . $e->getMessage());
    }
}

    protected function centerText($text)
    {
        $padding = floor(($this->paperWidth - mb_strlen($text)) / 2);
        return str_repeat(' ', max(0, $padding)) . $text . "\n";
    }

    protected function textToRight($left, $right)
    {
        $lenLeft = mb_strlen($left);
        $lenRight = mb_strlen($right);
        $spaces = $this->paperWidth - $lenLeft - $lenRight;

        if ($spaces < 1) {
            $spaces = 1;
        }

        return $left . str_repeat(' ', $spaces) . $right . "\n";
    }
}
