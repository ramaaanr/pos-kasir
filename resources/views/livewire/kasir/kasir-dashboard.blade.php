<div class="space-y-8">
    {{-- Shift Management Alerts --}}
    @if(!$activeShift)
        <div class="bg-amber-500/10 border border-amber-500/20 p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative group">
            <div class="relative z-10 flex items-center gap-5">
                <div class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/40 animate-pulse">
                    <x-lucide-clock class="w-7 h-7 text-white" />
                </div>
                <div>
                    <h2 class="text-xl font-black text-amber-900 dark:text-amber-100">Shift Belum Dimulai</h2>
                    <p class="text-sm text-amber-700/80 dark:text-amber-400 font-medium">Anda perlu memasukkan Saldo Awal sebelum melakukan transaksi.</p>
                </div>
            </div>
            <button wire:click="$set('showOpeningModal', true)" class="relative z-10 px-8 py-3.5 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-2xl shadow-xl shadow-amber-500/20 transition-all hover:scale-105 active:scale-95 flex items-center gap-2">
                <span>Mulai Shift Sekarang</span>
                <x-lucide-play class="w-4 h-4 fill-current" />
            </button>
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-amber-500/5 rounded-full blur-3xl"></div>
        </div>
    @else
        <div class="bg-emerald-500/10 border border-emerald-500/20 p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative">
            <div class="relative z-10 flex items-center gap-5">
                <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/40">
                    <x-lucide-check-circle class="w-7 h-7 text-white" />
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black text-emerald-900 dark:text-emerald-100">Shift Sedang Aktif</h2>
                        <span class="px-2 py-0.5 bg-emerald-500 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">Live</span>
                    </div>
                    <p class="text-sm text-emerald-700/80 dark:text-emerald-400 font-medium italic">
                        Dimulai sejak {{ $activeShift->start_time->format('H:i') }} • Saldo Awal: <span class="font-bold">Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Sistem Record (Cash)</p>
                    <p class="text-lg font-black text-foreground">Rp {{ number_format($systemCash, 0, ',', '.') }}</p>
                </div>
                <button wire:click="$set('showClosingModal', true)" class="relative z-10 px-8 py-3.5 bg-white dark:bg-zinc-800 border border-emerald-500/30 hover:border-emerald-500 text-emerald-600 dark:text-emerald-400 font-black rounded-2xl shadow-xl shadow-emerald-500/5 transition-all hover:scale-105 active:scale-95 flex items-center gap-2 group">
                    <span>Tutup Shift</span>
                    <x-lucide-log-out class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                </button>
            </div>
        </div>
    @endif

    {{-- Opening Cash Modal --}}
    @if($showOpeningModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-sm animate-in fade-in duration-300">
        <div class="bg-card w-full max-w-md rounded-[2.5rem] border border-border shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300">
            <div class="p-8 pb-4">
                <div class="w-16 h-16 bg-amber-500/10 rounded-2xl flex items-center justify-center mb-6">
                    <x-lucide-landmark class="w-8 h-8 text-amber-500" />
                </div>
                <h3 class="text-2xl font-black text-foreground tracking-tight">Saldo Awal Kasir</h3>
                <p class="text-muted-foreground text-sm font-medium mt-1">Masukkan jumlah tunai yang ada di laci sebelum memulai penjualan.</p>
                
                <div class="mt-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-3 ml-1">Jumlah Saldo Awal (Tunai)</label>
                        <div class="relative group">
                            <div class="absolute left-5 top-1/2 -translate-y-1/2 text-xl font-black text-muted-foreground group-focus-within:text-amber-500 transition-colors">Rp</div>
                            <input type="number" wire:model="openingCash" autofocus class="w-full bg-muted/50 border-2 border-transparent focus:border-amber-500 focus:bg-card rounded-2xl py-4 pl-14 pr-6 text-xl font-black transition-all outline-none" placeholder="0">
                        </div>
                        @error('openingCash') <span class="text-red-500 text-xs font-bold mt-2 block ml-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="p-8 pt-4">
                <button wire:click="startShift" class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-white font-black rounded-2xl shadow-xl shadow-amber-500/20 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                    <span>Mulai Bekerja Sekarang</span>
                    <x-lucide-chevron-right class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Closing Cash Modal --}}
    @if($showClosingModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-sm animate-in fade-in duration-300">
        <div class="bg-card w-full max-w-md rounded-[2.5rem] border border-border shadow-2xl overflow-hidden animate-in zoom-in-95 duration-300">
            <div class="p-8 pb-4">
                <div class="w-16 h-16 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6">
                    <x-lucide-box class="w-8 h-8 text-emerald-500" />
                </div>
                <h3 class="text-2xl font-black text-foreground tracking-tight">Tutup Shift & Rekap</h3>
                <p class="text-muted-foreground text-sm font-medium mt-1">Hitung fisik uang tunai di laci dan masukkan jumlahnya di bawah.</p>
                
                <div class="mt-8 space-y-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 rounded-2xl bg-muted/30 border border-border/50">
                            <p class="text-[9px] font-black text-muted-foreground uppercase tracking-wider">Saldo Awal</p>
                            <p class="text-sm font-bold mt-1">Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 rounded-2xl bg-blue-500/5 border border-blue-500/20">
                            <p class="text-[9px] font-black text-blue-500 uppercase tracking-wider">Masuk (System)</p>
                            <p class="text-sm font-bold text-blue-600 mt-1">+Rp {{ number_format($systemCash, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-span-2 p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/20 flex justify-between items-center">
                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-wider">Total Seharusnya</p>
                            <p class="text-lg font-black text-emerald-600">Rp {{ number_format($activeShift->opening_cash + $systemCash, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-3 ml-1">Uang Tunai di Laci (Fisik)</label>
                        <div class="relative group">
                            <div class="absolute left-5 top-1/2 -translate-y-1/2 text-xl font-black text-muted-foreground group-focus-within:text-emerald-500 transition-colors">Rp</div>
                            <input type="number" wire:model="cashInDrawer" autofocus class="w-full bg-muted/50 border-2 border-transparent focus:border-emerald-500 focus:bg-card rounded-2xl py-4 pl-14 pr-6 text-xl font-black transition-all outline-none" placeholder="0">
                        </div>
                        @error('cashInDrawer') <span class="text-red-500 text-xs font-bold mt-2 block ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-3 ml-1">Catatan Tambahan (Opsional)</label>
                        <textarea wire:model="note" class="w-full bg-muted/50 border-2 border-transparent focus:border-emerald-500 focus:bg-card rounded-2xl py-3 px-4 text-sm font-medium transition-all outline-none" rows="2" placeholder="Contoh: Ada selisih karena pembulatan..."></textarea>
                    </div>
                </div>
            </div>
            <div class="p-8 pt-4 flex gap-3">
                <button wire:click="$set('showClosingModal', false)" class="flex-1 py-4 bg-muted text-muted-foreground font-black rounded-2xl hover:bg-muted/80 transition-all">
                    Batal
                </button>
                <button wire:click="closeShift" class="flex-[2] py-4 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-2xl shadow-xl shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                    <span>Finalisasi & Tutup</span>
                    <x-lucide-check class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
    @endif

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
