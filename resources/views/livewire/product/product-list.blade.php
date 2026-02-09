<div class="space-y-6 animate-fade-in">
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Master Produk</h2>
            <p class="text-muted-foreground text-sm mt-1">Kelola data master produk</p>
        </div>
        <button wire:click="openModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold transition-all duration-200 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg shadow-primary/20">
            <x-lucide-plus class="h-4 w-4" />
            Tambah Produk
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
                    placeholder="Cari nama atau barcode"
                    class="flex h-10 w-full rounded-lg border border-input bg-background/50 pl-10 pr-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
            </div>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                {{-- Kategori --}}
                <div class="sm:w-[200px]">
                    <select
                        wire:model.live="category_id"
                        class="flex h-10 w-full rounded-lg border border-input bg-background/50 px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
                        <option value="all">Semua Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="sm:w-[150px]">
                    <select
                        wire:model.live="status"
                        class="flex h-10 w-full rounded-lg border border-input bg-background/50 px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
                        <option value="all">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                @if($search || $category_id !== 'all' || $status !== 'all' || $sortBy !== 'created_at')
                <button wire:click="$set('search', ''); $set('category_id', 'all'); $set('status', 'all'); $set('sortBy', 'created_at'); $set('sortDirection', 'desc')" class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors">
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
                        <th wire:click="sort('nama')" class="px-4 py-3 font-semibold text-muted-foreground cursor-pointer hover:text-foreground transition-colors group">
                            <div class="flex items-center gap-2">
                                Produk
                                @if($sortBy === 'nama')
                                @if($sortDirection === 'asc') <x-lucide-chevron-up class="h-4 w-4 text-primary" /> @else <x-lucide-chevron-down class="h-4 w-4 text-primary" /> @endif
                                @else <x-lucide-chevrons-up-down class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100" /> @endif
                            </div>
                        </th>
                        <th wire:click="sort('category_name')" class="px-4 py-3 font-semibold text-muted-foreground cursor-pointer hover:text-foreground transition-colors group">
                            <div class="flex items-center gap-2">
                                Kategori
                                @if($sortBy === 'category_name')
                                @if($sortDirection === 'asc') <x-lucide-chevron-up class="h-4 w-4 text-primary" /> @else <x-lucide-chevron-down class="h-4 w-4 text-primary" /> @endif
                                @else <x-lucide-chevrons-up-down class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100" /> @endif
                            </div>
                        </th>
                        <th wire:click="sort('kode_produk')" class="px-4 py-3 font-semibold text-muted-foreground cursor-pointer hover:text-foreground transition-colors group">
                            <div class="flex items-center gap-2">
                                Barcode
                                @if($sortBy === 'kode_produk')
                                @if($sortDirection === 'asc') <x-lucide-chevron-up class="h-4 w-4 text-primary" /> @else <x-lucide-chevron-down class="h-4 w-4 text-primary" /> @endif
                                @else <x-lucide-chevrons-up-down class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100" /> @endif
                            </div>
                        </th>
                        <th wire:click="sort('harga_beli_default')" class="px-6 py-3 font-semibold text-muted-foreground text-right cursor-pointer hover:text-foreground transition-colors group">
                            <div class="flex items-center justify-end gap-2">
                                Harga Beli
                                @if($sortBy === 'harga_beli_default')
                                @if($sortDirection === 'asc') <x-lucide-chevron-up class="h-4 w-4 text-primary" /> @else <x-lucide-chevron-down class="h-4 w-4 text-primary" /> @endif
                                @else <x-lucide-chevrons-up-down class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100" /> @endif
                            </div>
                        </th>
                        <th wire:click="sort('harga_jual_default')" class="px-6 py-3 font-semibold text-muted-foreground text-right cursor-pointer hover:text-foreground transition-colors group">
                            <div class="flex items-center justify-end gap-2">
                                Harga Jual
                                @if($sortBy === 'harga_jual_default')
                                @if($sortDirection === 'asc') <x-lucide-chevron-up class="h-4 w-4 text-primary" /> @else <x-lucide-chevron-down class="h-4 w-4 text-primary" /> @endif
                                @else <x-lucide-chevrons-up-down class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100" /> @endif
                            </div>
                        </th>
                        <th wire:click="sort('stok')" class="px-4 py-3 font-semibold text-muted-foreground text-center cursor-pointer hover:text-foreground transition-colors group">
                            <div class="flex items-center justify-center gap-2">
                                Stok
                                @if($sortBy === 'stok')
                                @if($sortDirection === 'asc') <x-lucide-chevron-up class="h-4 w-4 text-primary" /> @else <x-lucide-chevron-down class="h-4 w-4 text-primary" /> @endif
                                @else <x-lucide-chevrons-up-down class="h-3.5 w-3.5 opacity-0 group-hover:opacity-100" /> @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-center">Status</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-right w-[80px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border relative">
                    {{-- Loading Overlay --}}
                    <tr wire:loading class="absolute inset-0 z-50">
                        <td colspan="8" class="p-0 border-none">
                            <div class="absolute inset-0 bg-background/50 backdrop-blur-[1px] flex flex-col items-center justify-center">
                                <x-lucide-loader-2 class="h-8 w-8 animate-spin text-primary" />
                                <span class="text-sm font-medium text-muted-foreground mt-2">Memuat data...</span>
                            </div>
                        </td>
                    </tr>
                    @forelse($products as $product)
                    <tr class="hover:bg-muted/50 transition-colors group">
                        <td class="px-4 py-4 font-medium text-foreground">
                            {{ $product->nama }}
                        </td>
                        <td class="px-4 py-4 text-muted-foreground">
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </td>
                        <td class="px-4 py-4 font-mono text-xs text-muted-foreground uppercase">
                            {{ $product->kode_produk }}
                        </td>
                        <td class="px-6 py-4 text-right font-medium">
                            Rp {{ number_format($product->harga_beli_default, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-primary">
                            Rp {{ number_format($product->harga_jual_default, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-orange-500/10 text-orange-600 font-bold text-xs border border-orange-500/20">
                                {{ (float)($product->batches_sum_qty_sisa_base ?? 0) }} {{ $product->base_unit }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($product->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-500/10 text-green-500 border border-green-500/20">
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-muted text-muted-foreground border border-border">
                                Nonaktif
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button
                                    @click="open = !open"
                                    @click.away="open = false"
                                    class="p-1.5 hover:bg-muted rounded-md transition-all duration-200 text-muted-foreground hover:text-foreground focus:ring-2 focus:ring-primary/20">
                                    <x-lucide-more-horizontal class="h-4 w-4" />
                                </button>
                                <div
                                    x-show="open"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-48 rounded-lg shadow-xl bg-card border border-border z-[60] py-1.5 overflow-hidden"
                                    style="display: none;">
                                    <button
                                        wire:click="openDetailModal({{ $product->id }})"
                                        @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-sm text-foreground hover:bg-muted transition-colors text-left">
                                        <x-lucide-eye class="h-4 w-4 text-muted-foreground" />
                                        Detail Produk
                                    </button>
                                    <button
                                        wire:click="openModal({{ $product->id }})"
                                        @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-sm text-foreground hover:bg-muted transition-colors text-left">
                                        <x-lucide-pencil class="h-4 w-4 text-muted-foreground" />
                                        Edit Produk
                                    </button>
                                    <button
                                        wire:click="openHistoryModal({{ $product->id }})"
                                        @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-sm text-foreground hover:bg-muted transition-colors text-left">
                                        <x-lucide-history class="h-4 w-4 text-muted-foreground" />
                                        Riwayat
                                    </button>
                                    <button
                                        wire:click="toggleStatus({{ $product->id }})"
                                        @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-sm text-foreground hover:bg-muted transition-colors text-left">
                                        @if($product->is_active)
                                        <x-lucide-power-off class="h-4 w-4 text-red-500" />
                                        Nonaktifkan
                                        @else
                                        <x-lucide-power class="h-4 w-4 text-green-500" />
                                        Aktifkan
                                        @endif
                                    </button>
                                    <a href="{{ route('products.barcode', $product->id) }}" target="_blank" @click="open = false" class="flex items-center gap-2 px-3 py-2 text-sm text-foreground hover:bg-muted transition-colors">
                                        <x-lucide-printer class="h-4 w-4 text-muted-foreground" />
                                        Cetak Barcode
                                    </a>
                                    <hr class="my-1 border-border/50">
                                    <button
                                        wire:click="confirmDelete({{ $product->id }})"
                                        @click="open = false"
                                        class="flex w-full items-center gap-2 px-3 py-2 text-sm text-destructive hover:bg-red-500/10 transition-colors text-left">
                                        <x-lucide-trash-2 class="h-4 w-4" />
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-20 text-center">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="p-4 rounded-full bg-muted/50">
                                    <x-lucide-package class="h-10 w-10 text-muted-foreground/30" />
                                </div>
                                <div class="space-y-1">
                                    <p class="text-base font-semibold text-foreground">Belum ada produk</p>
                                    <p class="text-sm text-muted-foreground max-w-xs mx-auto">Mulailah dengan menambahkan produk pertama Anda ke sistem.</p>
                                </div>
                                <button wire:click="openModal()" class="mt-2 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg bg-primary/10 text-primary hover:bg-primary/20 transition-all">
                                    <x-lucide-plus class="h-4 w-4" />
                                    Tambah Produk Baru
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="border-t border-border px-4 py-4 bg-muted/10">
            {{ $products->links() }}
        </div>
        @endif
    </div>

    {{-- Summary Footer --}}
    <div class="flex flex-wrap gap-6 text-sm text-muted-foreground px-1 items-center">
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-primary/40"></span>
            <span>Total: <strong class="text-foreground font-bold">{{ $products->total() }}</strong> produk</span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-green-500/40"></span>
            <span>Aktif: <strong class="text-foreground font-bold">{{ $products->where('is_active', true)->count() }}</strong></span>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-muted-foreground/40"></span>
            <span>Nonaktif: <strong class="text-foreground font-bold">{{ $products->where('is_active', false)->count() }}</strong></span>
        </div>
    </div>

    {{-- Modal Tambah Produk --}}
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        @keydown.enter.prevent
        class="fixed inset-0 z-[100] overflow-y-auto"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Backdrop with smooth fade --}}
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-background/80 backdrop-blur-sm transition-opacity"
                wire:click="closeModal"></div>

            {{-- Centering spacer --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Content with scale and fade --}}
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block align-bottom bg-card w-full max-w-2xl rounded-2xl shadow-2xl border border-border text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle z-[101]">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-6 border-b border-border bg-muted/10">
                    <div>
                        <h3 class="text-xl font-bold text-foreground">{{ $isEdit ? 'Edit Produk' : 'Tambah Produk Baru' }}</h3>
                        <p class="text-sm text-muted-foreground mt-1">{{ $isEdit ? 'Perbarui detail produk Anda' : 'Lengkapi detail produk di bawah ini' }}</p>
                    </div>
                    <button wire:click="closeModal" class="p-2 hover:bg-muted rounded-full transition-colors group">
                        <x-lucide-x class="h-5 w-5 text-muted-foreground group-hover:text-foreground" />
                    </button>
                </div>

                {{-- Modal Body --}}
                <form wire:submit.prevent="store" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Produk --}}
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-semibold text-foreground flex items-center gap-1.5">
                                Nama Produk
                                <span class="text-destructive">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="nama"
                                placeholder="Masukkan nama produk"
                                class="flex h-10 w-full rounded-lg border border-input bg-background/50 px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all"
                                autofocus>
                            @error('nama') <span class="text-xs text-destructive font-medium">{{ $message }}</span> @enderror
                        </div>

                        {{-- Kategori --}}
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-foreground flex items-center gap-1.5">
                                Kategori Produk
                                <span class="text-destructive">*</span>
                            </label>
                            <select
                                wire:model="selectedCategory"
                                class="flex h-10 w-full rounded-lg border border-input bg-background/50 px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedCategory') <span class="text-xs text-destructive font-medium">{{ $message }}</span> @enderror
                        </div>

                        {{-- Barcode --}}
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-foreground flex items-center gap-1.5">
                                Kode Barcode
                                <span class="text-destructive">*</span>
                            </label>
                            <div class="relative group">
                                <input
                                    type="text"
                                    wire:model="kode_produk"
                                    @keydown.enter.prevent
                                    placeholder="Klik untuk scan / generate barcode"
                                    class="flex h-10 w-full rounded-lg border-2 border-primary/20 bg-background/50 pl-3 pr-10 py-2 text-sm font-mono ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/30 transition-all cursor-pointer hover:border-primary/40">
                                <button
                                    type="button"
                                    wire:click="generateBarcode"
                                    class="absolute inset-y-0 right-0 p-2 text-primary hover:text-primary/70 transition-colors"
                                    title="Generate barcode otomatis">
                                    <x-lucide-wand-sparkles class="h-5 w-5" />
                                </button>
                            </div>
                            @error('kode_produk') <span class="text-xs text-destructive font-medium">{{ $message }}</span> @enderror
                        </div>

                        {{-- Satuan --}}
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-foreground flex items-center gap-1.5">
                                Satuan (Base Unit)
                                <span class="text-destructive">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="base_unit"
                                placeholder="Cth: Pcs, Box, Pack"
                                class="flex h-10 w-full rounded-lg border border-input bg-background/50 px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
                            @error('base_unit') <span class="text-xs text-destructive font-medium">{{ $message }}</span> @enderror
                        </div>

                        {{-- Product Units (Dynamic) --}}
                        <div class="col-span-full mt-2 p-4 rounded-xl border border-dashed border-primary/30 bg-primary/5">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                                        <x-lucide-layers class="h-4 w-4" />
                                    </div>
                                    <h4 class="text-sm font-bold text-foreground">Satuan Multi (Opsional)</h4>
                                </div>
                                <button
                                    type="button"
                                    wire:click="addUnit"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-all shadow-sm">
                                    <x-lucide-plus class="h-3.5 w-3.5" />
                                    Tambah Unit
                                </button>
                            </div>

                            @if(count($units) > 0)
                            <div class="space-y-3">
                                @foreach($units as $index => $unit)
                                <div class="grid grid-cols-12 gap-3 items-end animate-in fade-in slide-in-from-top-2 duration-300">
                                    <div class="col-span-6 space-y-1.5">
                                        <label class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider ml-1">Label Satuan</label>
                                        <input
                                            type="text"
                                            wire:model="units.{{ $index }}.label"
                                            placeholder="Cth: Box, Pack"
                                            class="flex h-9 w-full rounded-lg border border-input bg-background/50 px-3 py-1 text-sm transition-all focus-visible:ring-2 focus-visible:ring-primary/30">
                                    </div>
                                    <div class="col-span-4 space-y-1.5">
                                        <label class="text-[11px] font-bold text-muted-foreground uppercase tracking-wider ml-1">Pengali (Isi)</label>
                                        <input
                                            type="number"
                                            wire:model="units.{{ $index }}.multiplier"
                                            placeholder="Cth: 10"
                                            class="flex h-9 w-full rounded-lg border border-input bg-background/50 px-3 py-1 text-sm transition-all focus-visible:ring-2 focus-visible:ring-primary/30">
                                    </div>
                                    <div class="col-span-2 pb-0.5">
                                        <button
                                            type="button"
                                            wire:click="removeUnit({{ $index }})"
                                            class="h-9 w-full flex items-center justify-center rounded-lg text-destructive hover:bg-destructive/10 transition-colors"
                                            title="Hapus unit">
                                            <x-lucide-trash-2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-4 border border-dashed border-border rounded-lg bg-background/30">
                                <p class="text-xs text-muted-foreground">Belum ada satuan tambahan. Klik "Tambah Unit" untuk menambahkan.</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Harga & Margin --}}
                    <div class="space-y-3">
                        <label class="text-sm font-semibold text-foreground">Harga Produk</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- Harga Beli --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Harga Beli (Rp)</span>
                                    <div wire:loading wire:target="harga_beli" class="animate-in fade-in zoom-in duration-200">
                                        <x-lucide-loader-2 class="h-3 w-3 animate-spin text-muted-foreground" />
                                    </div>
                                </div>
                                <div class="relative">
                                    <input
                                        type="number"
                                        wire:model.live.debounce.250ms="harga_beli"
                                        class="flex h-10 w-full rounded-lg border border-input bg-background/50 px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
                                </div>
                            </div>

                            {{-- Margin --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Margin (Rp)</span>
                                    <div wire:loading wire:target="margin" class="animate-in fade-in zoom-in duration-200">
                                        <x-lucide-loader-2 class="h-3 w-3 animate-spin text-green-500" />
                                    </div>
                                </div>
                                <div class="relative">
                                    <input
                                        type="number"
                                        wire:model.live.debounce.250ms="margin"
                                        class="flex h-10 w-full rounded-lg border-2 border-green-500/30 bg-green-500/5 px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-green-500/30 transition-all font-semibold text-green-600">
                                </div>
                            </div>

                            {{-- Harga Jual --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Harga Jual (Rp)</span>
                                    <div wire:loading wire:target="harga_jual" class="animate-in fade-in zoom-in duration-200">
                                        <x-lucide-loader-2 class="h-3 w-3 animate-spin text-primary" />
                                    </div>
                                </div>
                                <div class="relative">
                                    <input
                                        type="number"
                                        wire:model.live.debounce.250ms="harga_jual"
                                        class="flex h-10 w-full rounded-lg border border-input bg-background/50 px-3 py-2 text-sm font-bold text-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status Card --}}
                    <div class="bg-muted/30 rounded-xl p-4 border border-border/50 flex items-center justify-between">
                        <div class="space-y-0.5">
                            <h4 class="text-sm font-bold text-foreground">Status Aktif</h4>
                            <p class="text-xs text-muted-foreground">Produk aktif dapat digunakan dalam transaksi</p>
                        </div>
                        <button
                            type="button"
                            wire:click="$toggle('is_active')"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring {{ $is_active ? 'bg-primary' : 'bg-muted-foreground/30' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-border">
                        <button
                            type="button"
                            wire:click="closeModal"
                            class="px-4 py-2 text-sm font-semibold rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2 text-sm font-semibold rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg shadow-primary/20 transition-all flex items-center gap-2">
                            <x-lucide-save class="h-4 w-4" />
                            {{ $isEdit ? 'Perbarui Produk' : 'Simpan Produk' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal Detail Produk --}}
    <div
        x-data="{ show: @entangle('showDetailModal') }"
        x-show="show"
        class="fixed inset-0 z-[110] overflow-y-auto"
        style="display: none;">
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
                @click="show = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block align-bottom bg-card w-full max-w-2xl rounded-2xl shadow-2xl border border-border text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle z-[111]">
                @if($selectedProduct)
                <div class="flex items-center justify-between p-6 border-b border-border bg-muted/30">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-primary/10 text-primary">
                            <x-lucide-package class="h-6 w-6" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-foreground">Detail Produk</h3>
                            <p class="text-sm text-muted-foreground">{{ $selectedProduct->nama }}</p>
                        </div>
                    </div>
                    <button @click="show = false" class="p-2 rounded-full hover:bg-muted transition-colors">
                        <x-lucide-x class="h-5 w-5 text-muted-foreground" />
                    </button>
                </div>

                <div class="p-6 space-y-8">
                    {{-- Info Utama --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-[11px] font-bold text-muted-foreground uppercase tracking-widest">Informasi Dasar</label>
                                <div class="mt-2 space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-border/50">
                                        <span class="text-sm text-muted-foreground">Barcode</span>
                                        <span class="text-sm font-mono font-bold bg-muted px-2 py-0.5 rounded">{{ $selectedProduct->kode_produk }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-border/50">
                                        <span class="text-sm text-muted-foreground">Kategori</span>
                                        <span class="text-sm font-semibold">{{ $selectedProduct->category->name ?? 'Tanpa Kategori' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-border/50">
                                        <span class="text-sm text-muted-foreground">Total Stok</span>
                                        <span class="text-sm font-bold text-orange-600 bg-orange-500/10 px-2.5 py-1 rounded-lg border border-orange-500/20">
                                            {{ (float)($selectedProduct->batches_sum_qty_sisa_base ?? 0) }} {{ $selectedProduct->base_unit }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-border/50">
                                        <span class="text-sm text-muted-foreground">Base Unit</span>
                                        <span class="text-sm font-semibold">{{ $selectedProduct->base_unit }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-sm text-muted-foreground">Status</span>
                                        @if($selectedProduct->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-500/10 text-green-500">
                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            Aktif
                                        </span>
                                        @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-500/10 text-red-500">
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Nonaktif
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-[11px] font-bold text-muted-foreground uppercase tracking-widest">Pricing & Margin</label>
                                <div class="mt-2 space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-border/50">
                                        <span class="text-sm text-muted-foreground">Harga Beli</span>
                                        <span class="text-sm font-bold text-foreground">Rp {{ number_format($selectedProduct->harga_beli_default, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-border/50">
                                        <span class="text-sm text-muted-foreground">Harga Jual</span>
                                        <span class="text-lg font-black text-primary">Rp {{ number_format($selectedProduct->harga_jual_default, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-sm text-muted-foreground">Margin Kontribusi</span>
                                        @php
                                        $marginVal = $selectedProduct->harga_jual_default - $selectedProduct->harga_beli_default;
                                        $marginPct = $selectedProduct->harga_beli_default > 0 ? ($marginVal / $selectedProduct->harga_beli_default) * 100 : 0;
                                        @endphp
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-foreground">Rp {{ number_format($marginVal, 0, ',', '.') }}</div>
                                            <div class="text-[10px] font-bold text-green-500">{{ number_format($marginPct, 1) }}% keunggulan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Multi Unit --}}
                    <div class="pt-6 border-t border-border">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                                <x-lucide-layers class="h-4 w-4" />
                            </div>
                            <h4 class="text-sm font-bold text-foreground">Konfigurasi Satuan Multi</h4>
                        </div>

                        @if($selectedProduct->units->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($selectedProduct->units as $unit)
                            <div class="flex items-center justify-between p-4 rounded-xl border border-border bg-muted/10 group hover:border-primary/50 hover:bg-primary/5 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-background border border-border group-hover:bg-primary/10 group-hover:border-primary/20 transition-all font-bold text-primary">
                                        {{ $unit->label }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-foreground">{{ $unit->label }}</div>
                                        <div class="text-[11px] text-muted-foreground uppercase tracking-tighter">Multiplier: {{ (int)$unit->multiplier }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[10px] text-muted-foreground uppercase font-semibold">Estimasi Harga</div>
                                    <div class="text-sm font-bold text-foreground">
                                        Rp {{ number_format($selectedProduct->harga_jual_default * $unit->multiplier, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="flex flex-col items-center justify-center py-10 border border-dashed border-border rounded-2xl bg-muted/5">
                            <x-lucide-layers-2 class="h-10 w-10 text-muted-foreground/30 mb-2" />
                            <p class="text-sm text-muted-foreground">Tidak ada konfigurasi satuan tambahan untuk produk ini.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 p-6 border-t border-border bg-muted/10">
                    <button
                        @click="show = false"
                        class="px-6 py-2.5 text-sm font-semibold rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg shadow-primary/20 transition-all">
                        Tutup Detail
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Riwayat Log --}}
    <div
        x-data="{ show: @entangle('showHistoryModal') }"
        x-show="show"
        class="fixed inset-0 z-[100] overflow-y-auto"
        style="display: none;">
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
                wire:click="closeHistoryModal"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block align-bottom bg-card w-full max-w-lg rounded-2xl shadow-2xl border border-border text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle z-[101]">
                <div class="flex items-center justify-between p-6 border-b border-border bg-muted/10">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-primary/10 text-primary">
                            <x-lucide-history class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-foreground">Riwayat Aktivitas</h3>
                            <p class="text-xs text-muted-foreground mt-0.5">Log perubahan data produk</p>
                        </div>
                    </div>
                    <button wire:click="closeHistoryModal" class="p-2 hover:bg-muted rounded-full transition-colors group">
                        <x-lucide-x class="h-5 w-5 text-muted-foreground group-hover:text-foreground" />
                    </button>
                </div>

                <div class="p-6 max-h-[500px] overflow-y-auto">
                    @if(count($logs) > 0)
                    <div class="relative pl-6 space-y-6 before:absolute before:inset-y-0 before:left-[7px] before:w-[2px] before:bg-border/50">
                        @foreach($logs as $log)
                        <div class="relative">
                            <div class="absolute -left-[23px] top-1 h-[10px] w-[10px] rounded-full border-2 border-background shadow-[0_0_0_2px_rgba(0,0,0,0.05)]
                                        {{ $log->action === 'created' ? 'bg-green-500' : ($log->action === 'toggled_status' ? 'bg-amber-500' : 'bg-primary') }}">
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold text-foreground flex items-center gap-2">
                                        @if($log->action === 'created')
                                        <x-lucide-plus-circle class="h-3.5 w-3.5 text-green-500" />
                                        Produk Dibuat
                                        @elseif($log->action === 'toggled_status')
                                        <x-lucide-power class="h-3.5 w-3.5 text-amber-500" />
                                        Perubahan Status
                                        @else
                                        <x-lucide-pencil class="h-3.5 w-3.5 text-primary" />
                                        Detail Diperbarui
                                        @endif
                                    </p>
                                    <time class="text-[10px] font-medium text-muted-foreground uppercase tracking-wider">
                                        {{ $log->created_at->translatedFormat('d M Y, H:i') }}
                                    </time>
                                </div>
                                <p class="text-sm text-muted-foreground leading-relaxed">
                                    {{ $log->description }}
                                </p>

                                @if($log->action === 'updated' && isset($log->changes['old']) && isset($log->changes['new']))
                                <div class="mt-2 p-2.5 rounded-lg bg-muted/30 border border-border/50 text-[11px] space-y-2">
                                    @php
                                    $fieldLabels = [
                                    'nama' => 'Nama',
                                    'category_id' => 'Kategori',
                                    'kode_produk' => 'Barcode',
                                    'base_unit' => 'Satuan',
                                    'harga_beli_default' => 'Harga Beli',
                                    'harga_jual_default' => 'Harga Jual',
                                    'is_active' => 'Status',
                                    ];
                                    @endphp
                                    @foreach($log->changes['old'] as $field => $oldValue)
                                    @php $newValue = $log->changes['new'][$field] ?? null; @endphp
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-foreground/70 w-20 shrink-0">{{ $fieldLabels[$field] ?? $field }}:</span>
                                        <div class="flex items-center gap-1.5 overflow-hidden">
                                            <span class="text-muted-foreground truncate max-w-[80px]" title="{{ $oldValue }}">
                                                @if(str_contains($field, 'harga'))
                                                Rp {{ number_format($oldValue, 0, ',', '.') }}
                                                @elseif($field === 'is_active')
                                                {{ $oldValue ? 'Aktif' : 'Nonaktif' }}
                                                @else
                                                {{ $oldValue }}
                                                @endif
                                            </span>
                                            <x-lucide-arrow-right class="h-2.5 w-2.5 text-muted-foreground/50 shrink-0" />
                                            <span class="text-primary font-bold truncate max-w-[80px]" title="{{ $newValue }}">
                                                @if(str_contains($field, 'harga'))
                                                Rp {{ number_format($newValue, 0, ',', '.') }}
                                                @elseif($field === 'is_active')
                                                {{ $newValue ? 'Aktif' : 'Nonaktif' }}
                                                @else
                                                {{ $newValue }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                @if($log->user)
                                <div class="flex items-center gap-1.5 mt-2">
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
                        <p class="text-xs text-muted-foreground mt-1">Semua aktivitas produk akan tercatat di sini.</p>
                    </div>
                    @endif
                </div>

                <div class="p-4 bg-muted/10 border-t border-border flex justify-end">
                    <button
                        wire:click="closeHistoryModal"
                        class="px-4 py-2 text-sm font-semibold rounded-lg bg-muted text-foreground hover:bg-muted/80 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div
        x-data="{ show: @entangle('showDeleteModal') }"
        x-show="show"
        class="fixed inset-0 z-[110] overflow-y-auto"
        style="display: none;">
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
                @click="show = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block align-bottom bg-card w-full max-w-md rounded-2xl shadow-2xl border border-border text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle z-[111]">
                <div class="p-6 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-destructive/10 mb-4">
                        <x-lucide-trash-2 class="h-8 w-8 text-destructive" />
                    </div>
                    <h3 class="text-xl font-bold text-foreground">Hapus Produk?</h3>
                    <p class="text-sm text-muted-foreground mt-2">
                        Apakah Anda yakin ingin menghapus produk ini?
                    </p>
                </div>

                <div class="flex items-center justify-center gap-3 p-6 border-t border-border bg-muted/10">
                    <button
                        @click="show = false"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-all">
                        Batal
                    </button>
                    <button
                        wire:click="delete"
                        class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-lg bg-destructive text-destructive-foreground hover:bg-destructive/90 shadow-lg shadow-destructive/20 transition-all">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification System --}}
    <div
        x-data="{ 
            toasts: [], 
            add(toast) { 
                this.toasts.push({ 
                    id: Date.now(), 
                    ...toast 
                }); 
                setTimeout(() => { 
                    this.toasts = this.toasts.filter(t => t.id !== toast.id); 
                }, 3000); 
            } 
        }"
        @toast.window="add($event.detail[0])"
        class="fixed bottom-4 right-4 z-[100] flex flex-col gap-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-8"
                class="flex items-center gap-3 px-4 py-3 rounded-lg border shadow-lg overflow-hidden min-w-[300px]"
                :class="{
                    'bg-card border-border border-l-4 border-l-green-500': toast.type === 'success',
                    'bg-card border-border border-l-4 border-l-red-500': toast.type === 'error'
                }">
                <div :class="toast.type === 'success' ? 'text-green-500' : 'text-red-500'">
                    <template x-if="toast.type === 'success'">
                        <x-lucide-check-circle class="h-5 w-5" />
                    </template>
                    <template x-if="toast.type === 'error'">
                        <x-lucide-alert-circle class="h-5 w-5" />
                    </template>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-foreground" x-text="toast.message"></p>
                </div>
                <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-muted-foreground hover:text-foreground">
                    <x-lucide-x class="h-4 w-4" />
                </button>
            </div>
        </template>
    </div>

</div>