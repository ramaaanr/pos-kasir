<div class="space-y-6 animate-fade-in">
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }

            #invoice-print,
            #invoice-print * {
                visibility: visible !important;
            }

            #invoice-print {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
            }
        }
    </style>
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Riwayat Penjualan</h2>
            <p class="text-muted-foreground text-sm mt-1">Kelola dan lihat data transaksi</p>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-card border border-border/50 rounded-xl p-4 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
            {{-- Search --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-muted-foreground">
                    <x-lucide-search class="h-4 w-4" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nomor invoice..."
                    class="flex h-10 w-full rounded-lg border border-input bg-background/50 pl-10 pr-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="rounded-xl border border-border/50 bg-card overflow-hidden shadow-sm flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-border bg-muted/30">
                        <th class="px-4 py-3 font-semibold text-muted-foreground">Invoice</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-right">Total</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-right">Terbayar</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-center">Cash (Rec/Change)</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-center">Metode</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground">Kasir</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-muted/5 transition-colors group">
                        <td class="px-4 py-4">
                            <span class="font-mono font-bold text-primary">{{ $sale->invoice_number }}</span>
                        </td>
                        <td class="px-4 py-4 text-right font-black">
                            Rp {{ number_format($sale->total, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right font-bold text-emerald-600">
                            Rp {{ number_format($sale->total_paid, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($sale->payment_method === 'cash')
                            <div class="flex flex-col leading-tight">
                                <span class="text-xs font-bold text-foreground">Rp {{ number_format($sale->cash_received, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-muted-foreground font-medium">Kemb: Rp {{ number_format($sale->cash_change, 0, ',', '.') }}</span>
                            </div>
                            @else
                            <span class="text-muted-foreground opacity-30 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-tight
                                {{ $sale->payment_method === 'cash' ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-orange-500/10 text-orange-600 border border-orange-500/20' }}">
                                {{ $sale->payment_method }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-tight
                                {{ $sale->status === 'completed' ? 'bg-blue-500/10 text-blue-600 border border-blue-500/20' : 'bg-amber-500/10 text-amber-600 border border-amber-500/20' }}">
                                {{ $sale->status }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-xs text-muted-foreground">
                            {{ $sale->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-4 text-sm font-medium">
                            {{ $sale->user->name ?? '-' }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            <button wire:click="openDetail('{{ $sale->invoice_number }}')" class="p-2 hover:bg-primary/10 rounded-lg text-primary transition-colors hover:scale-105 duration-200">
                                <x-lucide-eye class="h-4 w-4" />
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada data penjualan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
        <div class="px-4 py-4 bg-muted/10 border-t border-border">
            {{ $sales->links() }}
        </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedSale)
    <div class="fixed inset-0 z-[100] overflow-y-auto" wire:keydown.escape.window="closeDetail">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-background/80 backdrop-blur-md transition-opacity" wire:click="closeDetail"></div>
            
            <div class="relative bg-card w-full max-w-2xl rounded-3xl shadow-2xl border border-border overflow-hidden animate-in zoom-in duration-300">
                {{-- Modal Header --}}
                <div class="p-6 border-b border-border bg-muted/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-2xl bg-primary/10 text-primary">
                            <x-lucide-file-text class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-foreground tracking-tight">Detail Sales</h3>
                            <p class="text-[10px] text-muted-foreground uppercase font-black tracking-widest">{{ $selectedSale->invoice_number }}</p>
                        </div>
                    </div>
                    <button wire:click="closeDetail" class="p-2 hover:bg-muted rounded-full transition-colors text-muted-foreground hover:text-foreground">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-8">
                    {{-- Basic Info --}}
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest mb-1">Customer</h4>
                                <p class="text-sm font-bold text-foreground">{{ $selectedSale->customer->nama ?? 'Umum (Walk-in)' }}</p>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest mb-1">Kasir</h4>
                                <p class="text-sm font-bold text-foreground">{{ $selectedSale->user->name ?? 'System' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-right">
                            <div>
                                <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest mb-1">Tanggal Transaksi</h4>
                                <p class="text-sm font-bold text-foreground">{{ $selectedSale->created_at->translatedFormat('d F Y, H:i') }}</p>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest mb-1">Metode Pembayaran</h4>
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $selectedSale->payment_method === 'cash' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-orange-500/10 text-orange-600' }}">
                                    {{ $selectedSale->payment_method }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="space-y-3">
                        <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest flex items-center gap-2">
                            <x-lucide-box class="w-3 h-3" />
                            Produk Terjual
                        </h4>
                        <div class="rounded-2xl border border-border/50 overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-muted/30 border-b border-border">
                                    <tr>
                                        <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Produk</th>
                                        <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Qty</th>
                                        <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Harga Satuan</th>
                                        <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    @foreach($selectedSale->items as $item)
                                    <tr class="hover:bg-muted/5 transition-colors">
                                        <td class="px-4 py-3 font-bold text-foreground">
                                            {{ $item->product->nama ?? 'Produk Dihapus' }}
                                            @if($item->unit_label)
                                            <span class="ml-1 text-[10px] text-muted-foreground font-medium">({{ $item->unit_label }})</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium">
                                            {{ (float)($item->qty_base / $item->unit_multiplier) }} {{ $item->unit_label ?: ($item->product->base_unit ?? '') }}
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            Rp {{ number_format($item->harga_jual_per_unit, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold text-foreground">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-muted/10 border-t border-border">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-right font-black uppercase tracking-widest text-[9px] text-muted-foreground">Total Keseluruhan</td>
                                        <td class="px-4 py-3 text-right font-black text-primary text-base">
                                            Rp {{ number_format($selectedSale->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-6 border-t border-border bg-muted/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <button wire:click="printInvoice" class="flex items-center gap-2 px-6 py-2.5 bg-orange-500 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-orange-500/20 hover:bg-orange-600 transition-all active:scale-95">
                            <x-lucide-printer class="w-4 h-4" />
                            Cetak Invoice
                        </button>
                        @if($selectedSale->payment_method === 'debt')
                        <a href="{{ route('kasir.debt', ['customer_id' => $selectedSale->customer_id]) }}" class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all active:scale-95">
                            <x-lucide-credit-card class="w-4 h-4" />
                            Detail Hutang
                        </a>
                        @endif
                    </div>
                    <button wire:click="closeDetail" class="px-8 py-2.5 bg-muted text-foreground font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-muted/80 transition-all active:scale-95 border border-border">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Hidden Print Template (Aligned with POS Template) --}}

</div>
