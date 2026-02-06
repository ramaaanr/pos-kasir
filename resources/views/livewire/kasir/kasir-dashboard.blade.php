<div class="space-y-8">
    {{-- Quick Action & Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        {{-- Card 1: Transaksi Baru --}}
        <a href="{{ route('kasir.pos') }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 p-6 text-white shadow-lg transition-all hover:scale-[1.02] hover:shadow-xl">
            <div class="relative z-10 flex flex-col justify-between h-full min-h-[160px]">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-xl font-bold">Transaksi Baru</h3>
                        <p class="text-emerald-100 text-sm mt-1">Mulai penjualan POS</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <x-lucide-shopping-cart class="h-8 w-8 text-white" />
                    </div>
                </div>
                
                <div class="flex items-center gap-2 text-sm font-medium bg-white/20 w-fit px-4 py-2 rounded-lg backdrop-blur-sm group-hover:bg-white/30 transition-colors">
                    <span>Mulai Sekarang</span>
                    <x-lucide-arrow-right class="h-4 w-4" />
                </div>
            </div>
            
            {{-- Decorative Pattern --}}
            <div class="absolute -bottom-4 -right-4 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute top-0 right-0 h-full w-1/2 bg-gradient-to-l from-white/5 to-transparent"></div>
        </a>

        {{-- Card 2: Pembayaran Hutang --}}
        <a href="{{ route('kasir.debt') }}" class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 text-white shadow-lg transition-all hover:scale-[1.02] hover:shadow-xl">
            <div class="relative z-10 flex flex-col justify-between h-full min-h-[160px]">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-xl font-bold">Pembayaran Hutang</h3>
                        <p class="text-amber-100 text-sm mt-1">Kelola piutang pelanggan</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <x-lucide-wallet class="h-8 w-8 text-white" />
                    </div>
                </div>
                
                <div class="flex items-center gap-2 text-sm font-medium bg-white/20 w-fit px-4 py-2 rounded-lg backdrop-blur-sm group-hover:bg-white/30 transition-colors">
                    <span>Lihat Daftar</span>
                    <x-lucide-arrow-right class="h-4 w-4" />
                </div>
            </div>
            
            {{-- Decorative Pattern --}}
            <div class="absolute -bottom-4 -right-4 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        </a>

        {{-- Card 3: Quick Stats --}}
        <div class="rounded-2xl bg-card border border-border/50 p-6 shadow-sm flex flex-col justify-between min-h-[160px]">
            <div>
                <h3 class="text-base font-semibold text-muted-foreground uppercase tracking-wider text-xs">Statistik Hari Ini</h3>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-4xl font-extrabold text-foreground">{{ $todayTransactionCount }}</span>
                    <span class="text-sm text-muted-foreground">Transaksi</span>
                </div>
            </div>
            
            <div class="pt-4 border-t border-border/50 mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-muted-foreground">Total Penjualan</span>
                    <span class="text-lg font-bold text-foreground">Rp {{ number_format($todayTotalSales, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions Table --}}
    <div class="bg-card rounded-2xl border border-border/50 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-border/50 flex items-center justify-between">
            <h3 class="font-bold text-lg">Transaksi Terakhir</h3>
            <a href="#" class="text-sm text-primary hover:underline">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-muted/50 text-xs uppercase text-muted-foreground font-semibold">
                    <tr>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/50">
                    @forelse($recentTransactions as $sale)
                        <tr class="hover:bg-muted/5 transition-colors">
                            <td class="px-6 py-4 font-mono font-medium text-primary">
                                {{ $sale->invoice_number }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $sale->customer->nama ?? 'Umum' }}
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">
                                {{ $sale->created_at->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-foreground">
                                Rp {{ number_format($sale->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($sale->payment_method === 'debt' || $sale->status === 'pending_payment') 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-500">
                                        Hutang
                                    </span>
                                @elseif($sale->status === 'cancelled')
                                     <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-500">
                                        Batal
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-500">
                                        Lunas
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-muted-foreground">
                                <div class="flex flex-col items-center gap-2">
                                    <x-lucide-clipboard-list class="h-8 w-8 text-muted-foreground/30" />
                                    <p>Belum ada transaksi hari ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
