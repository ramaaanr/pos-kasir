<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-fade-in">
    {{-- Left Column: Customer List & Search --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-card border border-border/50 rounded-3xl p-6 shadow-sm flex flex-col h-full">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2.5 rounded-xl bg-primary/10 text-primary">
                    <x-lucide-users class="w-5 h-5" />
                </div>
                <h3 class="font-black text-foreground tracking-tight">Pelanggan Berhutang</h3>
            </div>

            <div class="relative mb-6">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-muted-foreground">
                    <x-lucide-search class="h-4 w-4" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchCustomer"
                    placeholder="Nama atau No. HP..."
                    class="flex h-10 w-full rounded-xl border border-input bg-background/50 pl-10 pr-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
            </div>

            <div class="space-y-3 overflow-y-auto max-h-[600px] pr-2 custom-scrollbar">
                @forelse($customers as $customer)
                    <button
                        wire:click="selectCustomer({{ $customer->id }})"
                        class="w-full text-left p-4 rounded-2xl border transition-all duration-200 group {{ $selectedCustomerId == $customer->id ? 'bg-primary border-primary shadow-lg shadow-primary/20 text-primary-foreground' : 'bg-card border-border/50 hover:border-primary/50 text-foreground' }}">
                        <div class="flex flex-col">
                            <span class="font-black tracking-tight">{{ $customer->nama }}</span>
                            <span class="text-[10px] uppercase font-black tracking-widest opacity-60 {{ $selectedCustomerId == $customer->id ? 'text-primary-foreground' : 'text-muted-foreground' }}">
                                {{ $customer->no_hp ?: 'Tanpa No. HP' }}
                            </span>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-xs font-black">Rp {{ number_format($customer->total_debt, 0, ',', '.') }}</span>
                                <x-lucide-chevron-right class="w-4 h-4 transform transition-transform group-hover:translate-x-1" />
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="text-center py-10 text-sm text-muted-foreground italic">
                        Tidak ada pelanggan berhutang.
                    </div>
                @endforelse
            </div>

            <div class="mt-6 pt-6 border-t border-border/50">
                {{ $customers->links() }}
            </div>
        </div>
    </div>

    {{-- Right Column: Debt Details --}}
    <div class="lg:col-span-2 space-y-6">
        @if($selectedCustomer)
            {{-- Customer Information Header --}}
            <div class="bg-card border border-border/50 rounded-3xl p-8 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                            <x-lucide-user class="w-8 h-8" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-foreground">{{ $selectedCustomer->nama }}</h2>
                            <p class="text-xs font-black uppercase tracking-[0.2em] text-muted-foreground mt-1">{{ $selectedCustomer->no_hp ?: 'Pelanggan Walk-in' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-widest text-muted-foreground">Total Hutang Aktif</span>
                        <p class="text-3xl font-black text-rose-600 mt-1">Rp {{ number_format($selectedCustomer->total_debt, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            {{-- Invoices & History --}}
            <div class="grid grid-cols-1 gap-6">
                {{-- Debt List with Progress --}}
                <div class="bg-card border border-border/50 rounded-3xl overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-border/50 flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-600">
                            <x-lucide-receipt class="w-5 h-5" />
                        </div>
                        <h3 class="font-black text-foreground tracking-tight">Daftar Nota Berhutang</h3>
                    </div>
                    <div class="divide-y divide-border">
                        @foreach($activeDebts as $debt)
                            @php
                                $totalPaid = $debt->payments->sum('amount');
                                $progress = $debt->amount > 0 ? min(100, ($totalPaid / $debt->amount) * 100) : 0;
                            @endphp
                            <div class="p-6 hover:bg-muted/5 transition-colors">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono font-bold text-primary">{{ $debt->sale->invoice_number }}</span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $debt->status === 'OPEN' ? 'bg-rose-500/10 text-rose-600' : 'bg-orange-500/10 text-orange-600' }}">
                                                {{ $debt->status }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-muted-foreground font-medium">{{ $debt->created_at->translatedFormat('d F Y, H:i') }}</p>
                                    </div>
                                    <div class="text-right space-y-1">
                                        <div class="flex items-center justify-end gap-3">
                                            <div class="text-right leading-none">
                                                <span class="text-[10px] text-muted-foreground uppercase font-black block">Terbayar</span>
                                                <span class="text-sm font-black text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="h-8 w-px bg-border mx-2"></div>
                                            <div class="text-right leading-none">
                                                <span class="text-[10px] text-muted-foreground uppercase font-black block">Total Nota</span>
                                                <span class="text-sm font-black text-foreground">Rp {{ number_format($debt->amount, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="mt-4">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[10px] font-black uppercase text-muted-foreground tracking-widest">Progress Pelunasan</span>
                                        <span class="text-[10px] font-black {{ $progress >= 100 ? 'text-emerald-600' : 'text-primary' }}">{{ number_format($progress, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-muted rounded-full h-2 relative overflow-hidden">
                                        <div
                                            class="h-full rounded-full transition-all duration-700 ease-out {{ $progress >= 100 ? 'bg-emerald-500' : 'bg-primary' }}"
                                            style="width: {{ $progress }}%">
                                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mt-1.5">
                                        <span class="text-[9px] font-medium text-muted-foreground italic">Sisa Hutang : Rp {{ number_format($debt->amount - $totalPaid, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Payment History --}}
                <div class="bg-card border border-border/50 rounded-3xl overflow-hidden shadow-sm">
                    <div class="p-6 border-b border-border/50 flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600">
                            <x-lucide-history class="w-5 h-5" />
                        </div>
                        <h3 class="font-black text-foreground tracking-tight">History Pembayaran</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead>
                                <tr class="bg-muted/30 border-b border-border">
                                    <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Invoice</th>
                                    <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Waktu</th>
                                    <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px]">Metode</th>
                                    <th class="px-6 py-4 font-black text-muted-foreground uppercase tracking-widest text-[9px] text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                @forelse($selectedCustomer->debt_payments()->with('debt.sale')->latest()->get() as $payment)
                                    <tr class="hover:bg-muted/5 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="font-mono font-bold text-primary">{{ $payment->debt->sale->invoice_number }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-muted-foreground font-medium">
                                            {{ $payment->paid_at->translatedFormat('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase">
                                                {{ $payment->payment_method }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="font-black text-emerald-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-muted-foreground italic">
                                            Belum ada history pembayaran.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-card border border-border/50 rounded-3xl p-12 shadow-sm flex flex-col items-center justify-center text-center">
                <div class="w-24 h-24 rounded-full bg-muted/50 flex items-center justify-center text-muted-foreground mb-6">
                    <x-lucide-mouse-pointer-2 class="w-12 h-12" />
                </div>
                <h3 class="text-xl font-black text-foreground tracking-tight">Pilih Pelanggan</h3>
                <p class="text-sm text-muted-foreground mt-2 max-w-xs">Silakan pilih pelanggan di sebelah kiri untuk melihat rincian piutang dan history pembayaran mereka.</p>
            </div>
        @endif
    </div>
</div>
