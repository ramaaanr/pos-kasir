<div class="space-y-8">
    {{-- Shift Management Alerts --}}
    @if(!$activeShift)
    <div wire:key="shift-alert-none" class="bg-amber-500/10 border border-amber-500/20 p-6 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-6 overflow-hidden relative group">
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
    <div wire:key="shift-alert-active" class="relative overflow-hidden rounded-3xl p-6 border transition-all 
            {{ $isShiftOverdue 
                ? 'bg-rose-500/10 border-rose-500/30' 
                : 'bg-emerald-500/10 border-emerald-500/20' }}">

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg transition-colors
                        {{ $isShiftOverdue 
                            ? 'bg-rose-500 shadow-rose-500/40 animate-pulse' 
                            : 'bg-emerald-500 shadow-emerald-500/40' }}">
                    @if($isShiftOverdue)
                    <x-lucide-alert-triangle class="w-7 h-7 text-white" />
                    @else
                    <x-lucide-check-circle class="w-7 h-7 text-white" />
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-black 
                                {{ $isShiftOverdue ? 'text-rose-950 dark:text-rose-500' : 'text-emerald-950 dark:text-emerald-500' }}">
                            {{ $isShiftOverdue ? 'Shift Segera Tutup!' : 'Shift Sedang Aktif' }}
                        </h2>
                        <span class="px-2 py-0.5 text-white text-[10px] font-black rounded-lg uppercase tracking-widest
                                {{ $isShiftOverdue ? 'bg-rose-500' : 'bg-emerald-500' }}">
                            {{ $isShiftOverdue ? 'Overdue' : 'Live' }}
                        </span>
                    </div>
                    <p class="text-sm font-medium italic 
                            {{ $isShiftOverdue ? 'text-rose-700/80 dark:text-rose-400' : 'text-emerald-900/80 dark:text-emerald-400' }}">
                        {{ $isShiftOverdue ? 'Perhatian: Shift sudah berjalan lebih dari 24 jam. Segera rekap saldo.' : 'Dimulai sejak ' . $activeShift->start_time->format('H:i') . ' • Saldo Awal: ' }}
                        @if(!$isShiftOverdue)
                        <span class="font-bold">Rp {{ number_format($activeShift->opening_cash, 0, ',', '.') }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-[10px] font-black uppercase tracking-widest 
                            {{ $isShiftOverdue ? 'text-rose-600' : 'text-emerald-700' }}">Sistem Record (Cash)</p>
                    <p class="text-lg font-black text-foreground">Rp {{ number_format($systemCash, 0, ',', '.') }}</p>
                </div>
                <button wire:click="$set('showClosingModal', true)"
                    class="relative z-10 px-8 py-3.5 bg-white dark:bg-zinc-800 border font-black rounded-2xl shadow-xl transition-all hover:scale-105 active:scale-95 flex items-center gap-2 group
                        {{ $isShiftOverdue 
                            ? 'border-rose-500/30 hover:border-rose-500 text-rose-600 dark:text-rose-400 shadow-rose-500/5' 
                            : 'border-emerald-500/30 hover:border-emerald-500 text-emerald-700 dark:text-emerald-400 shadow-emerald-500/5' }}">
                    <span>Tutup Shift</span>
                    <x-lucide-log-out class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                </button>
            </div>
        </div>
        @if($isShiftOverdue)
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-rose-500/5 rounded-full blur-3xl"></div>
        @endif
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

    {{-- Transactions & Stock Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Recent Transactions Table --}}
        <div class="lg:col-span-2 bg-card rounded-2xl border border-border/50 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-border/50 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-lg">Riwayat Penjualan</h3>
                </div>

                {{-- Sales Filters --}}
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="relative flex-1 group">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                        <input type="text" wire:model.live.debounce.300ms="saleSearch" placeholder="Cari nomor invoice..." class="w-full bg-muted/50 border-none rounded-xl py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                    </div>
                    <select wire:model.live="saleStatus" class="bg-muted/50 border-none rounded-xl py-2 px-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none font-medium">
                        <option value="all">Semua Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending_payment">Pending</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto overflow-y-auto max-h-[460px] custom-scrollbar">
                <table class="w-full text-sm text-left border-collapse">
                    <thead class="bg-white  text-xs text-slate-900  font-bold sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 border-b border-border/50">Invoice</th>
                            <th class="px-6 py-4 border-b border-border/50">Pelanggan</th>
                            <th class="px-6 py-4 border-b border-border/50 text-right">Total</th>
                            <th class="px-6 py-4 border-b border-border/50 text-center">Metode</th>
                            <th class="px-6 py-4 border-b border-border/50 text-center">Status</th>
                            <th class="px-6 py-4 border-b border-border/50 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border/50">
                        @forelse($recentTransactions as $sale)
                        <tr class="hover:bg-muted/5 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col leading-tight">
                                    <span class="font-mono font-bold text-primary">{{ $sale->invoice_number }}</span>
                                    <span class="text-[10px] text-muted-foreground">{{ $sale->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold">{{ $sale->customer->nama ?? 'Umum' }}</span>
                                    <span class="text-[10px] text-muted-foreground">Oleh: {{ $sale->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-foreground">Rp {{ number_format($sale->total, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-tighter
                                    {{ $sale->payment_method === 'cash' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-orange-500/10 text-orange-600' }}">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-tighter
                                    {{ $sale->status === 'completed' ? 'bg-blue-500/10 text-blue-600' : 'bg-amber-500/10 text-amber-600' }}">
                                    {{ $sale->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="openDetail('{{ $sale->invoice_number }}')" class="p-2 hover:bg-primary/10 rounded-lg text-primary transition-all hover:scale-110 active:scale-90">
                                    <x-lucide-eye class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-muted-foreground opacity-50 italic">
                                Tidak ada data penjualan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-border/50 bg-muted/5 mt-auto">
                {{ $recentTransactions->links(data: ['scrollTo' => false]) }}
            </div>
        </div>

        {{-- Real-time Stock Section --}}
        <div class="lg:col-span-1 bg-card rounded-2xl border border-border/50 shadow-sm overflow-hidden flex flex-col h-[600px]">
            <div class="p-6 border-b border-border/50 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-lg">Stok Real-time</h3>
                    <x-lucide-package class="h-5 w-5 text-muted-foreground" />
                </div>
                <div class="relative group">
                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground group-focus-within:text-primary transition-colors" />
                    <input type="text" wire:model.live.debounce.300ms="stockSearch" placeholder="Cari nama produk atau kode..." class="w-full bg-muted/50 border-none rounded-xl py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                </div>
            </div>
            <div class="p-0 flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar relative">
                {{-- Loading Overlay --}}
                <div wire:loading wire:target="stockSearch, gotoPage" class="absolute inset-0 z-20 bg-card/40 backdrop-blur-[1px] flex items-center justify-center">
                    <div class="flex flex-col items-center gap-2 bg-card/80 p-4 rounded-2xl border border-border/50 shadow-xl shadow-black/5 animate-in zoom-in-95 duration-200">
                        <x-lucide-loader-2 class="w-5 h-5 text-primary animate-spin" />
                        <span class="text-[9px] font-black text-muted-foreground uppercase tracking-[0.2em] ml-1">Memuat...</span>
                    </div>
                </div>

                @forelse($stockData as $product)
                <div class="px-5 py-3.5 flex flex-col hover:bg-muted/5 transition-colors border-b border-border/50 last:border-0 gap-2.5">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col max-w-[70%]">
                            <span class="font-bold text-sm text-foreground leading-tight truncate">{{ $product->nama }}</span>
                            <span class="text-[9px] font-mono text-muted-foreground mt-0.5 tracking-tighter">{{ $product->kode_produk }}</span>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-base font-black {{ ($product->batches_sum_qty_sisa_base ?? 0) <= 10 ? 'text-rose-600' : 'text-emerald-600 dark:text-emerald-500' }}">
                                {{ number_format($product->batches_sum_qty_sisa_base ?? 0, 0, ',', '.') }}
                            </span>
                            <span class="text-[8px] font-bold uppercase text-muted-foreground tracking-wider">{{ $product->base_unit }}</span>
                        </div>
                    </div>
                    
                    {{-- Ultra-Compact Batch List --}}
                    @if($product->batches->count() > 0)
                    <div class="space-y-1">
                        <div class="grid grid-cols-1 gap-1">
                            @foreach($product->batches as $batch)
                            <div class="flex items-center justify-between px-2 py-1 rounded-md bg-muted/20 border border-border/30 text-[9px] group/batch">
                                <div class="flex items-center gap-1.5 font-medium">
                                    <span class="font-mono font-bold text-blue-600/80">{{ $batch->batch_code }}</span>
                                    <span class="text-[8px] text-muted-foreground/60">{{ $batch->tanggal_masuk->format('d/m/y') }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-foreground">{{ (float)$batch->qty_sisa_base }} <span class="text-[7px] text-muted-foreground">{{ $product->base_unit }}</span></span>
                                    @if($batch->qty_masuk_original > 0)
                                    <span class="text-[7px] opacity-40 italic hidden sm:inline">orig: {{ (float)$batch->qty_masuk_original }}{{ $batch->input_unit_name }}</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @empty
                <div class="px-6 py-12 text-center text-muted-foreground">
                    <div class="flex flex-col items-center gap-2">
                        <x-lucide-box class="h-8 w-8 text-muted-foreground/30" />
                        <p class="text-xs font-medium uppercase tracking-widest opacity-50">Stok tidak ditemukan</p>
                    </div>
                </div>
                @endforelse
            </div>
            <div class="px-6 py-4 border-t border-border/50 bg-muted/5 mt-auto">
                {{ $stockData->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    </div>

    {{-- Sale Detail Modal --}}
    @if($showDetailModal && $selectedSale)
    <div class="fixed inset-0 z-[140] overflow-y-auto" wire:keydown.escape.window="closeDetail">
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
                            <h3 class="text-xl font-black text-foreground tracking-tight">Detail Penjualan</h3>
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
                                <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest mb-1">Pelanggan</h4>
                                <p class="text-sm font-bold text-foreground">{{ $selectedSale->customer->nama ?? 'Umum (Walk-in)' }}</p>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest mb-1">Kasir</h4>
                                <p class="text-sm font-bold text-foreground">{{ $selectedSale->user->name ?? 'System' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4 text-right">
                            <div>
                                <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest mb-1">Waktu</h4>
                                <p class="text-sm font-bold text-foreground">{{ $selectedSale->created_at->translatedFormat('d F Y, H:i') }}</p>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-muted-foreground uppercase tracking-widest mb-1">Metode Bayar</h4>
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
                            Daftar Barang
                        </h4>
                        <div class="rounded-2xl border border-border/50 overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-muted/30 border-b border-border">
                                    <tr>
                                        <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Produk</th>
                                        <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Qty</th>
                                        <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    @foreach($selectedSale->items as $item)
                                    <tr class="hover:bg-muted/5 transition-colors">
                                        <td class="px-4 py-3 font-bold text-foreground">
                                            {{ $item->product->nama ?? 'Produk Dihapus' }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-medium">
                                            {{ (float)($item->qty_base / $item->unit_multiplier) }} {{ $item->unit_label }}
                                        </td>
                                        <td class="px-4 py-3 text-right font-bold text-foreground">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-muted/10 border-t border-border">
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-right font-black uppercase tracking-widest text-[9px] text-muted-foreground text-base">TOTAL PENJUALAN</td>
                                        <td class="px-4 py-3 text-right font-black text-primary text-lg">
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
                    <button wire:click="printInvoice" class="flex items-center gap-2 px-6 py-2.5 bg-orange-500 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-orange-500/20 hover:bg-orange-600 transition-all active:scale-95">
                        <x-lucide-printer class="w-4 h-4" />
                        Cetak Ulang Struk
                    </button>
                    <button wire:click="closeDetail" class="px-8 py-2.5 bg-muted text-foreground font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-muted/80 transition-all active:scale-95 border border-border">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>