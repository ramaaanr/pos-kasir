<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-card/50 backdrop-blur-xl p-6 rounded-2xl border border-border shadow-sm">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Stock Adjustment</h1>
            <p class="text-muted-foreground mt-1">Koreksi stok rusak, hilang, atau hasil stok opname.</p>
        </div>
        <div>
            <button wire:click="openModal" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-semibold text-primary-foreground bg-primary hover:bg-primary/90 rounded-xl shadow-lg shadow-primary/25 transition-all duration-200">
                <x-lucide-plus-circle class="w-5 h-5" />
                <span>Buat Stock Adjustment</span>
            </button>
        </div>
    </div>

    {{-- List Table --}}
    <div class="bg-card border border-border rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-muted/80 backdrop-blur-md border-b border-border sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="px-6 py-3 font-bold text-foreground uppercase tracking-wider text-xs w-1/4">Tanggal & Alasan</th>
                        <th class="px-6 py-3 font-bold text-foreground uppercase tracking-wider text-xs w-2/5">Info Batch / Produk</th>
                        <th class="px-6 py-3 font-bold text-foreground uppercase tracking-wider text-xs w-1/6">Selisih</th>
                        <th class="px-6 py-3 font-bold text-foreground uppercase tracking-wider text-xs">Dibuat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/30">
                    @forelse($adjustments as $index => $adj)
                        @php $isEven = $index % 2 === 0; @endphp
                        
                        {{-- Group Header Row (Date & User) --}}
                        <tr class="{{ $isEven ? 'bg-muted/40' : 'bg-background' }} border-t-2 border-border/60">
                            <td class="px-6 pt-4 pb-1">
                                <div class="flex items-center gap-2 text-primary font-bold text-[11px] uppercase tracking-widest">
                                    <x-lucide-calendar class="w-3.5 h-3.5" />
                                    {{ $adj->created_at->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td class="px-6 pt-4 pb-1" colspan="2"></td>
                            <td class="px-6 pt-4 pb-1">
                                <div class="flex items-center gap-2 text-muted-foreground text-[11px] font-medium">
                                    <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary">
                                        {{ substr($adj->user->name ?? '?', 0, 1) }}
                                    </div>
                                    {{ $adj->user->name ?? 'Unknown' }}
                                </div>
                            </td>
                        </tr>

                        {{-- Item Rows (Reason & Items) --}}
                        @foreach($adj->items as $itemIndex => $item)
                            @php $diff = $item->qty_after_base - $item->qty_before_base; @endphp
                            <tr class="{{ $isEven ? 'bg-muted/40' : 'bg-background' }} hover:bg-primary/5 transition-colors group">
                                <td class="px-6 py-2 align-top">
                                    @if($itemIndex === 0)
                                        <div class="flex flex-col">
                                            <span class="text-foreground font-semibold text-sm leading-tight" title="{{ $adj->reason }}">
                                                {{ $adj->reason }}
                                            </span>
                                            <span class="text-[10px] text-muted-foreground uppercase mt-1">Reason</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-2">
                                    <div class="flex items-start gap-3">
                                        <div class="text-muted-foreground font-mono text-sm mt-0.5 select-none text-primary/40">↳</div>
                                        <div>
                                            <div class="text-sm font-bold text-foreground group-hover:text-primary transition-colors">
                                                {{ $item->batch->product->nama ?? 'Unknown Product' }}
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-muted font-mono text-muted-foreground uppercase">
                                                    {{ $item->batch->batch_code ?? '-' }}
                                                </span>
                                                <span class="text-[9px] text-muted-foreground italic">
                                                    Stock: {{ $item->qty_before_base }} → {{ $item->qty_after_base }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-2">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg {{ $diff > 0 ? 'bg-green-500/10 text-green-600' : ($diff < 0 ? 'bg-red-500/10 text-red-600' : 'bg-muted text-muted-foreground') }} font-bold text-sm">
                                        @if($diff > 0)
                                            <x-lucide-trending-up class="w-3.5 h-3.5" />
                                            +{{ $diff }}
                                        @elseif($diff < 0)
                                            <x-lucide-trending-down class="w-3.5 h-3.5" />
                                            {{ $diff }}
                                        @else
                                            <x-lucide-minus class="w-3.5 h-3.5" />
                                            0
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-2"></td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-muted-foreground">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-lucide-clipboard-list class="w-10 h-10 opacity-20" />
                                    <p>Belum ada riwayat adjustment</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-border bg-muted/20">
            {{ $adjustments->links() }}
        </div>
    </div>

    {{-- Create Modal --}}
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-background/80 backdrop-blur-sm transition-opacity" @click="show = false"></div>

        <div class="relative w-full max-w-4xl bg-card border border-border rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-border flex items-center justify-between bg-muted/30">
                <div>
                    <h2 class="text-lg font-bold text-foreground flex items-center gap-2">
                        <x-lucide-clipboard-edit class="w-5 h-5 text-primary" />
                        Buat Stock Adjustment
                    </h2>
                    <p class="text-xs text-muted-foreground">Wizard langkah demi langkah</p>
                </div>
                <button @click="show = false" class="text-muted-foreground hover:text-foreground">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                
                {{-- Step 1: Header Info --}}
                @if($step === 1)
                    <div class="space-y-4 max-w-lg mx-auto py-10">
                        <div class="text-center mb-6">
                            <h3 class="text-lg font-semibold text-foreground">Informasi Adjustment</h3>
                            <p class="text-sm text-muted-foreground">Tentukan tanggal dan alasan perubahan stok.</p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-muted-foreground">Tanggal</label>
                            <input type="date" wire:model="tanggal" class="w-full px-4 py-2.5 rounded-xl bg-muted/50 border-transparent focus:bg-background focus:border-primary focus:ring-0 transition-all text-foreground" readonly>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-muted-foreground">Reason (Wajib)</label>
                            <textarea wire:model="reason" rows="3" placeholder="Contoh: Stok opname, Barang rusak, Selisih timbang..." class="w-full px-4 py-2.5 rounded-xl bg-muted/50 border-transparent focus:bg-background focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-foreground"></textarea>
                            @error('reason') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button wire:click="nextStep" class="px-6 py-2.5 bg-primary text-primary-foreground font-semibold rounded-xl hover:bg-primary/90 transition-all">
                                Lanjut Pilih Produk
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Step 2: Manage Products --}}
                @if($step === 2)
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-full">
                        {{-- Left: Product Selection --}}
                        <div class="lg:col-span-1 space-y-4 border-r border-border pr-2">
                            <h3 class="font-semibold text-foreground flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs">1</span>
                                Pilih Produk
                            </h3>
                            
                            <div class="relative">
                                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="productSearch" 
                                    placeholder="Cari nama atau barcode..." 
                                    class="w-full pl-10 pr-4 py-2 rounded-lg bg-muted/50 border-transparent focus:bg-background focus:border-primary focus:ring-0 text-sm"
                                >
                            </div>

                            {{-- Search Results --}}
                            @if(strlen($productSearch) >= 2)
                                <div class="bg-card border border-border rounded-lg shadow-lg overflow-hidden max-h-60 overflow-y-auto">
                                    @forelse($this->filteredProducts as $product)
                                        <button 
                                            wire:click="selectProduct({{ $product->id }})"
                                            class="w-full text-left px-4 py-3 hover:bg-muted/50 border-b border-border/50 last:border-0 transition-colors flex flex-col gap-0.5"
                                        >
                                            <span class="text-sm font-medium text-foreground">{{ $product->nama }}</span>
                                            <span class="text-xs text-muted-foreground">{{ $product->kode_produk }}</span>
                                        </button>
                                    @empty
                                        <div class="px-4 py-3 text-xs text-muted-foreground text-center">Produk tidak ditemukan</div>
                                    @endforelse
                                </div>
                            @endif

                            {{-- Selected List --}}
                            <div class="mt-6">
                                <h4 class="text-xs font-bold text-muted-foreground uppercase tracking-wider mb-2">Produk Dipilih</h4>
                                <div class="space-y-2">
                                    @forelse($stagedItems as $prodId => $data)
                                        <div 
                                            class="flex items-center justify-between p-3 rounded-lg border {{ $selectedProductId == $prodId ? 'bg-primary/5 border-primary' : 'bg-muted/20 border-border hover:border-primary/50' }} cursor-pointer transition-all"
                                            wire:click="$set('selectedProductId', {{ $prodId }})"
                                        >
                                            <span class="text-sm font-medium text-foreground">{{ $data['name'] }}</span>
                                            <button wire:click.stop="removeProduct({{ $prodId }})" class="text-muted-foreground hover:text-destructive">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @empty
                                        <p class="text-xs text-muted-foreground italic">Belum ada produk dipilih</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Right: Batch Management --}}
                        <div class="lg:col-span-2 space-y-4 pl-2">
                            @if($selectedProductId && isset($stagedItems[$selectedProductId]))
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-semibold text-foreground flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs">2</span>
                                        Kelola Batch: {{ $stagedItems[$selectedProductId]['name'] }}
                                    </h3>
                                </div>

                                <div class="bg-muted/20 rounded-xl border border-border overflow-hidden">
                                    <table class="w-full text-sm">
                                        <thead class="bg-muted/50 border-b border-border text-xs uppercase text-muted-foreground">
                                            <tr>
                                                <th class="px-4 py-3 text-left">Batch Code</th>
                                                <th class="px-4 py-3 text-left">Tgl Masuk</th>
                                                <th class="px-4 py-3 text-center">Stok Saat Ini</th>
                                                <th class="px-4 py-3 text-center bg-primary/5">Stok Baru (Hasil Opname)</th>
                                                <th class="px-4 py-3 text-right">Selisih</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-border/50">
                                            @foreach($stagedItems[$selectedProductId]['batches'] as $batchId => $batch)
                                                @php $diff = $batch['qty_new'] - $batch['qty_current']; @endphp
                                                <tr class="hover:bg-background transition-colors">
                                                    <td class="px-4 py-3 font-mono text-xs">{{ $batch['code'] }}</td>
                                                    <td class="px-4 py-3">{{ $batch['tanggal_masuk'] }}</td>
                                                    <td class="px-4 py-3 text-center font-medium">{{ $batch['qty_current'] }}</td>
                                                    <td class="px-4 py-2 bg-primary/5">
                                                        <input 
                                                            type="number" 
                                                            value="{{ $batch['qty_new'] }}"
                                                            class="w-24 px-2 py-1 text-center rounded bg-background border border-border focus:border-primary focus:ring-1 focus:ring-primary text-foreground font-bold"
                                                            wire:change="updateBatchQty({{ $selectedProductId }}, {{ $batchId }}, $event.target.value)"
                                                        >
                                                    </td>
                                                    <td class="px-4 py-3 text-right font-bold {{ $diff > 0 ? 'text-green-500' : ($diff < 0 ? 'text-red-500' : 'text-muted-foreground') }}">
                                                        {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-xs text-muted-foreground mt-2">
                                    * Masukkan jumlah stok fisik (real) pada kolom "Stok Baru". Selisih akan dihitung otomatis.
                                </p>
                            @else
                                <div class="h-full flex flex-col items-center justify-center text-muted-foreground opacity-50 space-y-2 border-2 border-dashed border-border rounded-xl">
                                    <x-lucide-arrow-left class="w-8 h-8" />
                                    <p class="text-sm">Pilih produk di sebelah kiri untuk mulai mengedit batch.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Modal Footer --}}
            @if($step === 2)
                <div class="px-6 py-4 border-t border-border bg-muted/30 flex items-center justify-between">
                    {{-- Summary --}}
                    <div class="flex items-center gap-6">
                        <div>
                            <p class="text-xs text-muted-foreground uppercase tracking-wider">Batch Berubah</p>
                            <p class="text-lg font-bold text-foreground">{{ $totalAffectedBatches }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted-foreground uppercase tracking-wider">Total Selisih</p>
                            <p class="text-lg font-bold {{ $totalNetChange >= 0 ? 'text-green-500' : 'text-red-500' }}">
                                {{ $totalNetChange > 0 ? '+' : '' }}{{ $totalNetChange }}
                            </p>
                        </div>
                        
                        @if($totalNetChange < 0)
                            <div class="px-3 py-1.5 rounded-lg bg-red-500/10 border border-red-500/20 text-red-600 text-xs font-medium flex items-center gap-2">
                                <x-lucide-alert-triangle class="w-4 h-4" />
                                Stok total akan berkurang
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        <button wire:click="$set('step', 1)" class="px-4 py-2 text-sm font-semibold text-muted-foreground hover:text-foreground">
                            Kembali
                        </button>
                        <button 
                            wire:click="save" 
                            class="px-6 py-2 bg-primary text-primary-foreground font-bold rounded-lg hover:bg-primary/90 shadow-lg shadow-primary/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            {{ $totalAffectedBatches === 0 ? 'disabled' : '' }}
                        >
                            <span wire:loading.remove>Simpan Adjustment</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <x-toast />

</div>
