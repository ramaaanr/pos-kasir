<div class="space-y-8 animate-fade-in">
    @if(!$part || $part === 'stats')
    {{-- Monthly Business Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Revenue Card --}}
        <div class="bg-card border border-border/50 p-6 rounded-3xl shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <x-lucide-trending-up class="w-24 h-24 text-primary" />
            </div>
            <div class="relative z-10">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground">Omzet (Bulan Ini)</span>
                <p class="text-3xl font-black text-foreground mt-2">Rp {{ number_format($monthlyStats['revenue'], 0, ',', '.') }}</p>
                <div class="flex items-center gap-2 mt-4">
                    <span class="text-[10px] font-bold text-primary bg-primary/10 px-2 py-0.5 rounded-full">Bulan Berjalan</span>
                </div>
            </div>
        </div>

        {{-- COGS Card --}}
        <div class="bg-card border border-border/50 p-6 rounded-3xl shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <x-lucide-shopping-bag class="w-24 h-24 text-rose-500" />
            </div>
            <div class="relative z-10">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground">COGS (Modal Terjual)</span>
                <p class="text-3xl font-black text-foreground mt-2">Rp {{ number_format($monthlyStats['cogs'], 0, ',', '.') }}</p>
                <p class="text-[10px] text-muted-foreground mt-4 italic font-medium">Berdasarkan perhitungan FIFO</p>
            </div>
        </div>

        {{-- Margin Card --}}
        <div class="bg-card border border-border/50 p-6 rounded-3xl shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <x-lucide-bar-chart-3 class="w-24 h-24 text-emerald-500" />
            </div>
            <div class="relative z-10">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground">Margin (Profit %)</span>
                <p class="text-3xl font-black text-emerald-600 mt-2">{{ number_format($monthlyStats['margin'], 1) }}%</p>
                <div class="flex items-center gap-2 mt-4 text-[10px] font-bold text-muted-foreground">
                    <span>Laba Bersih:</span>
                    <span class="text-emerald-600 font-black">Rp {{ number_format($monthlyStats['profit'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Accrual Profit Card --}}
        @php $accrualProfit = $monthlyStats['accrual_profit'] ?? 0; $outstandingDebt = $monthlyStats['outstanding_debt'] ?? 0; @endphp
        <div class="border p-6 rounded-3xl shadow-sm relative overflow-hidden group {{ $accrualProfit >= 0 ? 'bg-violet-500/5 border-violet-500/20' : 'bg-rose-500/5 border-rose-500/20' }}">
            <div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-110 transition-transform duration-700">
                <x-lucide-wallet class="w-24 h-24 {{ $accrualProfit >= 0 ? 'text-violet-500' : 'text-rose-500' }}" />
            </div>
            <div class="relative z-10">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground">Accrual Profit</span>
                <p class="text-3xl font-black mt-2 {{ $accrualProfit >= 0 ? 'text-violet-600' : 'text-rose-600' }}">Rp {{ number_format($accrualProfit, 0, ',', '.') }}</p>
                <div class="flex items-center gap-2 mt-4 text-[10px] font-bold text-muted-foreground">
                    <span class="text-rose-500">− Rp {{ number_format($outstandingDebt, 0, ',', '.') }}</span>
                    <span>hutang bulan ini</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(!$part || $part === 'activity')
    {{-- Recent Activity Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Recent Sales --}}
        <div class="bg-card border border-border/50 rounded-3xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-border/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-primary/10 text-primary">
                        <x-lucide-shopping-cart class="w-5 h-5" />
                    </div>
                    <h3 class="font-black text-foreground tracking-tight">Penjualan Terakhir</h3>
                </div>
                <a href="{{ route('admin.sales.index') }}" class="text-[10px] font-black text-primary uppercase hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-muted/30 border-b border-border">
                            <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Invoice</th>
                            <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Waktu</th>
                            <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($recentSales as $sale)
                        <tr class="hover:bg-muted/10 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.sales.detail', $sale->invoice_number) }}" class="font-mono font-bold text-primary hover:underline">{{ $sale->invoice_number }}</a>
                            </td>
                            <td class="px-6 py-4 text-muted-foreground">{{ $sale->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-right font-black">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-muted-foreground italic">Belum ada transaksi hari ini</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Recent Debts --}}
        <div class="bg-card border border-border/50 rounded-3xl shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-border/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-orange-500/10 text-orange-600">
                        <x-lucide-scroll-text class="w-5 h-5" />
                    </div>
                    <h3 class="font-black text-foreground tracking-tight">Hutang Outstanding Terbaru</h3>
                </div>
                <a href="{{ route('admin.debts.index') }}" class="text-[10px] font-black text-primary uppercase hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-muted/30 border-b border-border">
                            <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Customer</th>
                            <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Umur</th>
                            <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($recentDebts as $debt)
                        <tr class="hover:bg-muted/10 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-foreground">{{ $debt->customer->nama ?? 'Guest' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php $days = now()->diffInDays($debt->created_at); @endphp
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $days > 30 ? 'bg-rose-500/10 text-rose-600' : 'bg-orange-500/10 text-orange-600' }}">
                                    {{ $days }} Hari
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-black text-rose-600">Rp {{ number_format($debt->amount - $debt->payments->sum('amount'), 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-muted-foreground italic">Tidak ada piutang aktif</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
