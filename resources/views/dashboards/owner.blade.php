<x-layouts.admin title="Dashboard Owner" subtitle="Analisa Bisnis & Performa">
    
    {{-- Welcome Message --}}
    <div class="mb-8 animate-fade-in">
        <h2 class="text-2xl font-black text-foreground flex items-center gap-3">
            <span class="w-2 h-8 bg-primary rounded-full"></span>
            Selamat Datang, Owner! 🚀
        </h2>
        <p class="text-muted-foreground mt-1 text-sm font-medium">Pantau performa bisnis dan kelola akses tim Anda.</p>
    </div>

    {{-- Real Stats (Reusing Admin Stats) --}}
    <livewire:admin.admin-dashboard part="stats" />

    {{-- Feature Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10 mt-10">
        <x-admin.card
            title="Manajemen User"
            subtitle="Atur hak akses admin & kasir"
            icon="users"
            color="indigo"
            href="{{ route('admin.users.index') }}"
            delay="delay-100" />
        <x-admin.card
            title="Laporan Penjualan"
            icon="bar-chart-3"
            color="orange"
            href="{{ route('admin.reports.index') }}"
            delay="delay-200" />
        <x-admin.card
            title="Data Penjualan"
            icon="receipt"
            color="emerald"
            href="{{ route('admin.sales.index') }}"
            delay="delay-300" />
    </div>

    {{-- Recent Activity --}}
    <livewire:admin.admin-dashboard part="activity" />

</x-layouts.admin>
