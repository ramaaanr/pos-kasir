<div class="relative overflow-hidden rounded-xl border bg-card text-card-foreground shadow-sm transition-all hover:shadow-md animate-in fade-in zoom-in duration-500 delay-500">
    <div class="p-6 flex flex-col md:flex-row items-center justify-between gap-6">

        {{-- Icon and Title --}}
        <div class="flex items-center gap-4">
            <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                <x-lucide-database class="w-8 h-8" />
            </div>
            <div class="text-left">
                <h3 class="font-semibold tracking-tight text-lg">Backup Database</h3>
                <p class="text-sm text-muted-foreground">
                    @if($this->lastBackup)
                    Last: {{ $this->lastBackup->created_at->format('d M Y H:i') }}
                    @else
                    Belum ada backup
                    @endif
                </p>
            </div>
        </div>

        {{-- Caution Note --}}
        <div class="flex-1 w-full md:w-auto">
            <div class="text-xs text-amber-600 bg-amber-50 p-3 rounded border border-amber-200 flex items-center gap-2">
                <x-lucide-alert-triangle class="w-4 h-4 shrink-0" />
                <span><span class="font-bold">Caution:</span> Lakukan backup saat jaringan sepi (malam hari) untuk kelancaran proses.</span>
            </div>
        </div>

        {{-- Action Button --}}
        <div class="w-full md:w-auto flex flex-col gap-2">
            <button
                wire:click="backup"
                wire:confirm="Yakin ingin membackup database? Pastikan koneksi stabil."
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-6 py-2 w-full md:w-auto min-w-[150px]">
                <span wire:loading.remove wire:target="backup" class="flex items-center gap-2">
                    <x-lucide-download class="w-4 h-4" /> Backup Sekarang
                </span>
                <span wire:loading wire:target="backup">Proses...</span>
            </button>

            @if (session()->has('success'))
            <div class="text-[10px] text-green-600 font-bold text-center">
                {{ session('success') }}
            </div>
            @endif
            @if (session()->has('error'))
            <div class="text-[10px] text-red-600 font-bold text-center">
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>
    <div class="absolute inset-0 border-l-4 border-blue-500/20 opacity-0 transition-opacity hover:opacity-100 pointer-events-none"></div>
</div>