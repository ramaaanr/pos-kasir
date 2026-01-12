<x-layouts.admin title="Dashboard Kasir" subtitle="Sistem Penjualan & Transaksi">
    
    {{-- Welcome Message --}}
    <div class="mb-8 animate-fade-in opacity-0">
        <h2 class="text-2xl font-black text-foreground flex items-center gap-3">
            <span class="w-2 h-8 bg-role-kasir rounded-full"></span>
            Selamat Bertugas, Kasir! ☕
        </h2>
        <p class="text-muted-foreground mt-1 text-sm font-medium">Buka menu kasir untuk memulai transaksi baru.</p>
    </div>

    {{-- Kasir Feature Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
        <x-admin.card 
            title="Menu Kasir" 
            icon="shopping-cart" 
            color="cyan" 
            delay="delay-100" 
        />
        <x-admin.card 
            title="Riwayat Transaksi" 
            icon="history" 
            color="emerald" 
            delay="delay-200" 
        />
        <x-admin.card 
            title="Tutup Kasir" 
            icon="power" 
            color="amber" 
            disabled="true" 
            delay="delay-300" 
        />
    </div>

    {{-- Stats for Kasir --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <x-admin.stat-card 
            label="Total Transaksi Hari Ini" 
            value="42" 
            icon="shopping-bag" 
            color="role-kasir" 
        />
        <x-admin.stat-card 
            label="Omzet Penjualan" 
            value="Rp 1.2M" 
            icon="bar-chart-3" 
            color="role-kasir" 
        />
        <x-admin.stat-card 
            label="Status Shift" 
            value="Aktif" 
            icon="clock" 
            color="role-kasir" 
        />
    </div>

</x-layouts.admin>
