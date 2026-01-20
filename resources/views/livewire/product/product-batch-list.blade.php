<div class="space-y-6 animate-fade-in">
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Stok Masuk (Product Batch)</h2>
            <p class="text-muted-foreground text-sm mt-1">Kelola stok masuk berdasarkan batch produksi</p>
        </div>
        <button wire:click="openModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold transition-all duration-200 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg shadow-primary/20">
            <x-lucide-plus class="h-4 w-4" />
            Tambah Stok (Batch)
        </button>
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
                    placeholder="Cari Batch Code..."
                    class="flex h-10 w-full rounded-lg border border-input bg-background/50 pl-10 pr-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all"
                >
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                {{-- Produk Filter --}}
                <div class="sm:w-[250px]">
                    <select 
                        wire:model.live="product_filter"
                        class="flex h-10 w-full rounded-lg border border-input bg-background/50 px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all"
                    >
                        <option value="all">Semua Produk</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->nama }}</option>
                        @endforeach
                    </select>
                </div>

                @if($search || $product_filter !== 'all')
                    <button wire:click="$set('search', ''); $set('product_filter', 'all')" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
                        Reset
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="rounded-xl border border-border/50 bg-card overflow-hidden shadow-sm min-h-[500px] flex flex-col">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-border bg-muted/30">
                        <th class="px-4 py-3 font-semibold text-muted-foreground">Tgl Masuk</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground">Batch Code</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground">Produk</th>
                        <th class="px-6 py-3 font-semibold text-muted-foreground text-right">Harga Beli</th>
                        <th class="px-6 py-3 font-semibold text-muted-foreground text-right">Harga Jual</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-center">Stok (Sisa/Masuk)</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-right w-[80px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-muted/50 transition-colors group">
                            <td class="px-4 py-4 text-muted-foreground">
                                {{ $batch->tanggal_masuk->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-4">
                                <button 
                                    wire:click="openHistoryModal({{ $batch->id }})" 
                                    class="font-mono text-xs font-bold text-primary hover:underline hover:text-primary/80 transition-colors text-left"
                                    title="Klik untuk lihat riwayat"
                                >
                                    {{ $batch->batch_code }}
                                </button>
                            </td>
                            <td class="px-4 py-4 font-medium text-foreground">
                                {{ $batch->product->nama }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                Rp {{ number_format($batch->harga_beli_per_unit, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-primary">
                                Rp {{ number_format($batch->harga_jual_per_unit, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="font-bold {{ $batch->qty_sisa_base > 0 ? 'text-foreground' : 'text-destructive' }}">
                                        {{ $batch->qty_sisa_base }}
                                    </span>
                                    <span class="text-[10px] text-muted-foreground uppercase tracking-tighter">dari {{ $batch->qty_masuk_base }} {{ $batch->product->base_unit }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-right relative">
                                <div class="dropdown inline-block relative">
                                    <button class="p-1.5 hover:bg-muted rounded-md transition-all duration-200 text-muted-foreground hover:text-foreground focus:ring-2 focus:ring-primary/20 dropdown-toggle">
                                        <x-lucide-more-horizontal class="h-4 w-4" />
                                    </button>
                                    <div class="dropdown-menu absolute right-0 mt-2 w-48 rounded-lg shadow-xl bg-card border border-border z-50 py-1.5 hidden animate-in fade-in zoom-in duration-200">
                                        <button 
                                            wire:click="openDetailModal({{ $batch->id }})"
                                            class="flex w-full items-center gap-2 px-3 py-2 text-sm text-foreground hover:bg-muted transition-colors"
                                        >
                                            <x-lucide-eye class="h-4 w-4 text-muted-foreground" />
                                            Detail Batch
                                        </button>
                                        
                                        <button 
                                            wire:click="openHistoryModal({{ $batch->id }})"
                                            class="flex w-full items-center gap-2 px-3 py-2 text-sm text-foreground hover:bg-muted transition-colors"
                                        >
                                            <x-lucide-history class="h-4 w-4 text-muted-foreground" />
                                            Riwayat Batch
                                        </button>
                                        
                                        @if($batch->qty_sisa_base == $batch->qty_masuk_base)
                                            <button 
                                                wire:click="openModal({{ $batch->id }})"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-sm text-foreground hover:bg-muted transition-colors"
                                            >
                                                <x-lucide-pencil class="h-4 w-4 text-muted-foreground" />
                                                Edit Batch
                                            </button>
                                            <hr class="my-1 border-border/50">
                                            <button 
                                                wire:click="confirmDelete({{ $batch->id }})"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-sm text-destructive hover:bg-red-500/10 transition-colors"
                                            >
                                                <x-lucide-trash-2 class="h-4 w-4" />
                                                Hapus
                                            </button>
                                        @else
                                            <div class="px-3 py-2 text-[10px] text-muted-foreground uppercase font-bold bg-muted/30">
                                                Locked (FIFO)
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-20 text-center">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="p-4 rounded-full bg-muted/50">
                                        <x-lucide-truck class="h-10 w-10 text-muted-foreground/30" />
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-base font-semibold text-foreground">Belum ada stok masuk</p>
                                        <p class="text-sm text-muted-foreground max-w-xs mx-auto">Catat stok masuk pertama Anda untuk mulai mengelola inventaris batch.</p>
                                    </div>
                                    <button wire:click="openModal()" class="mt-2 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-all">
                                        <x-lucide-plus class="h-4 w-4" />
                                        Tambah Stok Masuk
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($batches->hasPages())
            <div class="border-t border-border px-4 py-4 bg-muted/10">
                {{ $batches->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tambah/Edit Batch --}}
    <div 
        x-data="{ 
            show: @entangle('showModal'),
            searchOpen: false,
            isEdit: @entangle('isEdit')
        }" 
        x-show="show" 
        class="fixed inset-0 z-[100] overflow-y-auto" 
        style="display: none;"
        @keydown.escape.window="show = false"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-background/80 backdrop-blur-sm transition-opacity" 
                wire:click="closeModal"
            ></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div 
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block align-bottom bg-card w-full max-w-lg rounded-2xl shadow-2xl border border-border text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle z-[101]"
            >
                <div class="flex items-center justify-between p-6 border-b border-border bg-muted/10">
                    <div>
                        <h3 class="text-xl font-bold text-foreground" x-text="isEdit ? 'Edit Batch' : 'Tambah Stok (Batch Baru)'"></h3>
                        <p class="text-sm text-muted-foreground mt-1">Lengkapi detail stok masuk di bawah ini</p>
                    </div>
                    <button wire:click="closeModal" class="p-2 hover:bg-muted rounded-full transition-colors group">
                        <x-lucide-x class="h-5 w-5 text-muted-foreground group-hover:text-foreground" />
                    </button>
                </div>

                <form wire:submit.prevent="store" class="p-6 space-y-5">
                    {{-- Pilih Produk (Searchable) --}}
                    <div class="space-y-2 relative" x-data="{ searchOpen: false }">
                        <label class="text-sm font-semibold text-foreground flex items-center gap-1.5">
                            Cari Produk
                            <span class="text-destructive">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                wire:model.live.debounce.300ms="productSearch"
                                @focus="searchOpen = true"
                                @click.away="searchOpen = false"
                                placeholder="Ketik nama produk..."
                                class="flex h-10 w-full rounded-lg border border-input bg-background/50 pl-10 pr-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all"
                                :disabled="isEdit"
                            >
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-lucide-search class="h-4 w-4 text-muted-foreground" />
                            </div>
                            
                            {{-- Search Loading Spinner --}}
                            <div wire:loading wire:target="productSearch" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <x-lucide-loader-2 class="h-4 w-4 animate-spin text-primary" />
                            </div>
                            
                            {{-- Search Dropdown --}}
                            <div 
                                x-show="searchOpen && $wire.productSearch.length >= 2" 
                                class="absolute z-[110] mt-1 w-full bg-card border border-border rounded-lg shadow-xl py-1"
                                style="display: none;"
                            >
                                @forelse($searchProducts as $p)
                                    <button 
                                        type="button"
                                        wire:click="selectProduct({{ $p->id }})"
                                        @click="searchOpen = false"
                                        class="flex w-full items-center gap-3 px-3 py-2 text-sm hover:bg-muted transition-colors text-left"
                                    >
                                        <div class="p-1.5 rounded-md bg-primary/10 text-primary">
                                            <x-lucide-package class="h-4 w-4" />
                                        </div>
                                        <div>
                                            <div class="font-bold">{{ $p->nama }}</div>
                                            <div class="text-[10px] text-muted-foreground uppercase">{{ $p->kode_produk }} • Base: {{ $p->base_unit }}</div>
                                        </div>
                                    </button>
                                @empty
                                    <div class="px-3 py-4 text-center text-xs text-muted-foreground">Produk tidak ditemukan</div>
                                @endforelse
                            </div>
                        </div>
                        @error('selectedProduct') <span class="text-xs text-destructive font-medium">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Batch Code --}}
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-foreground">Batch Code (Auto)</label>
                            <div class="flex h-10 w-full rounded-lg border border-border bg-muted/30 px-3 py-2 text-sm font-mono text-muted-foreground cursor-not-allowed items-center">
                                {{ $batch_code }}
                            </div>
                        </div>

                        {{-- Tgl Masuk --}}
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-foreground">Tanggal Masuk <span class="text-destructive">*</span></label>
                            <input type="date" wire:model="tanggal_masuk" class="flex h-10 w-full rounded-lg border border-input bg-background/50 px-3 py-2 text-sm">
                            @error('tanggal_masuk') <span class="text-xs text-destructive font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="bg-card p-5 rounded-2xl space-y-5 border-2 border-muted/50 shadow-sm relative overflow-hidden group/pricing">
                        <div class="absolute top-0 right-0 p-3">
                            <x-lucide-badge-dollar-sign class="h-10 w-10 text-primary/5 -rotate-12 group-hover/pricing:scale-110 transition-transform" />
                        </div>
                        
                        <div class="grid grid-cols-2 gap-5">
                            {{-- Harga Beli --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-muted-foreground uppercase tracking-widest flex items-center gap-1.5">
                                    <x-lucide-shopping-cart class="h-3 w-3" />
                                    Harga Beli (Base)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted-foreground font-semibold text-xs">Rp</div>
                                    <input 
                                        type="number" 
                                        wire:model.live.debounce.500ms="harga_beli" 
                                        class="flex h-11 w-full rounded-xl border-border bg-background pl-8 pr-3 py-2 text-sm font-bold focus:ring-4 focus:ring-primary/10 transition-all outline-none border-2"
                                    >
                                </div>
                                @if($selectedProduct && $harga_beli != $master_harga_beli)
                                    <div class="flex items-center gap-1 text-[10px] text-destructive font-bold animate-pulse">
                                        <x-lucide-alert-circle class="h-3 w-3" />
                                        Harga berbeda dari Master (Rp {{ number_format($master_harga_beli, 0, ',', '.') }})
                                    </div>
                                @endif
                            </div>

                            {{-- Margin nominal --}}
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-green-600 uppercase tracking-widest flex items-center gap-1.5">
                                    <x-lucide-trending-up class="h-3 w-3" />
                                    Margin (Nominal)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-green-600 font-semibold text-xs">Rp</div>
                                    <input 
                                        type="number" 
                                        wire:model.live.debounce.500ms="margin" 
                                        class="flex h-11 w-full rounded-xl border-green-500/30 bg-green-500/5 pl-8 pr-3 py-2 text-sm font-bold text-green-700 focus:ring-4 focus:ring-green-500/10 transition-all outline-none border-2"
                                    >
                                </div>
                            </div>
                        </div>

                        {{-- Harga Jual result --}}
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-primary uppercase tracking-widest flex items-center gap-1.5">
                                <x-lucide-tag class="h-3 w-3" />
                                Harga Jual (Final)
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-primary font-bold text-lg">Rp</div>
                                <input 
                                    type="number" 
                                    wire:model.live.debounce.500ms="harga_jual" 
                                    class="flex h-14 w-full rounded-2xl border-primary/20 bg-primary/5 pl-12 pr-4 py-2 text-xl font-black text-primary focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none border-2 shadow-inner"
                                >
                                <div wire:loading wire:target="calculateHargaJual, calculateMargin, harga_beli, margin, harga_jual" class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                                    <x-lucide-loader-2 class="h-5 w-5 animate-spin text-primary" />
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between px-1">
                                @if($selectedProduct && $harga_jual != $master_harga_jual)
                                    <div class="flex items-center gap-1 text-[10px] text-destructive font-bold animate-pulse">
                                        <x-lucide-alert-circle class="h-3 w-3" />
                                        Beda dari Master (Rp {{ number_format($master_harga_jual, 0, ',', '.') }})
                                    </div>
                                @else
                                    <div></div>
                                @endif
                                
                                @if($selectedProduct && ($harga_beli != $master_harga_beli || $harga_jual != $master_harga_jual))
                                    <button 
                                        type="button"
                                        @click="$wire.showConfirmMasterUpdate = true"
                                        class="flex items-center gap-1.5 px-3 py-1 rounded-lg bg-orange-500/10 text-orange-600 hover:bg-orange-500/20 transition-all text-[10px] font-black uppercase tracking-tighter border border-orange-500/20 shadow-sm"
                                    >
                                        <x-lucide-refresh-cw class="h-3 w-3" />
                                        Update Harga Master
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Qty & Unit --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-2 space-y-2">
                            <label class="text-sm font-semibold text-foreground">Kuantitas Masuk <span class="text-destructive">*</span></label>
                            <input type="number" step="1" wire:model="qty_masuk" placeholder="0" class="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm font-bold" :disabled="isEdit">
                            @error('qty_masuk') <span class="text-xs text-destructive font-medium">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-foreground">Satuan</label>
                            <select wire:model="unit_id" class="flex h-10 w-full rounded-lg border border-input bg-background px-2 py-2 text-sm" :disabled="isEdit">
                                <option value="">(Base Unit)</option>
                                @foreach($availableUnits as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->label }} (x{{ $unit->multiplier }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-border mt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-semibold rounded-lg text-muted-foreground hover:bg-muted transition-all">Batal</button>
                        <button type="submit" class="px-6 py-2 text-sm font-semibold rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                            <x-lucide-save class="h-4 w-4" />
                            <span x-text="isEdit ? 'Perbarui Batch' : 'Simpan Batch'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Detail Batch --}}
    <div 
        x-data="{ show: @entangle('showDetailModal') }" 
        x-show="show" 
        class="fixed inset-0 z-[110] overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" class="fixed inset-0 bg-background/80 backdrop-blur-sm transition-opacity" @click="show = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="show" class="relative inline-block align-bottom bg-card w-full max-w-md rounded-2xl shadow-2xl border border-border text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle z-[111]">
                @if($batchDetail)
                    <div class="p-6 border-b border-border bg-muted/30 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-xl bg-primary/10 text-primary">
                                <x-lucide-box class="h-6 w-6" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">Detail Batch</h3>
                                <p class="text-xs text-muted-foreground">{{ $batchDetail->batch_code }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="p-2 hover:bg-muted rounded-full transition-colors"><x-lucide-x class="h-5 w-5" /></button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-sm text-muted-foreground">Produk</span>
                                <span class="text-sm font-bold text-foreground text-right pl-4">{{ $batchDetail->product->nama }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-sm text-muted-foreground">Tanggal Masuk</span>
                                <span class="text-sm font-semibold">{{ $batchDetail->tanggal_masuk->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-sm text-muted-foreground">Harga Beli / Unit</span>
                                <span class="text-sm font-semibold">Rp {{ number_format($batchDetail->harga_beli_per_unit, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-sm text-muted-foreground">Harga Jual / Unit</span>
                                <span class="text-sm font-bold text-primary">Rp {{ number_format($batchDetail->harga_jual_per_unit, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-sm text-muted-foreground">Total Stok Masuk</span>
                                <span class="text-sm font-semibold">{{ $batchDetail->qty_masuk_base }} {{ $batchDetail->product->base_unit }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm text-muted-foreground">Sisa Stok</span>
                                <span class="text-lg font-black {{ $batchDetail->qty_sisa_base > 0 ? 'text-green-600' : 'text-destructive' }}">
                                    {{ $batchDetail->qty_sisa_base }} {{ $batchDetail->product->base_unit }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-muted/10 border-t border-border flex justify-end">
                        <button @click="show = false" class="px-6 py-2 text-sm font-bold rounded-xl bg-primary text-primary-foreground">Tutup</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Riwayat Batch --}}
    <div 
        x-data="{ show: @entangle('showHistoryModal') }" 
        x-show="show" 
        class="fixed inset-0 z-[115] overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" class="fixed inset-0 bg-background/80 backdrop-blur-sm transition-opacity" @click="show = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div x-show="show" class="relative inline-block align-bottom bg-card w-full max-w-lg rounded-2xl shadow-2xl border border-border text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle z-[116]">
                <div class="flex items-center justify-between p-6 border-b border-border bg-muted/10">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <x-lucide-history class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-foreground">Riwayat Batch</h3>
                            <p class="text-xs text-muted-foreground mt-0.5">Log perubahan stok & aktivitas</p>
                        </div>
                    </div>
                    <button wire:click="closeHistoryModal" class="p-2 hover:bg-muted rounded-full transition-colors group">
                        <x-lucide-x class="h-5 w-5 text-muted-foreground group-hover:text-foreground" />
                    </button>
                </div>

                <div class="p-6 max-h-[500px] overflow-y-auto">
                    @if(count($batchLogs) > 0)
                        <div class="relative pl-6 space-y-8 before:absolute before:inset-y-0 before:left-[7px] before:w-[2px] before:bg-border/50">
                            @foreach($batchLogs as $log)
                                <div class="relative">
                                    {{-- Timeline Dot --}}
                                    <div class="absolute -left-[23px] top-1 h-[10px] w-[10px] rounded-full border-2 border-background shadow-[0_0_0_2px_rgba(0,0,0,0.05)]
                                        {{ $log->qty_change > 0 ? 'bg-green-500' : ($log->qty_change < 0 ? 'bg-red-500' : 'bg-primary') }}">
                                    </div>
                                    
                                    <div class="space-y-2">
                                        {{-- Header: Action & Time --}}
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-bold text-foreground flex items-center gap-2 capitalize">
                                                @if($log->action === 'created')
                                                    <x-lucide-plus-circle class="h-3.5 w-3.5 text-green-500" />
                                                    Batch Dibuat
                                                @elseif($log->action === 'adjustment')
                                                    <x-lucide-clipboard-edit class="h-3.5 w-3.5 text-orange-500" />
                                                    Stock Adjustment
                                                @elseif($log->action === 'updated')
                                                    <x-lucide-pencil class="h-3.5 w-3.5 text-primary" />
                                                    Update Data
                                                @else
                                                    <x-lucide-activity class="h-3.5 w-3.5 text-muted-foreground" />
                                                    {{ str_replace('_', ' ', $log->action) }}
                                                @endif
                                            </p>
                                            <time class="text-[10px] font-medium text-muted-foreground uppercase tracking-wider">
                                                {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                                            </time>
                                        </div>

                                        {{-- Description --}}
                                        <p class="text-sm text-muted-foreground leading-relaxed">
                                            {{ $log->description ?? 'Tidak ada deskripsi' }}
                                        </p>

                                        {{-- Qty Changes Box --}}
                                        @if($log->qty_change != 0 || $log->qty_before != $log->qty_after)
                                            <div class="p-3 rounded-lg bg-muted/30 border border-border/50 text-xs space-y-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-foreground/70 w-16 shrink-0">Kuantitas:</span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-muted-foreground">{{ $log->qty_before }}</span>
                                                        <x-lucide-arrow-right class="h-3 w-3 text-muted-foreground/50" />
                                                        <span class="font-bold text-foreground">{{ $log->qty_after }}</span>
                                                        
                                                        <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] font-bold 
                                                            {{ $log->qty_change > 0 ? 'bg-green-500/10 text-green-600' : 'bg-red-500/10 text-red-600' }}">
                                                            {{ $log->qty_change > 0 ? '+' : '' }}{{ $log->qty_change }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- User Info --}}
                                        @if($log->user)
                                            <div class="flex items-center gap-1.5">
                                                <div class="h-4 w-4 rounded-full bg-primary/10 flex items-center justify-center">
                                                    <x-lucide-user class="h-2.5 w-2.5 text-primary" />
                                                </div>
                                                <span class="text-[11px] text-muted-foreground font-medium">{{ $log->user->name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="p-4 rounded-full bg-muted/50 mb-4 text-muted-foreground/30">
                                <x-lucide-clipboard-list class="h-10 w-10" />
                            </div>
                            <p class="text-sm font-semibold text-foreground">Belum ada riwayat</p>
                            <p class="text-xs text-muted-foreground mt-1">Aktivitas batch akan tercatat di sini.</p>
                        </div>
                    @endif
                </div>

                <div class="p-4 bg-muted/10 border-t border-border flex justify-end">
                    <button wire:click="closeHistoryModal" class="px-4 py-2 text-sm font-bold rounded-lg bg-muted text-foreground hover:bg-muted/80 transition-comments">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Update Master Harga --}}
    <div 
        x-data="{ show: @entangle('showConfirmMasterUpdate') }" 
        x-show="show" 
        class="fixed inset-0 z-[130] overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="show" class="fixed inset-0 bg-background/80 backdrop-blur-sm transition-opacity" @click="show = false"></div>
            <div x-show="show" class="relative bg-card w-full max-w-sm rounded-3xl shadow-2xl border border-orange-500/20 p-8 text-center transform transition-all z-[131] overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-orange-400 to-orange-600"></div>
                <div class="w-20 h-20 bg-orange-500/10 text-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6 rotate-3">
                    <x-lucide-refresh-cw class="h-10 w-10 animate-spin-slow" />
                </div>
                <h3 class="text-2xl font-black text-foreground mb-3 tracking-tight">Update Harga Master?</h3>
                <p class="text-sm text-muted-foreground mb-8 leading-relaxed">
                    Tindakan ini akan merubah <span class="font-bold text-foreground">Harga Beli & Jual Default</span> pada Master Produk. 
                    <br><br>
                    <span class="text-[10px] uppercase font-bold text-orange-600 tracking-widest bg-orange-500/5 px-2 py-1 rounded-md italic">Batch stok yang sudah tersimpan tidak akan berubah.</span>
                </p>
                <div class="flex flex-col gap-3">
                    <button wire:click="updateMasterPrices" class="w-full py-3.5 rounded-2xl bg-orange-600 text-white font-black hover:bg-orange-700 shadow-lg shadow-orange-600/20 transition-all flex items-center justify-center gap-2">
                        <x-lucide-check-circle-2 class="h-5 w-5" />
                        Ya, Update Master Produk
                    </button>
                    <button @click="show = false" class="w-full py-3.5 rounded-2xl border-2 border-border font-bold hover:bg-muted transition-all">Batal</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div 
        x-data="{ show: @entangle('showDeleteModal') }" 
        x-show="show" 
        class="fixed inset-0 z-[120] overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4">
            <div x-show="show" class="fixed inset-0 bg-background/80 backdrop-blur-sm transition-opacity" @click="show = false"></div>
            <div x-show="show" class="relative bg-card w-full max-w-sm rounded-2xl shadow-2xl border border-border p-6 text-center transform transition-all z-[121]">
                <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-lucide-alert-triangle class="h-8 w-8" />
                </div>
                <h3 class="text-xl font-bold text-foreground mb-2">Hapus Batch?</h3>
                <p class="text-sm text-muted-foreground mb-6">Tindakan ini tidak dapat dibatalkan. Batch akan dihapus permanen dari sistem.</p>
                <div class="flex gap-3">
                    <button @click="show = false" class="flex-1 px-4 py-2.5 rounded-xl border border-border font-semibold hover:bg-muted transition-all">Batal</button>
                    <button wire:click="delete" class="flex-1 px-4 py-2.5 rounded-xl bg-destructive text-destructive-foreground font-semibold hover:bg-destructive/90 transition-all">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        // Dropdown toggle logic
        document.addEventListener('click', (e) => {
            const toggle = e.target.closest('.dropdown-toggle');
            const allMenus = document.querySelectorAll('.dropdown-menu');

            if (toggle) {
                const menu = toggle.nextElementSibling;
                const isHidden = menu.classList.contains('hidden');
                
                // Close all other menus
                allMenus.forEach(m => m.classList.add('hidden'));
                
                if (isHidden) {
                    menu.classList.remove('hidden');
                }
            } else {
                // Clicked outside, close all
                allMenus.forEach(m => m.classList.add('hidden'));
            }
        });
    });
</script>
