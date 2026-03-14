<div class="h-[calc(100vh-80px)] -mt-6 -mx-4 sm:-mx-6 lg:-mx-8 overflow-hidden flex flex-col md:flex-row bg-background"
    x-data="{ 
        showCancelModal: @entangle('showCancelModal'),
        showCheckoutModal: @entangle('showCheckoutModal'),
        showErrorModal: @entangle('showErrorModal')
    }">
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
    <!-- LEFT PANEL (65%) -->
    <div class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8 space-y-6">

        <!-- 1. Transaction Header Card -->
        <div class="rounded-2xl border border-border/50 bg-card/50 backdrop-blur-md p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button wire:click="handleBack" class="p-2.5 rounded-xl border border-border hover:bg-muted transition-all group">
                        <x-lucide-arrow-left class="h-5 w-5 text-muted-foreground group-hover:text-foreground" />
                    </button>
                    <div>
                        @if(!$currentSale)
                        <h2 class="text-xl font-bold text-muted-foreground/60">Tidak ada transaksi aktif</h2>
                        <p class="text-sm text-muted-foreground">Klik tombol di samping untuk memulai</p>
                        @else
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">
                                <x-lucide-clock class="h-3.5 w-3.5" />
                                DRAFT
                            </span>
                            <h2 class="text-xl font-mono font-bold tracking-tight">{{ $currentSale->invoice_number }}</h2>
                        </div>
                        <p class="text-sm text-muted-foreground mt-0.5">Kasir: {{ auth()->user()->name }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(!$currentSale)
                    <button wire:click="startNewTransaction" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-primary-foreground font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                        <x-lucide-plus class="h-5 w-5" />
                        Transaksi Baru
                    </button>
                    @else
                    @php
                    $hasStockIssue = $currentSale->items->contains(function($item) {
                    return $item->qty_base > ($item->product->total_stock ?? 0);
                    });
                    @endphp
                    <button @click="showCancelModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-destructive/30 text-destructive font-bold hover:bg-destructive/10 transition-colors">
                        <x-lucide-x class="h-5 w-5" />
                        Batalkan
                    </button>
                    <button
                        wire:click="openCheckout"
                        @if($hasStockIssue) disabled @endif
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed">
                        <x-lucide-check-circle class="h-5 w-5" />
                        Selesaikan
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. Product Search Component -->
        <div class="relative">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <x-lucide-search class="h-6 w-6 text-muted-foreground" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    wire:keydown.enter="selectFirstResult"
                    @if(!$currentSale) disabled @endif
                    placeholder="Cari produk (nama atau barcode)..."
                    class="block w-full h-14 pl-12 pr-4 text-lg border-2 border-border/50 rounded-2xl bg-card focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all disabled:opacity-50 disabled:bg-muted">

                {{-- Unit Selector di Search Bar --}}
                @if(count($activeProductUnits) > 0)
                <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1.5 z-10 p-1 bg-card/80 backdrop-blur rounded-xl border border-border/50 shadow-sm">
                    @foreach($activeProductUnits as $unit)
                    <button
                        wire:click="setActiveUnit({{ $unit['id'] ?? 'null' }})"
                        class="px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all
                            {{ $activeUnitId === $unit['id'] 
                                ? 'bg-primary text-primary-foreground shadow-lg shadow-primary/20' 
                                : 'bg-muted/50 text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                        {{ $unit['label'] }}
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Search Dropdown Results -->
            @if(count($searchResults) > 0)
            <div class="absolute left-0 right-0 mt-2 p-2 bg-card/90 backdrop-blur-xl border border-border rounded-2xl shadow-2xl z-50 overflow-hidden">
                <div class="max-h-[400px] overflow-y-auto space-y-1">
                    @foreach($searchResults as $product)
                    <div
                        wire:key="search-result-{{ $product->id }}"
                        wire:click="addToCart({{ $product->id }})"
                        class="flex items-center justify-between p-3 rounded-xl hover:bg-primary/5 transition-colors border border-transparent hover:border-primary/20 group cursor-pointer">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                <x-lucide-package class="h-5 w-5" />
                            </div>
                            <div>
                                <h4 class="font-bold text-sm text-foreground">{{ $product->nama }}</h4>
                                <p class="text-[10px] text-muted-foreground font-mono uppercase">{{ $product->kode_produk }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="text-right">
                                <div class="font-bold text-primary">Rp {{ number_format($product->harga_jual_default, 0, ',', '.') }}</div>
                                <span class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full {{ $product->estimated_stock > 0 ? 'bg-secondary text-secondary-foreground' : 'bg-destructive/10 text-destructive border border-destructive/20' }}">
                                    Stok: {{ $product->estimated_stock }} {{ $product->base_unit }}
                                </span>
                            </div>
                            <div class="p-2 rounded-lg bg-primary text-white hover:bg-primary/90 transition-colors shadow-md shadow-primary/20">
                                <x-lucide-plus class="h-5 w-5" />
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- 3. Quick Info Card -->
        <div class="flex-1 flex flex-col justify-center items-center rounded-2xl border border-dashed border-border/60 bg-muted/5 p-12 text-center text-muted-foreground">
            @if(!$currentSale)
            <div class="p-6 bg-muted/30 rounded-full mb-6">
                <x-lucide-alert-circle class="h-16 w-16 opacity-30" />
            </div>
            <h3 class="text-xl font-bold">Siap untuk transaksi baru</h3>
            <p class="mt-2 text-sm max-w-sm">Klik tombol "Transaksi Baru" di atas untuk mulai melayani pelanggan.</p>
            @else
            <div class="w-full max-w-md text-left space-y-4">
                <h3 class="text-lg font-bold text-foreground flex items-center gap-2">
                    <x-lucide-help-circle class="h-5 w-5 text-primary" />
                    Cara Menggunakan:
                </h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3">
                        <span class="h-5 w-5 rounded bg-primary/10 text-primary flex items-center justify-center text-[10px] font-bold mt-0.5">1</span>
                        <span>Cari produk menggunakan <strong>nama</strong> atau <strong>barcode</strong>. Tekan <strong>Enter</strong> untuk hasil teratas.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="h-5 w-5 rounded bg-primary/10 text-primary flex items-center justify-center text-[10px] font-bold mt-0.5">2</span>
                        <span>Klik di mana saja pada baris produk untuk tambah ke keranjang.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="h-5 w-5 rounded bg-primary/10 text-primary flex items-center justify-center text-[10px] font-bold mt-0.5">3</span>
                        <span>Ubah <strong>quantity</strong> di keranjang. Input akan sinkron dengan tombol +/-.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="h-5 w-5 rounded bg-primary/10 text-primary flex items-center justify-center text-[10px] font-bold mt-0.5">4</span>
                        <span>Pilih unit yang sesuai untuk otomatis mengonversi stok dan harga.</span>
                    </li>
                </ul>
            </div>
            @endif
        </div>
    </div>

    <!-- RIGHT PANEL (35% / Cart) -->
    <div class="w-full md:w-[384px] lg:w-[440px] border-l border-border bg-card flex flex-col shadow-2xl">
        <!-- Cart Header -->
        <div class="p-6 border-b border-border bg-muted/20 flex items-center justify-between">
            <h3 class="font-bold flex items-center gap-2">
                <x-lucide-shopping-cart class="h-5 w-5 text-primary" />
                Keranjang
            </h3>
            @if($currentSale)
            <span class="text-xs font-mono font-medium text-muted-foreground">{{ $currentSale->invoice_number }}</span>
            @endif
        </div>

        <!-- Cart Items (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @if(!$currentSale)
            <div class="h-full flex flex-col items-center justify-center text-center opacity-30 select-none">
                <x-lucide-shopping-cart class="h-20 w-20 mb-4" />
                <h4 class="font-bold">Belum ada transaksi</h4>
                <p class="text-xs px-12 mt-2">Klik Transaksi Baru untuk memulai</p>
            </div>
            @elseif($currentSale->items->count() === 0)
            <div class="h-full flex flex-col items-center justify-center text-center text-muted-foreground select-none">
                <div class="p-4 bg-muted rounded-full mb-4">
                    <x-lucide-shopping-cart class="h-12 w-12 opacity-50" />
                </div>
                <h4 class="font-bold">Keranjang kosong</h4>
                <p class="text-xs px-12 mt-2">Cari dan tambahkan produk untuk memulai belanja</p>
            </div>
            @else
            @foreach($currentSale->items as $item)
            @php
            $availBase = $item->is_bonus_item
            ? $this->getAvailableBonusStock($item->product_id)
            : ($item->product->total_stock ?? 0);
            $isStockInsufficient = $item->qty_base > $availBase;
            @endphp
            <div
                wire:key="cart-item-{{ $item->id }}"
                class="p-4 rounded-3xl border-2 transition-all group {{ $isStockInsufficient ? 'bg-destructive/15 border-destructive ring-4 ring-destructive/10' : 'bg-card border-border/50 hover:border-primary/50 hover:shadow-lg hover:shadow-primary/5' }}">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <div class="flex items-center gap-2 mt-1">
                            <h4 class="font-bold text-sm {{ $item->is_bonus_item ? ($isStockInsufficient ? 'text-destructive line-through' : 'text-blue-700/50 line-through') : ($isStockInsufficient ? 'text-destructive' : 'text-foreground') }}">{{ $item->product->nama }}</h4>
                            @if($item->is_bonus_item)
                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[8px] font-black {{ $isStockInsufficient ? 'bg-destructive/20 text-destructive border-destructive/30' : 'bg-blue-500/10 text-blue-600 border-blue-500/20' }} border uppercase tracking-tighter">
                                Bonus
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <select
                                wire:change="changeUnit({{ $item->id }}, $event.target.value)"
                                class="text-[10px] font-bold bg-white dark:bg-muted border border-border rounded px-1.5 py-0.5 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all cursor-pointer">
                                <option value="" disabled>Unit</option>
                                <option value="base" @if($item->unit_multiplier == 1) selected @endif>
                                    {{ $item->product->base_unit }}
                                </option>
                                @foreach($item->product->units as $unit)
                                <option value="{{ $unit->id }}" @if($item->unit_label == $unit->label) selected @endif>
                                    {{ $unit->label }} (x{{ $unit->multiplier }})
                                </option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-muted-foreground">@ Rp {{ number_format($item->harga_jual_per_unit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 items-end">
                        <button wire:click="removeItem({{ $item->id }})" class="p-1.5 text-muted-foreground hover:text-destructive hover:bg-destructive/10 rounded-lg transition-all opacity-0 group-hover:opacity-100">
                            <x-lucide-trash-2 class="h-4 w-4" />
                        </button>

                        {{-- Jadikan Bonus Button --}}
                        @if($item->product->bonus_stock > 0 || $item->is_bonus_item)
                        <button
                            wire:click="toggleBonus({{ $item->id }})"
                            title="{{ $item->is_bonus_item ? 'Klik untuk jadikan penjualan reguler' : 'Klik untuk ambil dari stok bonus (Gratis)' }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border-2 {{ $item->is_bonus_item ? 'bg-muted border-border text-muted-foreground hover:bg-muted/80' : 'border-blue-200 text-blue-600 bg-blue-50 hover:bg-blue-100 shadow-sm shadow-blue-500/10' }} transition-all opacity-0 group-hover:opacity-100">
                            @if($item->is_bonus_item)
                            <x-lucide-x class="h-4 w-4" />
                            <span class="text-xs font-bold uppercase tracking-tight">Batal</span>
                            @else
                            <span class="text-base">🎁</span>
                            <span class="text-xs font-bold uppercase tracking-tight">Bonus</span>
                            @endif
                        </button>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    @php
                    $visualQty = $item->qty_base / $item->unit_multiplier;
                    $remainingInUnit = $availBase / $item->unit_multiplier;
                    @endphp
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center bg-white dark:bg-muted/30 rounded-lg border border-border shadow-sm overflow-hidden h-9">
                            <button wire:click="adjustQty({{ $item->id }}, -1)" class="w-9 flex items-center justify-center hover:bg-muted transition-colors border-r border-border">
                                <x-lucide-minus class="h-3.5 w-3.5" />
                            </button>
                            <input
                                type="text"
                                inputmode="numeric"
                                pattern="[0-9]*"
                                wire:key="qty-input-{{ $item->id }}-{{ $visualQty }}"
                                value="{{ $visualQty }}"
                                onfocus="this.select()"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                class="w-14 text-center text-sm font-black bg-transparent border-none p-0 focus:ring-0 {{ $isStockInsufficient ? 'text-destructive' : 'text-foreground' }}"
                                wire:change="updateQty( {{ $item->id }}, $event.target.value)">
                            <button wire:click="adjustQty({{ $item->id }}, 1)" class="w-9 flex items-center justify-center hover:bg-muted transition-colors border-l border-border">
                                <x-lucide-plus class="h-3.5 w-3.5" />
                            </button>
                        </div>
                        <span class="text-[9px] font-bold {{ $isStockInsufficient ? 'text-destructive animate-pulse' : 'text-muted-foreground' }} px-1 uppercase tracking-tighter">
                            {{ $item->is_bonus_item ? 'Sisa Bonus' : 'Sisa' }}: {{ number_format($remainingInUnit, 1, ',', '.') }} {{ $item->unit_label }}
                        </span>
                    </div>
                    <div class="text-right">
                        @if($item->is_bonus_item)
                        <div class="text-[10px] line-through text-muted-foreground font-bold">Rp {{ number_format($item->product->harga_jual_default * ($item->unit_multiplier ?: 1), 0, ',', '.') }}</div>
                        <div class="flex items-center justify-end gap-1.5 {{ $isStockInsufficient ? 'text-destructive' : 'text-blue-600' }} font-bold">
                            <span class="text-xs">🎁 Item Bonus</span>
                            <span class="text-sm">Rp 0</span>
                        </div>
                        @else
                        <div class="text-sm font-black text-primary">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        <!-- Cart Footer (Sticky) -->
        <div class="p-6 border-t border-border bg-muted/30 space-y-3">
            <div class="flex justify-between items-center text-sm font-medium text-muted-foreground">
                <span>Subtotal</span>
                <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
            </div>

            @if($this->bonusCount > 0)
            <div class="flex justify-between items-center text-sm font-medium text-blue-600">
                <span>Item Bonus ({{ $this->bonusCount }} unit)</span>
                <span>- Rp {{ number_format($this->bonusDiscount, 0, ',', '.') }}</span>
            </div>
            @endif

            <div class="border-t border-border/50 pt-2">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-base font-bold">TOTAL</span>
                    <span class="text-2xl font-black text-primary">Rp {{ number_format($currentSale?->total ?? 0, 0, ',', '.') }}</span>
                </div>
                <p class="text-[10px] text-muted-foreground italic text-right">(Item bonus tidak mengurangi total bayar)</p>
            </div>

            <div class="flex justify-between items-center text-[10px] text-muted-foreground font-medium uppercase tracking-wider">
                <span>{{ $currentSale?->items->count() ?? 0 }} Baris Item</span>
                <span>•</span>
                <span>{{ $currentSale?->items->sum('qty_base') ?? 0 }} Total Unit</span>
            </div>
        </div>
    </div>

    <!-- 4. Checkout Modal -->
    <div
        x-show="showCheckoutModal"
        x-cloak
        class="fixed inset-0 z-[100] overflow-y-auto"
        @keydown.escape.window="showCheckoutModal = false">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div x-show="showCheckoutModal" x-transition.opacity class="fixed inset-0 bg-background/80 backdrop-blur-sm" @click="showCheckoutModal = false"></div>

            <div
                x-show="showCheckoutModal"
                x-transition.scale.origin.center
                class="relative inline-block align-bottom bg-card rounded-3xl shadow-2xl border border-border text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle w-full max-w-xl z-10">
                <!-- Modal Header -->
                <div class="px-8 py-6 border-b border-border bg-muted/30 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-extrabold flex items-center gap-3">
                            @if($checkoutStep === 1)
                            <x-lucide-shopping-cart class="h-6 w-6 text-primary" />
                            Ringkasan Transaksi
                            @elseif($checkoutStep === 2)
                            <x-lucide-credit-card class="h-6 w-6 text-primary" />
                            Pilih Pembayaran
                            @else
                            <x-lucide-user-plus class="h-6 w-6 text-primary" />
                            Data Pelanggan
                            @endif
                        </h3>
                    </div>
                    <button @click="showCheckoutModal = false" class="p-2 hover:bg-muted rounded-full transition-colors"><x-lucide-x class="h-5 w-5" /></button>
                </div>

                <!-- Modal Body -->
                <div class="px-8 py-8 min-h-[300px]">

                    <!-- STEP 1: REVIEW -->
                    @if($checkoutStep === 1)
                    <div class="space-y-6">
                        <div class="p-4 rounded-xl bg-muted/50 border border-border flex justify-between items-center">
                            <span class="text-sm font-medium">Invoice</span>
                            <span class="font-mono text-sm font-bold">{{ $currentSale?->invoice_number }}</span>
                        </div>

                        <div class="space-y-2 max-h-[250px] overflow-y-auto pr-2">
                            @foreach($currentSale?->items ?? [] as $item)
                            <div class="flex justify-between text-sm">
                                <div class="flex flex-col">
                                    <span class="text-muted-foreground {{ $item->is_bonus_item ? 'line-through opacity-50' : '' }}">
                                        {{ $item->product->nama }} <span class="font-bold text-foreground mx-1">x{{ $item->qty_base / $item->unit_multiplier }} {{ $item->unit_label }}</span>
                                    </span>
                                    @if($item->is_bonus_item)
                                    <span class="text-[10px] text-blue-600 font-bold">🎁 Item Bonus — Rp 0</span>
                                    @endif
                                </div>
                                <span class="font-bold font-mono {{ $item->is_bonus_item ? 'text-blue-600' : '' }}">
                                    {{ $item->is_bonus_item ? 'Rp 0' : 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                            @endforeach
                        </div>

                        <div class="pt-4 border-t border-border space-y-2">
                            <div class="flex justify-between text-sm font-medium text-muted-foreground italic">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($this->bonusCount > 0)
                            <div class="flex justify-between text-sm font-medium text-blue-600">
                                <span>Item Bonus ({{ $this->bonusCount }} unit)</span>
                                <span>- Rp {{ number_format($this->bonusDiscount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-border flex justify-between items-center">
                            <span class="text-lg font-bold">Total Pembayaran</span>
                            <span class="text-2xl font-black text-primary">Rp {{ number_format($currentSale?->total ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- STEP 2: PAYMENT METHOD -->
                    @elseif($checkoutStep === 2)
                    <div class="space-y-4">
                        <!-- Tunai -->
                        <label class="relative flex items-center p-5 rounded-2xl border-2 cursor-pointer transition-all hover:bg-emerald-50/50 {{ $paymentMethod === 'cash' ? 'border-emerald-500 bg-emerald-50/30' : 'border-border' }}">
                            <input type="radio" name="payment_method" value="cash" wire:model.live="paymentMethod" class="sr-only">
                            <div class="p-3 rounded-xl bg-emerald-100 text-emerald-600 mr-4">
                                <x-lucide-banknote class="h-8 w-8" />
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-emerald-900">Pembayaran Tunai</h4>
                                <p class="text-xs text-emerald-600/70 font-medium">Transaksi lunas dibayar saat ini.</p>
                            </div>
                            <div class="h-6 w-6 rounded-full border-2 flex items-center justify-center {{ $paymentMethod === 'cash' ? 'border-emerald-500' : 'border-border' }}">
                                @if($paymentMethod === 'cash') <div class="h-3 w-3 rounded-full bg-emerald-500"></div> @endif
                            </div>
                        </label>

                        <!-- Hutang -->
                        <label class="relative flex items-center p-5 rounded-2xl border-2 cursor-pointer transition-all hover:bg-amber-50/50 {{ $paymentMethod === 'debt' ? 'border-amber-500 bg-amber-50/30' : 'border-border' }}">
                            <input type="radio" name="payment_method" value="debt" wire:model.live="paymentMethod" class="sr-only">
                            <div class="p-3 rounded-xl bg-amber-100 text-amber-600 mr-4">
                                <x-lucide-credit-card class="h-8 w-8" />
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-amber-900">Hutang / Piutang</h4>
                                <p class="text-xs text-amber-600/70 font-medium">Bayar nanti (membutuhkan data pelanggan).</p>
                            </div>
                            <div class="h-6 w-6 rounded-full border-2 flex items-center justify-center {{ $paymentMethod === 'debt' ? 'border-amber-500' : 'border-border' }}">
                                @if($paymentMethod === 'debt') <div class="h-3 w-3 rounded-full bg-amber-500"></div> @endif
                            </div>
                        </label>

                        <div class="mt-8 p-4 bg-primary/5 rounded-2xl flex flex-col gap-4 outline outline-1 outline-primary/20">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-primary">Total Tagihan</span>
                                <span class="text-xl font-black text-primary">Rp {{ number_format($currentSale?->total ?? 0, 0, ',', '.') }}</span>
                            </div>

                            @if($paymentMethod === 'cash')
                            <div class="pt-4 border-t border-primary/10 space-y-3">
                                <label class="text-xs font-bold text-primary uppercase tracking-wider">Uang Diterima (Cash)</label>
                                <div class="relative" x-data="{ 
                                    total: @js($currentSale?->total ?? 0),
                                    displayValue: '',
                                    formatDisplay(val) {
                                        if (!val || val === '0') return '0';
                                        let num = parseInt(val.toString().replace(/\D/g, '')) || 0;
                                        if (num === 0) return '0';
                                        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                    },
                                    updateRaw(val) {
                                        let raw = val.replace(/\D/g, '');
                                        let numeric = parseInt(raw) || 0;
                                        this.displayValue = this.formatDisplay(numeric);
                                        $wire.set('cashReceived', numeric);
                                    }
                                }" x-init="displayValue = formatDisplay($wire.get('cashReceived'))">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-muted-foreground">Rp</span>
                                    <input
                                        type="text"
                                        x-model="displayValue"
                                        x-on:input="updateRaw($event.target.value)"
                                        class="w-full h-14 pl-12 pr-4 rounded-2xl bg-white dark:bg-muted border-2 border-primary/20 focus:border-primary focus:ring-4 focus:ring-primary/10 font-black text-2xl transition-all"
                                        placeholder="0">
                                </div>
                                <div class="flex justify-between items-center px-1">
                                    <span class="text-xs font-bold text-muted-foreground uppercase">Kembalian:</span>
                                    <span class="text-xl font-black text-emerald-600">Rp {{ number_format($cashChange, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- STEP 3: CUSTOMER DATA -->
                    @elseif($checkoutStep === 3)
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-bold ml-1">Nama Pelanggan <span class="text-destructive">*</span></label>
                                <input type="text" wire:model.live="customerName" class="w-full h-12 rounded-2xl bg-muted/30 border-border px-4 font-medium focus:ring-primary focus:border-primary" placeholder="Nama lengkap...">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold ml-1">Nomor HP</label>
                                <input type="text" wire:model.live="customerPhone" class="w-full h-12 rounded-2xl bg-muted/30 border-border px-4 font-medium focus:ring-primary focus:border-primary" placeholder="08xxx (Opsional)...">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold ml-1">Jaminan Hutang</label>
                            <textarea wire:model.live="jaminan" class="w-full rounded-2xl bg-muted/30 border-border px-4 py-3 font-medium focus:ring-primary focus:border-primary" rows="2" placeholder="Contoh: KTP ditahan, Nota toko, dll (Opsional)..."></textarea>
                            <p class="text-[10px] text-muted-foreground ml-1">Opsional sebagai syarat tambahan transaksi hutang.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-bold ml-1">Alamat (Opsional)</label>
                            <textarea wire:model.live="customerAddress" class="w-full rounded-2xl bg-muted/30 border-border px-4 py-3 font-medium focus:ring-primary focus:border-primary" rows="2" placeholder="Masukkan alamat lengkap..."></textarea>
                        </div>

                        <!-- Partial Payment Input (Start of Debt) -->
                        <div class="pt-6 border-t border-border space-y-3">
                            <label class="text-sm font-bold text-primary flex justify-between px-1">
                                <span>Pembayaran Awal (Tunai)</span>
                                <span class="text-[10px] text-muted-foreground uppercase">Opsional</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-muted-foreground">Rp</span>
                                <input
                                    type="number"
                                    wire:model.live="partialDebtAmount"
                                    step="1"
                                    min="0"
                                    @if($currentSale) max="{{ $currentSale->total }}" @endif
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    class="w-full h-14 pl-12 pr-4 rounded-2xl bg-primary/5 border-primary/20 focus:ring-primary focus:border-primary font-black text-xl"
                                    placeholder="0">
                            </div>

                            @if($currentSale && $partialDebtAmount > 0)
                            <div class="flex justify-between items-center px-1">
                                <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Sisa Piutang:</span>
                                <span class="text-xs font-black text-red-500">Rp {{ number_format($currentSale->total - $partialDebtAmount, 0, ',', '.') }}</span>
                            </div>
                            @endif

                            @if($currentSale && $partialDebtAmount == $currentSale->total)
                            <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-start gap-2">
                                <x-lucide-alert-triangle class="h-4 w-4 text-amber-500 flex-shrink-0 mt-0.5" />
                                <p class="text-[10px] leading-tight text-amber-700 font-medium italic">
                                    Nominal senilai lunas. Silakan gunakan metode <strong>TUNAI / CASH</strong> di langkah sebelumnya.
                                </p>
                            </div>
                            @endif
                        </div>

                        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-amber-500 rounded-xl text-white">
                                    <x-lucide-alert-triangle class="h-5 w-5" />
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-amber-800 uppercase tracking-widest">Ringkasan Hutang</div>
                                    <div class="text-lg font-black text-amber-900">Rp {{ number_format($currentSale?->total ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] font-bold text-amber-800 uppercase tracking-widest text-right">Dibayar: 0</div>
                                <div class="text-[10px] font-bold text-amber-800 uppercase tracking-widest text-right">Sisa: Rp {{ number_format($currentSale?->total ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="px-8 py-6 border-t border-border bg-muted/30 flex items-center justify-between">
                    <div>
                        @if($checkoutStep > 1)
                        <button wire:click="prevStep" class="px-6 py-2.5 rounded-xl border border-border font-bold hover:bg-muted transition-all">
                            Kembali
                        </button>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @if($checkoutStep === 1)
                        <button wire:click="nextStep" class="px-8 py-2.5 rounded-xl bg-primary text-white font-bold hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                            Lanjutkan ke Pembayaran
                        </button>
                        @elseif($checkoutStep === 2)
                        @if($paymentMethod === 'cash')
                        <button
                            wire:click="nextStep"
                            wire:loading.attr="disabled"
                            @if($paymentMethod==='cash' && ($cashReceived < ($currentSale?->total ?? 0) || $cashReceived <= 0)) disabled @endif
                                class="px-8 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:grayscale disabled:cursor-not-allowed">
                                <span wire:loading.remove wire:target="nextStep">Bayar Tunai</span>
                                <span wire:loading wire:target="nextStep" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                        </button>
                        @else
                        <button wire:click="nextStep" class="px-8 py-2.5 rounded-xl bg-amber-500 text-white font-bold hover:bg-amber-600 transition-all shadow-lg shadow-amber-500/20">
                            Lanjut ke Data Pelanggan
                        </button>
                        @endif
                        @else
                        @php
                        $isDebtValid = !empty($customerName);
                        $isPartialFull = $paymentMethod === 'debt' && $currentSale && $partialDebtAmount == $currentSale->total;
                        $canSelesaikan = (($paymentMethod === 'cash') || ($paymentMethod === 'debt' && $isDebtValid)) && !$isPartialFull;
                        @endphp
                        <button
                            wire:click="finalizeTransaction"
                            wire:loading.attr="disabled"
                            @if(!$canSelesaikan) disabled @endif
                            class="px-8 py-2.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:grayscale">
                            <span wire:loading.remove wire:target="finalizeTransaction">Selesaikan ({{ $paymentMethod === 'debt' ? 'Hutang' : 'Tunai' }})</span>
                            <span wire:loading wire:target="finalizeTransaction" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ $paymentMethod === 'debt' ? 'Memproses transaksi hutang...' : 'Memproses...' }}
                            </span>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Custom Cancel Modal -->
    <div
        x-show="showCancelModal"
        x-cloak
        class="fixed inset-0 z-[110] overflow-y-auto"
        @keydown.escape.window="showCancelModal = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showCancelModal" x-transition.opacity class="fixed inset-0 bg-background/80 backdrop-blur-md" @click="showCancelModal = false"></div>

            <div
                x-show="showCancelModal"
                x-transition.scale.origin.center
                class="relative bg-card rounded-3xl shadow-2xl border-2 border-destructive/20 p-8 w-full max-w-md z-10 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-destructive/10 text-destructive mb-6">
                    <x-lucide-alert-triangle class="h-10 w-10" />
                </div>

                <h3 class="text-2xl font-black text-foreground mb-2">Batalkan Transaksi?</h3>
                <p class="text-muted-foreground mb-8">Seluruh item di keranjang akan dihapus dan transaksi ini akan dibatalkan secara permanen.</p>

                <div class="flex flex-col gap-3">
                    <button
                        wire:click="cancelTransaction"
                        @click="showCancelModal = false"
                        class="w-full py-4 rounded-2xl bg-destructive text-destructive-foreground font-black text-lg hover:bg-destructive/90 transition-all shadow-xl shadow-destructive/20">
                        Ya, Batalkan Sekarang
                    </button>
                    <button
                        @click="showCancelModal = false"
                        class="w-full py-4 rounded-2xl bg-muted font-bold text-foreground hover:bg-muted/80 transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Safe Navigation Guard Modal -->
    <div
        x-data="{ show: @entangle('showNavGuardModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[120] overflow-y-auto"
        @keydown.escape.window="show = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-background/80 backdrop-blur-md" @click="show = false"></div>

            <div
                x-show="show"
                x-transition.scale.origin.center
                class="relative bg-card rounded-3xl shadow-2xl border border-border p-8 w-full max-w-md z-10 text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-amber-100 text-amber-600 mb-6">
                    <x-lucide-log-out class="h-10 w-10" />
                </div>

                <h3 class="text-2xl font-black text-foreground mb-2">Keluar dari POS?</h3>
                <p class="text-muted-foreground mb-8 text-sm">Transaksi yang sedang berjalan akan dibatalkan/dihapus jika Anda keluar sekarang.</p>

                <div class="grid grid-cols-1 gap-3">
                    <button
                        wire:click="cancelTransactionFromGuard"
                        class="w-full py-4 rounded-2xl bg-destructive text-white font-black text-lg hover:bg-destructive/90 transition-all shadow-xl shadow-destructive/20">
                        Ya, Batalkan & Keluar
                    </button>
                    <button
                        @click="show = false"
                        class="w-full py-4 rounded-2xl bg-muted font-bold text-foreground hover:bg-muted/80 transition-all">
                        Batal (Tetap di Sini)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. Transaction Completed Modal (Success) -->
    <div
        x-data="{ show: @entangle('showSuccessModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[130] overflow-y-auto"
        @keydown.escape.window="show = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-background/90 backdrop-blur-xl" @click="show = false"></div>

            <div
                x-show="show"
                x-transition.scale.origin.center
                class="relative bg-card rounded-[40px] shadow-[0_32px_64px_-12px_rgba(0,0,0,0.3)] border-4 border-emerald-500/20 p-10 w-full max-w-lg z-10 text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mb-8 animate-bounce">
                    <x-lucide-check class="h-12 w-12" />
                </div>

                <h3 class="text-3xl font-black text-foreground mb-2">Pembayaran Berhasil!</h3>
                <p class="text-muted-foreground mb-10">Transaksi telah tercatat dan stok telah diperbarui.</p>

                <div class="bg-muted/30 rounded-3xl p-6 mb-10 text-left space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Nomor Invoice</span>
                        <span class="font-mono font-bold">{{ $successInvoice }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Total Transaksi</span>
                        <span class="font-bold text-lg">Rp {{ number_format($successTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Metode Bayar</span>
                        <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-[10px] font-black uppercase tracking-widest">{{ $successMethod }}</span>
                    </div>
                    @if($successMethod === 'debt')
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Pelanggan</span>
                        <span class="font-bold">{{ $successCustomer }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">DP (Dibayar Sekarang)</span>
                        <span class="font-bold text-emerald-600">Rp {{ number_format($successPartialAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Sisa Hutang</span>
                        <span class="font-bold text-red-500">Rp {{ number_format($successTotal - $successPartialAmount, 0, ',', '.') }}</span>
                    </div>
                    @else
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Bayar (Cash)</span>
                        <span class="font-bold">Rp {{ number_format($successCashReceived, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Kembalian</span>
                        <span class="font-bold text-emerald-600">Rp {{ number_format($successCashChange, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <button
                        wire:click="reprintLastInvoice"
                        class="flex items-center justify-center gap-2 py-5 rounded-2xl border-2 border-primary text-primary font-black text-lg hover:bg-primary/5 transition-all">
                        <x-lucide-printer class="h-6 w-6" />
                        Cetak Struk
                    </button>
                    <button
                        wire:click="resetPos"
                        class="flex items-center justify-center gap-2 py-5 rounded-2xl bg-primary text-white font-black text-lg hover:bg-primary/90 transition-all shadow-2xl shadow-primary/20">
                        Selesai
                        <x-lucide-arrow-right class="h-6 w-6" />
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- 8. Transaction Error Modal -->
    <div
        x-data="{ show: @entangle('showErrorModal') }"
        x-show="show"
        x-cloak
        class="fixed inset-0 z-[140] overflow-y-auto"
        @keydown.escape.window="show = false">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-background/80 backdrop-blur-md" @click="show = false"></div>

            <div
                x-show="show"
                x-transition.scale.origin.center
                class="relative bg-card rounded-[40px] shadow-[0_32px_64px_-12px_rgba(0,0,0,0.3)] border-4 border-destructive/20 p-10 w-full max-w-lg z-10 text-center">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-destructive/10 text-destructive mb-8">
                    <x-lucide-x-circle class="h-12 w-12" />
                </div>

                <h3 class="text-3xl font-black text-foreground mb-2">Gagal Memproses!</h3>
                <p class="text-muted-foreground mb-8">Terjadi kesalahan saat menyimpan transaksi.</p>

                <div class="bg-destructive/5 border-2 border-destructive/10 rounded-3xl p-6 mb-10 text-center">
                    <p class="text-destructive font-bold text-lg leading-relaxed">
                        {{ $errorMessage }}
                    </p>
                </div>

                <div class="grid grid-cols-1">
                    <button
                        @click="show = false"
                        class="flex items-center justify-center gap-2 py-5 rounded-2xl bg-muted font-black text-xl hover:bg-muted/80 transition-all">
                        Tutup & Periksa Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 9. PRINT INVOICE TEMPLATE (Hidden from UI) -->
    <div id="invoice-print" class="hidden print:block p-4 px-12 font-mono text-[18px] leading-tight w-[80mm]">
        <div class="text-center mb-4">
            <h2 class="text-lg font-bold uppercase">UD.SEPAN</h2>
            <p class="uppercase text-[14px]">JL. ANTANG JUNGAN KECAMATAN RUNGAN HULU KABUPATEN GUNUNG MAS</p>
            <div class="border-b border-dashed border-black my-2"></div>
            <p class="font-bold">INVOICE</p>
        </div>

        <div class="space-y-1 mb-4 text-[14px]">
            <div class="flex justify-between">
                <span>No :</span>
                <span class="text-[18px]">{{ $successInvoice }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tgl:</span>
                <span>{{ $successDate }}</span>
            </div>
            <div class="flex justify-between">
                <span>Ksr:</span>
                <span>{{ auth()->user()->name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Byr:</span>
                <span class="uppercase font-bold">{{ $successMethod }}</span>
            </div>
        </div>

        <div class="border-b border-dashed border-black mb-2"></div>

        <div class="space-y-2 mb-4">
            @foreach($successItems as $item)
            @if(is_array($item))
            <div>
                <div class="font-bold text-[16px] {{ ($item['is_bonus_item'] ?? false) ? 'line-through opacity-50' : '' }}">
                    {{ $item['nama'] ?? '' }}
                </div>
                <div class="flex justify-between text-[14px]">
                    @if($item['is_bonus_item'] ?? false)
                    <span>{{ number_format($item['qty'] ?? 0, 0) }} {{ $item['unit'] ?? '' }} (Bonus)</span>
                    <span>0</span>
                    @else
                    <span>{{ number_format($item['qty'] ?? 0, 0) }} {{ $item['unit'] ?? '' }} x {{ number_format($item['harga'] ?? 0, 0, ',', '.') }}</span>
                    <span>{{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>
            @endif
            @endforeach
        </div>

        <div class="border-b border-dashed border-black mb-2"></div>

        <div class="space-y-1 text-[16px]">
            <div class="flex justify-between">
                <span>SUBTOTAL</span>
                <span>{{ number_format($successTotal, 0, ',', '.') }}</span>
            </div>
            @php
            $successBonusDiscount = collect($successItems)->where('is_bonus_item', true)->sum(fn($i) => $i['regular_price'] * $i['qty']);
            $successBonusCount = collect($successItems)->where('is_bonus_item', true)->sum('qty');
            @endphp
            @if($successBonusCount > 0)
            <div class="flex justify-between text-[14px] italic">
                <span>ITEM BONUS ({{ $successBonusCount }})</span>
                <span>-{{ number_format($successBonusDiscount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between font-black text-[18px] border-t border-black pt-1">
                <span>TOTAL</span>
                <span>Rp {{ number_format($successTotal, 0, ',', '.') }}</span>
            </div>

            @if($successMethod === 'cash')
            <div class="flex justify-between">
                <span>BAYAR</span>
                <span>Rp {{ number_format($successCashReceived, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-black font-bold">
                <span>KEMBALIAN</span>
                <span>Rp {{ number_format($successCashChange, 0, ',', '.') }}</span>
            </div>
            @else
            <div class="flex justify-between font-bold">
                <span>PELANGGAN :</span>
                <span>{{ $successCustomer }}</span>
            </div>
            <div class="flex justify-between">
                <span>BAYAR (DP) :</span>
                <span>Rp {{ number_format($successPartialAmount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-bold text-black border-t border-black border-dotted pt-1 mt-1">
                <span>SISA HUTANG:</span>
                <span>Rp {{ number_format($successTotal - $successPartialAmount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <div class="border-b border-dashed border-black my-4"></div>

        <div class="text-center italic text-[14px]">
            <p>Terima Kasih</p>
            <p>Sudah Berbelanja!</p>
        </div>
    </div>
</div>