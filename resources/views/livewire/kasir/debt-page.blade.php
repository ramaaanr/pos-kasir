<div class="h-[calc(100vh-80px)] -mt-6 -mx-4 sm:-mx-6 lg:-mx-8 overflow-hidden flex flex-col bg-background"
    x-data="{ 
        showPaymentModal: @entangle('showPaymentModal'),
        showSuccessModal: @entangle('showSuccessModal'),
        showErrorModal: @entangle('showErrorModal')
    }"
>
    <!-- 1. Header (POS Style) -->
    <div class="px-4 md:px-6 lg:px-8 py-4">
        <div class="rounded-2xl border border-border/50 bg-card/50 backdrop-blur-md p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button wire:click="handleBack" class="p-2.5 rounded-xl border border-border hover:bg-muted transition-all group">
                        <x-lucide-arrow-left class="h-5 w-5 text-muted-foreground group-hover:text-foreground" />
                    </button>
                    <div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary border border-primary/20">
                                <x-lucide-wallet class="h-3.5 w-3.5" />
                                HUTANG
                            </span>
                            <h2 class="text-xl font-bold tracking-tight">Pembayaran Hutang Pelanggan</h2>
                        </div>
                        <p class="text-sm text-muted-foreground mt-0.5">Kelola penagihan dan pelunasan piutang toko</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Main Body (Dual Pane) -->
    <div class="flex flex-col lg:flex-row gap-6 flex-1 px-4 md:px-6 lg:px-8 pb-6 overflow-hidden">
        
        <!-- LEFT PANEL: CUSTOMER LIST (30%) -->
        <div class="w-full lg:w-1/3 flex flex-col gap-4">
            <div class="bg-card/50 backdrop-blur-md border border-border/50 rounded-2xl p-4 flex flex-col h-full shadow-sm overflow-hidden">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-muted-foreground uppercase tracking-wider px-2 flex items-center gap-2">
                        <x-lucide-users class="h-4 w-4" />
                        Pelanggan Berhutang
                    </h3>
                </div>

                <!-- Search Input -->
                <div class="relative mb-4 px-2">
                    <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                        <x-lucide-search class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="searchCustomer"
                        placeholder="Cari nama atau no. HP..."
                        class="w-full pl-10 h-10 rounded-xl bg-background/50 border-border/50 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm"
                    >
                </div>

                <!-- Scrollable List -->
                <div class="flex-1 overflow-y-auto space-y-1.5 px-2">
                    @forelse($customers as $customer)
                        <button 
                            wire:click="selectCustomer({{ $customer->id }})"
                            class="w-full text-left p-3 rounded-xl border transition-all flex justify-between items-center group {{ $selectedCustomerId == $customer->id ? 'bg-primary/10 border-primary ring-2 ring-primary/20' : 'bg-transparent border-transparent hover:bg-muted/50 hover:border-border' }}"
                        >
                            <div class="flex flex-col text-ellipsis overflow-hidden">
                                <span class="font-bold text-sm truncate {{ $selectedCustomerId == $customer->id ? 'text-primary' : 'text-foreground' }} group-hover:text-primary transition-colors">{{ $customer->nama }}</span>
                                <span class="text-[10px] text-muted-foreground">{{ $customer->no_hp }}</span>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="text-xs font-black text-red-500">Rp {{ number_format($customer->total_debt, 0, ',', '.') }}</div>
                            </div>
                        </button>
                    @empty
                        <div class="flex flex-col items-center justify-center h-40 opacity-40 grayscale">
                            <x-lucide-user-x class="h-10 w-10 mb-2" />
                            <p class="text-xs font-medium">Tidak ada data</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: DEBT DETAILS (70%) -->
        <div class="flex-1 overflow-hidden">
            @if(!$selectedCustomerId)
                <!-- Empty State -->
                <div class="h-full bg-card/30 backdrop-blur-sm border border-dashed border-border/60 rounded-3xl flex flex-col items-center justify-center text-center p-12">
                    <div class="p-6 bg-muted/20 rounded-full mb-6 text-muted-foreground/30 ring-8 ring-muted/5">
                        <x-lucide-wallet class="h-16 w-16" />
                    </div>
                    <h3 class="text-xl font-bold text-muted-foreground">Pilih Pelanggan</h3>
                    <p class="text-sm text-muted-foreground/60 max-w-sm mt-2">Pilih salah satu pelanggan di sebelah kiri untuk melihat rincian hutang dan melakukan pembayaran.</p>
                </div>
            @else
                <!-- Active Content -->
                <div class="flex flex-col h-full gap-4">
                    <!-- Header Info -->
                    <div class="bg-card/50 backdrop-blur-md border border-border/50 rounded-2xl p-6 shadow-sm flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-primary/10 text-primary flex items-center justify-center font-black text-lg">
                                {{ substr($selectedCustomer->nama, 0, 1) }}
                            </div>
                            <div>
                                <h2 class="text-xl font-black tracking-tight">{{ $selectedCustomer->nama }}</h2>
                                <p class="text-xs text-muted-foreground flex items-center gap-1.5">
                                    <x-lucide-phone class="h-3 w-3" />
                                    {{ $selectedCustomer->no_hp }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest block mb-1">Total Hutang Aktif</span>
                            <span class="text-2xl font-black text-red-500 tracking-tighter">Rp {{ number_format($selectedCustomer->total_debt, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Scrollable Debt List -->
                    <div class="flex-1 overflow-y-auto space-y-4 pr-1">
                        @foreach($activeDebts as $debt)
                            <div class="bg-card border border-border/60 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative z-10">
                                    <div class="flex items-start gap-4">
                                        <div class="p-3 bg-amber-500/10 text-amber-600 rounded-xl">
                                            <x-lucide-receipt class="h-6 w-6" />
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-mono text-xs font-bold bg-muted px-2 py-0.5 rounded">{{ $debt->sale->invoice_number }}</span>
                                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full {{ $debt->status === 'PARTIAL' ? 'bg-blue-100 text-blue-600' : 'bg-red-100 text-red-600' }}">
                                                    {{ $debt->status }}
                                                </span>
                                            </div>
                                            <p class="text-[10px] text-muted-foreground font-medium uppercase tracking-wider mb-2">Tercatat: {{ $debt->created_at->format('d M Y, H:i') }}</p>
                                            <p class="text-xs italic text-muted-foreground/80"><span class="font-bold text-muted-foreground not-italic">Jaminan:</span> {{ $debt->jaminan }}</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col md:items-end w-full md:w-auto mt-2 md:mt-0">
                                        <div class="flex md:flex-col justify-between items-baseline gap-2 mb-2">
                                            <span class="text-[10px] font-bold text-muted-foreground uppercase opacity-60">Sisa Hutang:</span>
                                            <span class="text-xl font-black text-primary tracking-tighter">Rp {{ number_format($debt->remaining_balance, 0, ',', '.') }}</span>
                                        </div>
                                        <button 
                                            wire:click="openPaymentModal({{ $debt->id }})"
                                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-sm hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20"
                                        >
                                            <x-lucide-check-circle class="h-4 w-4" />
                                            Bayar Sekarang
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Progress Bar -->
                                @if($debt->status === 'PARTIAL')
                                    <div class="mt-4 pt-4 border-t border-border/40 flex flex-col gap-1.5">
                                        @php $progress = ($debt->total_paid / $debt->amount) * 100; @endphp
                                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest mb-1">
                                            <span class="text-emerald-600">Terbayar: Rp {{ number_format($debt->total_paid, 0, ',', '.') }}</span>
                                            <span class="text-muted-foreground">{{ number_format($progress, 0) }}%</span>
                                        </div>
                                        <div class="h-1.5 w-full bg-muted rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div 
        x-show="showPaymentModal"
        x-cloak
        class="fixed inset-0 z-[100] overflow-y-auto"
        @keydown.escape.window="showPaymentModal = false"
    >
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <div x-show="showPaymentModal" x-transition.opacity class="fixed inset-0 bg-background/80 backdrop-blur-sm" @click="showPaymentModal = false"></div>
            
            <div 
                x-show="showPaymentModal" 
                x-transition.scale.origin.center
                class="relative inline-block align-bottom bg-card rounded-3xl shadow-2xl border border-border text-left overflow-hidden transform transition-all sm:my-8 sm:align-middle w-full max-w-lg z-10"
            >
                <div class="px-8 py-6 border-b border-border bg-muted/30 flex items-center justify-between">
                    <h3 class="text-xl font-extrabold flex items-center gap-3">
                        <x-lucide-credit-card class="h-6 w-6 text-primary" />
                        Pembayaran Hutang
                    </h3>
                    <button @click="showPaymentModal = false" class="p-2 hover:bg-muted rounded-full transition-colors"><x-lucide-x class="h-5 w-5" /></button>
                </div>

                <div class="px-8 py-8 space-y-6">
                    @if($selectedDebt)
                        <div class="p-4 bg-muted/50 rounded-2xl border border-border/50 flex flex-col gap-1">
                            <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Informasi Nota</span>
                            <div class="flex justify-between items-center">
                                <span class="font-mono text-sm font-bold">{{ $selectedDebt->sale->invoice_number }}</span>
                                <span class="text-sm font-black text-primary">Sisa: Rp {{ number_format($selectedDebt->remaining_balance, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-2">
                        <label class="text-sm font-bold ml-1 flex justify-between">
                            <span>Nominal Bayar</span>
                            <span class="text-[10px] text-primary">Maks: Rp {{ number_format($selectedDebt?->remaining_balance ?? 0, 0, ',', '.') }}</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-muted-foreground">Rp</span>
                            <input 
                                type="number" 
                                wire:model.live="paymentAmount"
                                step="1"
                                min="1"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                class="w-full h-14 pl-12 pr-4 rounded-2xl bg-muted/30 border-border px-4 font-black text-xl focus:ring-primary focus:border-primary"
                                placeholder="0"
                            >
                        </div>
                        <p class="text-[10px] text-muted-foreground ml-1">Pembayaran harus berupa angka bulat positif (Hanya Tunai).</p>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold ml-1">Catatan (Opsional)</label>
                        <textarea 
                            wire:model.live="paymentNote"
                            class="w-full rounded-2xl bg-muted/30 border-border px-4 py-3 font-medium focus:ring-primary focus:border-primary" 
                            rows="2" 
                            placeholder="Catatan pembayaran..."
                        ></textarea>
                    </div>
                </div>

                <div class="px-8 py-6 border-t border-border bg-muted/30 flex items-center gap-3">
                    <button @click="showPaymentModal = false" class="flex-1 py-3 rounded-xl border border-border font-bold hover:bg-muted transition-all text-sm">Batal</button>
                    <button 
                        wire:click="processPayment"
                        wire:loading.attr="disabled"
                        class="flex-[2] py-3 rounded-xl bg-emerald-600 text-white font-black hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="processPayment">Konfirmasi Bayar Tunai</span>
                        <span wire:loading wire:target="processPayment" class="flex items-center justify-center gap-2">
                            <x-lucide-loader-2 class="animate-spin h-4 w-4" />
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- SUCCESS MODAL -->
    <div 
        x-show="showSuccessModal"
        x-cloak
        class="fixed inset-0 z-[110] overflow-y-auto"
        @keydown.escape.window="showSuccessModal = false"
    >
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showSuccessModal" x-transition.opacity class="fixed inset-0 bg-background/90 backdrop-blur-xl" @click="showSuccessModal = false"></div>
            
            <div 
                x-show="showSuccessModal" 
                x-transition.scale.origin.center
                class="relative bg-card rounded-[40px] shadow-[0_32px_64px_-12px_rgba(0,0,0,0.3)] border-4 border-emerald-500/20 p-10 w-full max-w-md z-10 text-center"
            >
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 mb-8 animate-bounce">
                    <x-lucide-check class="h-12 w-12" />
                </div>
                
                <h3 class="text-3xl font-black text-foreground mb-2">Bayar Berhasil!</h3>
                <p class="text-muted-foreground mb-8 text-sm px-4">Pembayaran hutang telah dicatat dan saldo pelanggan telah diperbarui.</p>

                <div class="bg-muted/30 rounded-3xl p-6 mb-8 text-left space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-muted-foreground">Invoice</span>
                        <span class="font-mono font-bold">{{ $successData['invoice'] ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-muted-foreground">Nominal Bayar</span>
                        <span class="font-black text-emerald-600">Rp {{ number_format($successData['amount_paid'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs border-t border-border/50 pt-2">
                        <span class="text-muted-foreground">Sisa Hutang Notifikasi</span>
                        <span class="font-bold text-red-500">Rp {{ number_format($successData['remaining'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                
                <button 
                    @click="showSuccessModal = false"
                    class="w-full py-5 rounded-2xl bg-primary text-white font-black text-lg hover:bg-primary/90 transition-all shadow-2xl shadow-primary/20"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ERROR MODAL -->
    <div 
        x-show="showErrorModal"
        x-cloak
        class="fixed inset-0 z-[120] overflow-y-auto"
        @keydown.escape.window="showErrorModal = false"
    >
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="showErrorModal" x-transition.opacity class="fixed inset-0 bg-background/80 backdrop-blur-md" @click="showErrorModal = false"></div>
            
            <div 
                x-show="showErrorModal" 
                x-transition.scale.origin.center
                class="relative bg-card rounded-[40px] shadow-[0_32px_64px_-12px_rgba(0,0,0,0.3)] border-4 border-destructive/20 p-10 w-full max-w-md z-10 text-center"
            >
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-destructive/10 text-destructive mb-8">
                    <x-lucide-x-circle class="h-12 w-12" />
                </div>
                
                <h3 class="text-2xl font-black text-foreground mb-2">Gagal Bayar</h3>
                <p class="text-muted-foreground mb-6 text-sm">Kesalahan terjadi saat memproses pembayaran.</p>

                <div class="bg-destructive/5 border-2 border-destructive/10 rounded-2xl p-6 mb-8 text-center text-destructive font-bold">
                    {{ $errorMessage }}
                </div>
                
                <button 
                    @click="showErrorModal = false"
                    class="w-full py-4 rounded-xl bg-muted font-bold text-foreground hover:bg-muted/80 transition-all"
                >
                    Tutup & Periksa Kembali
                </button>
            </div>
        </div>
    </div>
</div>
