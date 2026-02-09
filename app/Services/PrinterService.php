<?php

namespace App\Services;

use App\Models\Sale;
use Exception;
use Illuminate\Support\Facades\Log;

class PrinterService
{
    protected $printerName = 'POS-58';
    protected $paperWidth = 32;

    public function printInvoice(Sale $sale)
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

        // Header (Center)
        $content .= $this->centerText("UD.SEPAN");
        $content .= $this->centerText("JL. ANTANG JUNGAN");
        $content .= $this->centerText("KECAMATAN RUNGAN HULU");
        $content .= $this->centerText("KABUPATEN GUNUNG MAS");
        $content .= str_repeat('-', $this->paperWidth) . "\n";
        $content .= $this->centerText("INVOICE");
        $content .= "\n";

        // Info
        $content .= "No : " . ($sale->invoice_number ?? '-') . "\n";
        $content .= "Tgl: " . $sale->created_at->format('d/m/Y H:i') . "\n";
        $content .= "Ksr: " . ($sale->user->name ?? 'System') . "\n";
        $content .= "Byr: " . strtoupper($sale->payment_method ?? 'CASH') . "\n";
        $content .= str_repeat('-', $this->paperWidth) . "\n";

        // Items
        if ($sale->items && $sale->items->count() > 0) {
            foreach ($sale->items as $item) {
                $name = $item->product->nama ?? 'Produk';
                $qty = ($item->qty_base ?? 0) / ($item->unit_multiplier ?? 1);
                $unit = $item->unit_label ?? 'pcs';
                $price = ($item->harga_jual_per_unit ?? 0) * ($item->unit_multiplier ?? 1);
                $subtotal = $item->subtotal ?? 0;

                // Potong nama jika terlalu panjang
                if (mb_strlen($name) > $this->paperWidth) {
                    $name = mb_substr($name, 0, $this->paperWidth - 3) . "...";
                }

                $content .= $name . "\n";

                $qtyStr = number_format((float)$qty, 0, ',', '.');
                $priceStr = number_format((float)$price, 0, ',', '.');
                $subtotalStr = number_format((float)$subtotal, 0, ',', '.');

                $leftPart = "$qtyStr $unit x $priceStr";
                $content .= $this->textToRight($leftPart, $subtotalStr);
            }
        } else {
            $content .= "(Tidak ada item)\n";
        }

        $content .= str_repeat('-', $this->paperWidth) . "\n";

        // Totals
        $total = number_format((float)($sale->total ?? 0), 0, ',', '.');
        $content .= $this->textToRight("TOTAL", "Rp $total");

        if ($sale->payment_method === 'cash') {
            $paid = number_format((float)($sale->cash_received ?? 0), 0, ',', '.');
            $change = number_format((float)($sale->cash_change ?? 0), 0, ',', '.');

            $content .= $this->textToRight("BAYAR", "Rp $paid");
            $content .= $this->textToRight("KEMBALI", "Rp $change");
        } else {
            $customer = $sale->customer->nama ?? '-';
            $paid = number_format((float)($sale->total_paid ?? 0), 0, ',', '.');
            $debt = number_format((float)(($sale->total ?? 0) - ($sale->total_paid ?? 0)), 0, ',', '.');

            $content .= "Pelanggan: $customer\n";
            $content .= $this->textToRight("BAYAR (DP)", "Rp $paid");
            $content .= $this->textToRight("SISA HUTANG", "Rp $debt");
        }

        // Footer
        $content .= "\n";
        $content .= $this->centerText("Terima Kasih");
        $content .= $this->centerText("Barang yang sudah dibeli");
        $content .= $this->centerText("tidak dapat ditukar/dikembalikan");
        $content .= "\n\n\n";

        return $content;
    }

    protected function printViaPowerShell($content)
    {
        try {
            $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'invoice_' . uniqid() . '.txt';

            // Tulis content ke file
            file_put_contents($tempFile, $content);

            // Print menggunakan PowerShell Out-Printer
            $command = 'powershell -Command "Get-Content \"' . $tempFile . '\" -Raw | Out-Printer -Name \"' . $this->printerName . '\""';

            exec($command, $output, $return);

            // Tunggu sebentar sebelum hapus file
            sleep(1);

            // Hapus temporary file
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }

            if ($return !== 0) {
                throw new Exception("Print command failed with return code: $return");
            }

            return true;
        } catch (Exception $e) {
            throw new Exception("PowerShell print error: " . $e->getMessage());
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
