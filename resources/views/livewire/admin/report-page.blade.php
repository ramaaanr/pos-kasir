<div class="relative min-h-[calc(100vh-100px)]">
    {{-- Global Download Loading Overlay --}}
    <div wire:loading.flex wire:target="exportExcel" class="fixed inset-0 z-[9999] items-center justify-center bg-background/80 backdrop-blur-md">
        <div class="bg-card p-8 rounded-3xl border border-border shadow-2xl flex flex-col items-center gap-4 max-w-xs w-full animate-in zoom-in duration-300">
            <div class="relative">
                <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <x-lucide-file-spreadsheet class="w-6 h-6 text-primary animate-pulse" />
                </div>
            </div>
            <div class="text-center">
                <h3 class="font-black text-foreground text-lg tracking-tight">Menyiapkan Excel</h3>
                <p class="text-xs text-muted-foreground font-medium mt-1">Data sedang diproses, mohon tunggu sebentar...</p>
            </div>
        </div>
    </div>

    {{-- Global Upload Drive Loading Overlay --}}
    <div wire:loading.flex wire:target="uploadToDrive" class="fixed inset-0 z-[9999] items-center justify-center bg-background/80 backdrop-blur-md">
        <div class="bg-card p-8 rounded-3xl border border-border shadow-2xl flex flex-col items-center gap-4 max-w-xs w-full animate-in zoom-in duration-300">
            <div class="relative">
                <div class="w-16 h-16 border-4 border-blue-500/20 border-t-blue-500 rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <x-lucide-hard-drive-upload class="w-6 h-6 text-blue-500 animate-pulse" />
                </div>
            </div>
            <div class="text-center">
                <h3 class="font-black text-foreground text-lg tracking-tight">Mengupload ke Drive</h3>
                <p class="text-xs text-muted-foreground font-medium mt-1">Sedang menyimpan file ke Google Drive...</p>
            </div>
        </div>
    </div>

    {{-- Success Modal --}}
    <div x-data="{ show: false, message: '' }" 
         @upload-success.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)" 
         x-show="show" 
         style="display: none;"
         class="fixed inset-0 z-[9999] flex items-center justify-center px-4 bg-background/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
        <div class="bg-card p-6 rounded-2xl border border-border shadow-xl w-full max-w-sm animate-in zoom-in-95 duration-200">
            <div class="flex flex-col items-center text-center gap-3">
                <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                    <x-lucide-check class="w-6 h-6 text-emerald-600" />
                </div>
                <div>
                    <h3 class="font-bold text-lg">Berhasil Upload</h3>
                    <p class="text-sm text-muted-foreground mt-1" x-text="message"></p>
                </div>
                <button @click="show = false" class="mt-2 w-full py-2 bg-muted hover:bg-muted/80 rounded-lg text-sm font-medium transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Error Modal --}}
    <div x-data="{ show: false, message: '' }" 
         @upload-error.window="show = true; message = $event.detail.message" 
         x-show="show" 
         style="display: none;"
         class="fixed inset-0 z-[9999] flex items-center justify-center px-4 bg-background/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
         
        <div class="bg-card p-6 rounded-2xl border border-destructive/20 shadow-xl w-full max-w-sm animate-in zoom-in-95 duration-200">
            <div class="flex flex-col items-center text-center gap-3">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <x-lucide-x class="w-6 h-6 text-red-600" />
                </div>
                <div>
                    <h3 class="font-bold text-lg text-destructive">Gagal Upload</h3>
                    <p class="text-sm text-muted-foreground mt-1" x-text="message"></p>
                </div>
                <button @click="show = false" class="mt-2 w-full py-2 bg-destructive/10 hover:bg-destructive/20 text-destructive rounded-lg text-sm font-medium transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <div class="animate-fade-in space-y-4">
        {{-- Header Section with Filters (Compact) --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between bg-card p-4 rounded-2xl border border-border/50 shadow-sm relative group overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:scale-110 transition-transform duration-700 pointer-events-none">
                <x-lucide-bar-chart-3 class="w-24 h-24 text-primary" />
            </div>
            
            <div class="relative z-10">
                <h2 class="text-xl font-black text-foreground tracking-tight">Pusat Laporan & Analitik</h2>
                <p class="text-[11px] text-muted-foreground font-medium">Kelola dan pantau data performa bisnis Anda</p>
            </div>

            <div class="flex flex-wrap items-center gap-3 relative z-10">
                {{-- Date Range / Single Date --}}
                @if(in_array($selectedReport, ['shift_harian', 'rekap_shift']))
                    <div class="flex items-center gap-2 bg-muted/50 p-1 rounded-xl border border-border/50">
                        <x-lucide-calendar class="w-3.5 h-3.5 ml-2 text-muted-foreground" />
                        <input type="date" wire:model.live="startDate" class="bg-transparent border-none text-[11px] font-bold focus:ring-0 px-2 py-0.5 w-32 cursor-pointer">
                    </div>
                @elseif(!in_array($selectedReport, ['stok_realtime', 'hutang_outstanding', 'inventory_valuation', 'debt_aging', 'slow_moving_stock']))
                    <div class="flex items-center gap-2 bg-muted/50 p-1 rounded-xl border border-border/50">
                        <input type="date" wire:model.live="startDate" class="bg-transparent border-none text-[11px] font-bold focus:ring-0 px-2 py-0.5 w-32 cursor-pointer">
                        <x-lucide-arrow-right class="w-3 h-3 text-muted-foreground" />
                        <input type="date" wire:model.live="endDate" class="bg-transparent border-none text-[11px] font-bold focus:ring-0 px-2 py-0.5 w-32 cursor-pointer">
                    </div>
                @endif

                <div class="h-6 w-px bg-border/50 mx-1"></div>

                <div class="flex items-center p-0.5 bg-muted/30 rounded-lg border border-border/50">
                    @if($selectedReport !== 'shift_harian')
                    <button wire:click="exportExcel" wire:loading.attr="disabled" wire:target="exportExcel" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600/10 text-emerald-600 text-[10px] font-black rounded-lg hover:bg-emerald-600 hover:text-white transition-all uppercase disabled:opacity-50">
                        <x-lucide-file-spreadsheet class="w-3.5 h-3.5" />
                        <span>Excel</span>
                    </button>
                    {{-- Upload to Drive Button --}}
                    <button wire:click="uploadToDrive" wire:loading.attr="disabled" wire:target="uploadToDrive" class="ml-1 inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600/10 text-blue-600 text-[10px] font-black rounded-lg hover:bg-blue-600 hover:text-white transition-all uppercase disabled:opacity-50">
                        <x-lucide-hard-drive-upload class="w-3.5 h-3.5" />
                        <span>Drive</span>
                    </button>
                    @endif
                    <button @click="window.print()" class="ml-1 inline-flex items-center gap-1.5 px-4 py-2 text-muted-foreground hover:text-primary text-[10px] font-black rounded-lg transition-all uppercase">
                        <x-lucide-printer class="w-3.5 h-3.5" />
                        <span>Cetak</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
            {{-- Left Side: Sidebar Categories (4 Columns) --}}
            <div class="lg:col-span-4 space-y-6 max-h-[calc(100vh-200px)] lg:overflow-y-auto pr-2 custom-scrollbar pb-10">
                
                {{-- Operasional Section --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 px-1">
                        <div class="w-2 h-4 bg-blue-500 rounded-full"></div>
                        <h3 class="font-black text-[10px] uppercase tracking-[0.2em] text-muted-foreground">Operasional</h3>
                    </div>
                    <div class="grid gap-2">
                        @foreach([
                            ['key' => 'shift_harian', 'name' => 'Shift Harian Kasir', 'icon' => 'user-check', 'color' => 'blue'],
                            ['key' => 'stok_realtime', 'name' => 'Stok Real-time', 'icon' => 'package-check', 'color' => 'blue'],
                            ['key' => 'penjualan_periode', 'name' => 'Penjualan Periode', 'icon' => 'shopping-cart', 'color' => 'blue'],
                            ['key' => 'fifo_pnl', 'name' => 'P&L FIFO (Profit)', 'icon' => 'trending-up', 'color' => 'emerald'],
                        ] as $item)
                        <button wire:click="selectReport('{{ $item['key'] }}')" 
                            class="flex items-center justify-between p-3 rounded-2xl border transition-all group overflow-hidden relative
                                {{ $selectedReport === $item['key'] 
                                    ? 'bg-primary/5 border-primary shadow-sm ring-1 ring-primary/20' 
                                    : 'bg-card border-border/50 hover:bg-muted/30 hover:border-border' }}">
                            
                            @if($selectedReport === $item['key'])
                            <div class="absolute -right-2 -top-2 opacity-10">
                                <x-dynamic-component :component="'lucide-' . $item['icon']" class="w-12 h-12" />
                            </div>
                            @endif

                            <div class="flex items-center gap-3 relative z-10">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all
                                    {{ $selectedReport === $item['key'] ? 'bg-primary text-white scale-110' : 'bg-muted/50 text-muted-foreground group-hover:scale-105' }}">
                                    <x-dynamic-component :component="'lucide-' . $item['icon']" class="w-4.5 h-4.5" />
                                </div>
                                <span class="text-xs font-bold {{ $selectedReport === $item['key'] ? 'text-foreground' : 'text-muted-foreground group-hover:text-foreground' }}">
                                    {{ $item['name'] }}
                                </span>
                            </div>
                            <x-lucide-chevron-right class="w-4 h-4 transition-all {{ $selectedReport === $item['key'] ? 'text-primary translate-x-0' : 'text-muted-foreground/30 -translate-x-2' }}" />
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Piutang & Auditor Section --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 px-1">
                        <div class="w-2 h-4 bg-purple-500 rounded-full"></div>
                        <h3 class="font-black text-[10px] uppercase tracking-[0.2em] text-muted-foreground">Piutang & Audit</h3>
                    </div>
                    <div class="grid gap-2">
                        @foreach([
                            ['key' => 'hutang_outstanding', 'name' => 'Hutang Outstanding', 'icon' => 'scroll-text', 'color' => 'purple'],
                            ['key' => 'pembayaran_hutang', 'name' => 'Riwayat Pembayaran', 'icon' => 'history', 'color' => 'purple'],
                            ['key' => 'rekap_shift', 'name' => 'Rekap Shift (Cash)', 'icon' => 'calculator', 'color' => 'purple'],
                            ['key' => 'fifo_compliance', 'name' => 'FIFO Compliance', 'icon' => 'refresh-cw', 'color' => 'purple'],
                        ] as $item)
                        <button wire:click="selectReport('{{ $item['key'] }}')" 
                            class="flex items-center justify-between p-3 rounded-2xl border transition-all group overflow-hidden relative
                                {{ $selectedReport === $item['key'] 
                                    ? 'bg-primary/5 border-primary shadow-sm ring-1 ring-primary/20' 
                                    : 'bg-card border-border/50 hover:bg-muted/30 hover:border-border' }}">
                            
                            @if($selectedReport === $item['key'])
                            <div class="absolute -right-2 -top-2 opacity-10">
                                <x-dynamic-component :component="'lucide-' . $item['icon']" class="w-12 h-12" />
                            </div>
                            @endif

                            <div class="flex items-center gap-3 relative z-10">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all
                                    {{ $selectedReport === $item['key'] ? 'bg-primary text-white scale-110' : 'bg-muted/50 text-muted-foreground group-hover:scale-105' }}">
                                    <x-dynamic-component :component="'lucide-' . $item['icon']" class="w-4.5 h-4.5" />
                                </div>
                                <span class="text-xs font-bold {{ $selectedReport === $item['key'] ? 'text-foreground' : 'text-muted-foreground group-hover:text-foreground' }}">
                                    {{ $item['name'] }}
                                </span>
                            </div>
                            <x-lucide-chevron-right class="w-4 h-4 transition-all {{ $selectedReport === $item['key'] ? 'text-primary translate-x-0' : 'text-muted-foreground/30 -translate-x-2' }}" />
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Kontrol & Analisis Section --}}
                <div class="space-y-3">
                    <div class="flex items-center gap-2 px-1">
                        <div class="w-2 h-4 bg-amber-500 rounded-full"></div>
                        <h3 class="font-black text-[10px] uppercase tracking-[0.2em] text-muted-foreground">Kontrol & Analisis</h3>
                    </div>
                    <div class="grid gap-2">
                        @foreach([
                            ['key' => 'inventory_valuation', 'name' => 'Inventory Valuation', 'icon' => 'banknote', 'color' => 'amber'],
                            ['key' => 'debt_aging', 'name' => 'Aging Hutang', 'icon' => 'calendar-clock', 'color' => 'amber'],
                            ['key' => 'cashier_performance', 'name' => 'Performa Kasir', 'icon' => 'medal', 'color' => 'amber'],
                            ['key' => 'slow_moving_stock', 'name' => 'Slow Moving Stock', 'icon' => 'snail', 'color' => 'amber'],
                        ] as $item)
                        <button wire:click="selectReport('{{ $item['key'] }}')" 
                            class="flex items-center justify-between p-3 rounded-2xl border transition-all group overflow-hidden relative
                                {{ $selectedReport === $item['key'] 
                                    ? 'bg-primary/5 border-primary shadow-sm ring-1 ring-primary/20' 
                                    : 'bg-card border-border/50 hover:bg-muted/30 hover:border-border' }}">
                            
                            @if($selectedReport === $item['key'])
                            <div class="absolute -right-2 -top-2 opacity-10">
                                <x-dynamic-component :component="'lucide-' . $item['icon']" class="w-12 h-12" />
                            </div>
                            @endif

                            <div class="flex items-center gap-3 relative z-10">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all
                                    {{ $selectedReport === $item['key'] ? 'bg-primary text-white scale-110' : 'bg-muted/50 text-muted-foreground group-hover:scale-105' }}">
                                    <x-dynamic-component :component="'lucide-' . $item['icon']" class="w-4.5 h-4.5" />
                                </div>
                                <span class="text-xs font-bold {{ $selectedReport === $item['key'] ? 'text-foreground' : 'text-muted-foreground group-hover:text-foreground' }}">
                                    {{ $item['name'] }}
                                </span>
                            </div>
                            <x-lucide-chevron-right class="w-4 h-4 transition-all {{ $selectedReport === $item['key'] ? 'text-primary translate-x-0' : 'text-muted-foreground/30 -translate-x-2' }}" />
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Right Side: Report Content (8 Columns) --}}
            <div class="lg:col-span-8 space-y-4 animate-slide-up">
                <div class="bg-card border border-border/50 rounded-3xl shadow-sm overflow-hidden flex flex-col min-h-[550px]">
                    
                    {{-- Report Card Header --}}
                    <div class="p-5 border-b border-border bg-muted/10 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-2xl bg-primary/10 text-primary">
                                @php
                                    $allReports = [
                                        'shift_harian' => ['name' => 'Shift Harian Kasir', 'icon' => 'user-check'],
                                        'stok_realtime' => ['name' => 'Stok Real-time', 'icon' => 'package-check'],
                                        'penjualan_periode' => ['name' => 'Penjualan Periode', 'icon' => 'shopping-cart'],
                                        'hutang_outstanding' => ['name' => 'Hutang Outstanding', 'icon' => 'scroll-text'],
                                        'pembayaran_hutang' => ['name' => 'Riwayat Pembayaran Hutang', 'icon' => 'history'],
                                        'rekap_shift' => ['name' => 'Rekap Shift (Cash)', 'icon' => 'calculator'],
                                        'fifo_pnl' => ['name' => 'P&L FIFO (Profit)', 'icon' => 'trending-up'],
                                        'fifo_compliance' => ['name' => 'FIFO Compliance', 'icon' => 'refresh-cw'],
                                        'inventory_valuation' => ['name' => 'Inventory Valuation', 'icon' => 'banknote'],
                                        'debt_aging' => ['name' => 'Aging Hutang (Piutang)', 'icon' => 'calendar-clock'],
                                        'cashier_performance' => ['name' => 'Performa Kasir', 'icon' => 'medal'],
                                        'slow_moving_stock' => ['name' => 'Slow Moving Stock', 'icon' => 'snail'],
                                    ];
                                    $current = $allReports[$selectedReport] ?? ['name' => 'Laporan', 'icon' => 'list'];
                                @endphp
                                <x-dynamic-component :component="'lucide-' . $current['icon']" class="w-5 h-5" />
                            </div>
                            <div>
                                <h3 class="font-black text-foreground tracking-tight">{{ $current['name'] }}</h3>
                                <p class="text-[10px] text-muted-foreground uppercase font-bold tracking-widest mt-0.5">
                                    @if(in_array($selectedReport, ['shift_harian', 'rekap_shift']))
                                        {{ date('d M Y', strtotime($startDate)) }}
                                    @elseif(in_array($selectedReport, ['stok_realtime', 'hutang_outstanding', 'inventory_valuation', 'debt_aging', 'slow_moving_stock']))
                                        REAL-TIME DATA
                                    @else
                                        {{ date('d M Y', strtotime($startDate)) }} — {{ date('d M Y', strtotime($endDate)) }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                             <div wire:loading wire:target="selectReport, loadReport" class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-muted animate-pulse">
                                <div class="w-2 h-2 rounded-full bg-primary animate-bounce"></div>
                                <span class="text-[9px] font-black text-muted-foreground uppercase">Memuat Data...</span>
                             </div>
                        </div>
                    </div>

                    <div class="flex-1 overflow-x-auto overflow-y-auto custom-scrollbar p-1">
                        {{-- Loading Skeleton --}}
                        <div wire:loading wire:target="selectReport, startDate, endDate, gotoPage, nextPage, previousPage" class="p-6 space-y-6">
                            <div class="grid grid-cols-4 gap-4">
                                <div class="h-20 bg-muted/50 rounded-2xl animate-pulse"></div>
                                <div class="h-20 bg-muted/50 rounded-2xl animate-pulse"></div>
                                <div class="h-20 bg-muted/50 rounded-2xl animate-pulse"></div>
                                <div class="h-20 bg-muted/50 rounded-2xl animate-pulse"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-10 bg-muted/50 rounded-lg animate-pulse"></div>
                                <div class="h-10 bg-muted/30 rounded-lg animate-pulse"></div>
                                <div class="h-10 bg-muted/50 rounded-lg animate-pulse"></div>
                            </div>
                        </div>

                        <div wire:loading.remove wire:target="selectReport, startDate, endDate, gotoPage, nextPage, previousPage">
                            @if($selectedReport === 'shift_harian')
                                {{-- 1. SHIFT HARIAN VIEW --}}
                                <div class="p-6 space-y-8">
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <div class="p-4 rounded-2xl bg-blue-500/5 border border-blue-500/10 space-y-1">
                                            <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Tunai</span>
                                            <p class="text-lg font-black text-foreground">Rp {{ number_format($this->reportData['total_cash'] ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="p-4 rounded-2xl bg-purple-500/5 border border-purple-500/10 space-y-1">
                                            <span class="text-[10px] font-black text-purple-500 uppercase tracking-widest">Hutang</span>
                                            <p class="text-lg font-black text-foreground">Rp {{ number_format($this->reportData['total_debt'] ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="p-4 rounded-2xl bg-amber-500/5 border border-amber-500/10 space-y-1">
                                            <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest">Total Bayar (DP)</span>
                                            <p class="text-lg font-black text-foreground">Rp {{ number_format($this->reportData['total_dp'] ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10 space-y-1">
                                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Masuk Ke Kas</span>
                                            <p class="text-lg font-black text-foreground">Rp {{ number_format($this->reportData['total_masuk_kas'] ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="p-4 rounded-2xl bg-primary/5 border border-primary/10 space-y-1">
                                            <span class="text-[10px] font-black text-primary uppercase tracking-widest">Total Omzet</span>
                                            <p class="text-lg font-black text-foreground">Rp {{ number_format($this->reportData['total_sales'] ?? 0, 0, ',', '.') }}</p>
                                        </div>
                                    </div>

                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Waktu</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Nama Kasir</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Metode</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($this->reportData['transactions'] ?? [] as $sale)
                                            <tr class="hover:bg-muted/10 transition-colors">
                                                <td class="px-4 py-3 font-mono font-bold">{{ isset($sale['created_at']) ? date('H:i', strtotime($sale['created_at'])) : '-' }}</td>
                                                <td class="px-4 py-3 font-medium">{{ $sale['user']['name'] ?? 'Sistem' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ ($sale['payment_method'] ?? 'cash') === 'cash' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-purple-500/10 text-purple-600' }}">
                                                        {{ $sale['payment_method'] ?? 'cash' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-black">Rp {{ number_format($sale['total'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada transaksi ditemukan</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($selectedReport === 'stok_realtime')
                                {{-- 2. STOK REAL-TIME VIEW --}}
                                <div class="p-6">
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Produk</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Stok Saat Ini</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($this->reportData as $product)
                                            <tr class="hover:bg-muted/20">
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-col">
                                                        <span class="font-bold text-foreground">{{ $product['nama'] ?? 'Tanpa Nama' }}</span>
                                                        <span class="text-[9px] uppercase text-muted-foreground tracking-widest">{{ $product['kode_produk'] ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="font-black text-primary text-base">{{ number_format($product['total_stock'] ?? 0, 0) }}</span>
                                                    <span class="text-[10px] font-bold text-muted-foreground">{{ $product['base_unit'] ?? 'Pcs' }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @php $status = $product['stock_status'] ?? 'aman'; @endphp
                                                    @if($status === 'habis')
                                                        <span class="px-3 py-1 rounded-full bg-red-500 text-white text-[9px] font-black uppercase">HABIS</span>
                                                    @elseif($status === 'menipis')
                                                        <span class="px-3 py-1 rounded-full bg-amber-500 text-white text-[9px] font-black uppercase">MENIPIS</span>
                                                    @else
                                                        <span class="px-3 py-1 rounded-full bg-emerald-500 text-white text-[9px] font-black uppercase">AMAN</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="3" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada data produk</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($selectedReport === 'penjualan_periode')
                                {{-- 3. PENJUALAN PERIODE VIEW --}}
                                <div class="p-6">
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Tanggal</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Invoice</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Detail Item</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Total Omzet</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($this->reportData as $row)
                                            <tr class="hover:bg-muted/20 align-top">
                                                <td class="px-4 py-3 font-bold">{{ isset($row['created_at']) ? date('d/m/y H:i', strtotime($row['created_at'])) : '-' }}</td>
                                                <td class="px-4 py-3 font-mono font-bold text-primary">{{ $row['invoice_number'] ?? '-' }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="space-y-1">
                                                        @foreach(($row['items'] ?? []) as $item)
                                                            <div class="flex justify-between items-center gap-4 text-[10px]">
                                                                <span class="text-muted-foreground">{{ $item['product']['nama'] ?? 'Produk Terhapus' }} (x{{ number_format(($item['qty_base'] ?? 0) / ($item['unit_multiplier'] ?? 1), 0) }} {{ $item['unit_label'] ?? '' }})</span>
                                                                <span class="font-bold">Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-right font-black text-base">Rp {{ number_format($row['total'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada data periode ini</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    @if($this->reportData instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                    <div class="mt-4">
                                        {{ $this->reportData->links() }}
                                    </div>
                                    @endif
                                </div>

                            @elseif($selectedReport === 'fifo_pnl')
                                {{-- 6. P&L FIFO VIEW --}}
                                <div class="p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                                        @php
                                            $totalRev = collect($this->reportData)->sum('total_revenue');
                                            $totalCogs = collect($this->reportData)->sum('total_cogs');
                                            $totalProfit = $totalRev - $totalCogs;
                                        @endphp
                                        <div class="p-5 rounded-3xl bg-blue-500/5 border border-blue-500/10">
                                            <span class="text-[9px] font-black text-blue-500 uppercase tracking-widest">Total Revenue</span>
                                            <p class="text-xl font-black text-foreground">Rp {{ number_format($totalRev, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="p-5 rounded-3xl bg-red-500/5 border border-red-500/10">
                                            <span class="text-[9px] font-black text-red-500 uppercase tracking-widest">Total COGS (FIFO)</span>
                                            <p class="text-xl font-black text-foreground">Rp {{ number_format($totalCogs, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="p-5 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 shadow-lg shadow-emerald-500/5">
                                            <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Gross Profit</span>
                                            <p class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalProfit, 0, ',', '.') }}</p>
                                            <span class="text-[10px] font-black text-emerald-500">{{ $totalRev > 0 ? number_format(($totalProfit / $totalRev) * 100, 1) : 0 }}% Margin</span>
                                        </div>
                                    </div>

                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Invoice</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Revenue</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">COGS</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Profit</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-center">Margin</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @foreach($this->reportData as $row)
                                            <tr class="hover:bg-muted/20">
                                                <td class="px-4 py-3 font-mono font-bold">{{ $row['invoice_number'] ?? '-' }}</td>
                                                <td class="px-4 py-3 text-right">Rp {{ number_format($row['total_revenue'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right text-red-600/70">Rp {{ number_format($row['total_cogs'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-black text-emerald-600">Rp {{ number_format($row['gross_profit'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="font-bold text-[10px]">{{ number_format($row['margin_percent'] ?? 0, 1) }}%</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            
                            @elseif($selectedReport === 'hutang_outstanding')
                                {{-- 4. HUTANG OUTSTANDING VIEW --}}
                                <div class="p-6">
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Nama</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Jaminan</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Hari</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Total Hutang</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($this->reportData as $debt)
                                            <tr class="hover:bg-muted/20">
                                                <td class="px-4 py-3">
                                                    <div class="flex flex-col">
                                                        <span class="font-bold text-foreground">{{ $debt['customer']['nama'] ?? 'Guest' }}</span>
                                                        <span class="text-[9px] text-muted-foreground">{{ $debt['customer']['no_hp'] ?? '-' }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 font-medium text-muted-foreground italic truncate max-w-[100px]">{{ $debt['jaminan'] ?? '-' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-0.5 rounded bg-muted/50 text-[10px] font-bold">{{ $debt['days_old'] ?? 0 }} Hari</span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-black text-red-600">Rp {{ number_format($debt['amount'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-center">
                                                     <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ ($debt['status'] ?? 'OPEN') === 'OPEN' ? 'bg-red-500/10 text-red-600' : 'bg-amber-500/10 text-amber-600' }}">
                                                        {{ $debt['status'] ?? 'OPEN' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada hutang aktif</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($selectedReport === 'pembayaran_hutang')
                                {{-- 5. RIWAYAT PEMBAYARAN HUTANG VIEW --}}
                                <div class="p-6">
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Waktu Bayar</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Customer</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Metode</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Nominal</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Penerima</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($reportData as $pay)
                                            <tr class="hover:bg-muted/10">
                                                <td class="px-4 py-3 text-muted-foreground">{{ isset($pay['paid_at']) ? date('d/m/y H:i', strtotime($pay['paid_at'])) : '-' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="font-bold text-foreground">{{ $pay['debt']['customer']['nama'] ?? 'Guest' }}</span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-blue-500/10 text-blue-600">
                                                        {{ $pay['payment_method'] ?? 'cash' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-black text-emerald-600">Rp {{ number_format($pay['amount'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 font-medium text-muted-foreground">{{ $pay['user']['name'] ?? '-' }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="5" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada riwayat pembayaran</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($selectedReport === 'rekap_shift')
                                <div class="p-6">
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Waktu</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Kasir</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Saldo Awal</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Masuk (Sistem)</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Fisik (Laci)</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Selisih</th>
                                                <th class="px-2 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse(($this->reportData ?? []) as $shift)
                                            <tr class="hover:bg-muted/10">
                                                <td class="px-4 py-3 text-muted-foreground">
                                                    {{ date('d/m/y H:i', strtotime($shift['start_time'])) }}
                                                </td>
                                                <td class="px-4 py-3 font-bold">
                                                    {{ $shift['user']['name'] ?? '-' }}
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    Rp {{ number_format($shift['opening_cash'], 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-blue-600 font-medium">
                                                    Rp {{ number_format($shift['cash_received'], 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-right text-emerald-600 font-bold">
                                                    Rp {{ number_format($shift['cash_in_drawer'], 0, ',', '.') }}
                                                </td>
                                                <td class="px-4 py-3 text-right {{ ($shift['difference'] ?? 0) < 0 ? 'text-red-600' : 'text-emerald-600' }} font-black">
                                                    Rp {{ number_format($shift['difference'] ?? 0, 0, ',', '.') }}
                                                </td>
                                                <td class="px-2 py-3 text-center">
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $shift['status'] === 'open' ? 'bg-amber-500/10 text-amber-600' : 'bg-zinc-500/10 text-zinc-600' }}">
                                                        {{ $shift['status'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @if($shift['note'])
                                            <tr class="bg-muted/5">
                                                <td colspan="7" class="px-4 py-2 text-[10px] italic text-muted-foreground">
                                                    Catatan: {{ $shift['note'] }}
                                                </td>
                                            </tr>
                                            @endif
                                            @empty
                                            <tr>
                                                <td colspan="7" class="px-4 py-12 text-center text-muted-foreground italic">
                                                    Belum ada rekap shift untuk tanggal ini
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($selectedReport === 'inventory_valuation')
                                {{-- 8. INVENTORY VALUATION VIEW --}}
                                <div class="p-6 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @php
                                            $totalInventoryValue = collect($this->reportData)->sum('total_valuation');
                                            $top5Valuation = collect($this->reportData)->sortByDesc('total_valuation')->take(5);
                                        @endphp
                                        <div class="p-6 rounded-3xl bg-primary text-white shadow-xl shadow-primary/20 flex flex-col justify-between">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80">Total Nilai Inventory</span>
                                                <x-lucide-banknote class="w-6 h-6 opacity-40" />
                                            </div>
                                            <div class="mt-4">
                                                <p class="text-3xl font-black italic">Rp {{ number_format($totalInventoryValue, 0, ',', '.') }}</p>
                                                <p class="text-[11px] font-bold mt-2 opacity-80 tracking-tight">Nilai total modal tertahan di stok saat ini</p>
                                            </div>
                                        </div>

                                        <div class="p-6 rounded-3xl bg-muted/30 border border-border/50 flex flex-col justify-between">
                                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-4">Top 5 Aset Stok</span>
                                            <div class="space-y-2">
                                                @foreach($top5Valuation as $item)
                                                    <div class="flex justify-between items-center group">
                                                        <span class="text-[11px] font-bold text-foreground group-hover:text-primary transition-colors truncate max-w-[150px]">{{ $item['nama'] }}</span>
                                                        <span class="text-[11px] font-black">Rp {{ number_format($item['total_valuation'], 0, ',', '.') }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Produk</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Stok</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Hrg Beli Rata-rata</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Total Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($this->reportData as $row)
                                            <tr class="hover:bg-muted/10 transition-colors">
                                                <td class="px-4 py-3 font-bold text-foreground">{{ $row['nama'] }}</td>
                                                <td class="px-4 py-3 text-right font-medium">{{ number_format($row['total_stock'], 0) }} unit</td>
                                                <td class="px-4 py-3 text-right text-muted-foreground italic font-semibold">Rp {{ number_format($row['avg_buying_price'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-black text-primary text-sm">Rp {{ number_format($row['total_valuation'], 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="4" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada data valuation</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($selectedReport === 'debt_aging')
                                {{-- 9. DEBT AGING VIEW --}}
                                <div class="p-6">
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Customer</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">0–30 Hari</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">31–60 Hari</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">>60 Hari</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right font-bold">Total Piutang</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($this->reportData as $row)
                                            <tr class="hover:bg-muted/10 transition-colors">
                                                <td class="px-4 py-3 font-black text-foreground">{{ $row['customer_name'] }}</td>
                                                <td class="px-4 py-3 text-right text-emerald-600 font-bold">Rp {{ number_format($row['aging']['current'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right text-amber-500 font-bold">Rp {{ number_format($row['aging']['at_risk'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right text-red-600 font-black">Rp {{ number_format($row['aging']['danger'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-black bg-muted/5">Rp {{ number_format($row['aging']['total'], 0, ',', '.') }}</td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="5" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada piutang aktif</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($selectedReport === 'cashier_performance')
                                {{-- 10. CASHIER PERFORMANCE VIEW --}}
                                <div class="p-6">
                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Nama Kasir</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-center">JML Transaksi</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Total Penjualan</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Rata-rata Penjualan</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Selisih Kas</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($this->reportData as $row)
                                            <tr class="hover:bg-muted/10 transition-colors">
                                                <td class="px-4 py-3 font-black text-foreground">{{ $row['name'] }}</td>
                                                <td class="px-4 py-3 text-center font-bold text-primary">{{ number_format($row['transaction_count'], 0) }}</td>
                                                <td class="px-4 py-3 text-right font-bold">Rp {{ number_format($row['total_sales'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right text-muted-foreground">Rp {{ number_format($row['avg_transaction_value'], 0, ',', '.') }}</td>
                                                <td class="px-4 py-3 text-right font-black {{ ($row['total_difference'] ?? 0) < 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                                    Rp {{ number_format($row['total_difference'] ?? 0, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="5" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada data untuk periode ini</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($selectedReport === 'slow_moving_stock')
                                {{-- 11. SLOW MOVING STOCK VIEW --}}
                                <div class="p-6">
                                    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
                                        <div class="p-3 rounded-2xl bg-emerald-500/5 border border-emerald-500/20">
                                            <p class="text-[9px] font-black text-emerald-600 uppercase tracking-tighter italic">Healthy</p>
                                            <p class="text-[10px] font-bold text-emerald-700/70 leading-tight">Terjual < 30 hr</p>
                                        </div>
                                        <div class="p-3 rounded-2xl bg-amber-500/5 border border-amber-500/20">
                                            <p class="text-[9px] font-black text-amber-600 uppercase tracking-tighter italic">Warning</p>
                                            <p class="text-[10px] font-bold text-amber-700/70 leading-tight">30-60 hr</p>
                                        </div>
                                        <div class="p-3 rounded-2xl bg-orange-500/5 border border-orange-500/20">
                                            <p class="text-[9px] font-black text-orange-600 uppercase tracking-tighter italic">Slow</p>
                                            <p class="text-[10px] font-bold text-orange-700/70 leading-tight">60-90 hr</p>
                                        </div>
                                        <div class="p-3 rounded-2xl bg-red-500/5 border border-red-500/20">
                                            <p class="text-[9px] font-black text-red-600 uppercase tracking-tighter italic">Dead</p>
                                            <p class="text-[10px] font-bold text-red-700/70 leading-tight">> 90 hr</p>
                                        </div>
                                    </div>

                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Produk</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Stok</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Tgl Terakhir Laku</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-center">Umur Diam</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @forelse($this->reportData as $row)
                                            <tr class="hover:bg-muted/10 transition-colors">
                                                <td class="px-4 py-3 font-black text-foreground">{{ $row['nama'] }}</td>
                                                <td class="px-4 py-3 text-right font-medium">{{ number_format($row['total_stock'], 0) }}</td>
                                                <td class="px-4 py-3 text-muted-foreground italic font-semibold">{{ $row['last_sold_at'] ? date('d/m/Y', strtotime($row['last_sold_at'])) : 'BELUM PERNAH' }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-0.5 rounded bg-muted text-[10px] font-black">{{ $row['days_inactive'] }} Hari</span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    @php
                                                        $colors = [
                                                            'healthy' => 'bg-emerald-500 text-white',
                                                            'warning' => 'bg-amber-500 text-white',
                                                            'slow' => 'bg-orange-500 text-white',
                                                            'dead' => 'bg-red-500 text-white',
                                                        ];
                                                        $statusColor = $colors[$row['status']] ?? 'bg-muted text-muted-foreground';
                                                    @endphp
                                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $statusColor }}">{{ $row['status'] }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr><td colspan="5" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada stok lama ditemukan</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                            @elseif($selectedReport === 'fifo_compliance')
                                {{-- 7. FIFO COMPLIANCE VIEW --}}
                                <div class="p-6">
                                    <div class="mb-6 p-4 rounded-xl bg-amber-500/5 border border-amber-500/20 flex gap-4 items-start">
                                        <x-lucide-info class="w-5 h-5 text-amber-600 mt-1" />
                                        <div class="space-y-1">
                                            <h4 class="text-sm font-bold text-amber-700 uppercase tracking-tight">Audit Trail FIFO</h4>
                                            <p class="text-[11px] text-amber-600/80 leading-relaxed font-semibold italic">Menampilkan history penggunaan batch stok untuk setiap transaksi penjualan. Gunakan data ini untuk memastikan stok lama selalu keluar lebih dulu.</p>
                                        </div>
                                    </div>

                                    <table class="w-full text-xs text-left">
                                        <thead>
                                            <tr class="bg-muted/30 border-y border-border">
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Waktu Jual</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Invoice</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Batch Digunakan</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Tgl Masuk Batch</th>
                                                <th class="px-4 py-3 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-center">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border">
                                            @foreach(($this->reportData ?? []) as $row)
                                            <tr class="hover:bg-muted/10">
                                                <td class="px-4 py-3 text-muted-foreground">{{ isset($row['sale_date']) ? date('d/m/y H:i', strtotime($row['sale_date'])) : '-' }}</td>
                                                <td class="px-4 py-3 font-bold">{{ $row['invoice_number'] ?? '-' }}</td>
                                                <td class="px-4 py-3 font-mono font-black text-blue-600">{{ $row['batch_code'] ?? '-' }}</td>
                                                <td class="px-4 py-3">{{ isset($row['batch_date']) ? date('d M Y', strtotime($row['batch_date'])) : '-' }}</td>
                                                <td class="px-4 py-3 text-center font-bold">{{ $row['qty_base'] ?? 0 }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            @else
                                {{-- Default / Empty State --}}
                                <div class="p-20 text-center space-y-4">
                                    <div class="w-20 h-20 bg-muted/50 rounded-full flex items-center justify-center mx-auto opacity-30">
                                        <x-lucide-alert-circle class="w-10 h-10" />
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="text-lg font-black text-foreground uppercase tracking-widest">Pilih Jenis Laporan</h3>
                                        <p class="text-xs text-muted-foreground font-medium">Gunakan menu di sisi kiri untuk melihat data laporan yang tersedia.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Footer with disclaimer --}}
                    <div class="p-4 bg-muted/5 border-t border-border flex items-center justify-between">
                        <p class="text-[9px] text-muted-foreground font-bold tracking-widest uppercase italic">* Seluruh data dihitung secara real-time dari database</p>
                        <span class="text-[9px] font-black text-primary px-2 py-0.5 rounded bg-primary/10">CONFIDENTIAL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.1);
        }
        
        @media print {
            header, nav, aside, .lg\:col-span-4, button { display: none !important; }
            .lg\:col-span-8 { width: 100% !important; flex: none !important; }
            .bg-card { border: none !important; box-shadow: none !important; }
            .shadow-sm { box-shadow: none !important; }
            .animate-slide-up { animation: none !important; }
        }
    </style>
</div>
