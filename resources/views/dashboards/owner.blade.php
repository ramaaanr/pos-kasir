<x-layouts.admin title="Dashboard Owner" subtitle="Analisa Bisnis & Performa">
    
    {{-- Welcome Message --}}
    <div class="mb-8 animate-fade-in opacity-0">
        <h2 class="text-2xl font-black text-foreground flex items-center gap-3">
            <span class="w-2 h-8 bg-role-owner rounded-full"></span>
            Selamat Datang, Owner! 📈
        </h2>
        <p class="text-muted-foreground mt-1 text-sm font-medium">Berikut adalah rangkuman performa seluruh outlet.</p>
    </div>

    {{-- Owner Feature Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
        <x-admin.card 
            title="Laporan Penjualan" 
            icon="bar-chart-3" 
            color="role-owner" 
            delay="delay-100" 
        />
        <x-admin.card 
            title="Analisa Produk" 
            icon="pie-chart" 
            color="cyan" 
            delay="delay-200" 
        />
        <x-admin.card 
            title="Audit Log" 
            icon="shield-check" 
            color="muted" 
            disabled="true" 
            delay="delay-300" 
        />
    </div>

    {{-- Stats for Owner --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <x-admin.stat-card 
            label="Total Omzet Bulanan" 
            value="Rp 450M" 
            icon="trending-up" 
            color="role-owner" 
        />
        <x-admin.stat-card 
            label="Pertumbuhan" 
            value="+12.5%" 
            icon="arrow-up-right" 
            color="role-owner" 
        />
        <x-admin.stat-card 
            label="Outlet Aktif" 
            value="5" 
            icon="building-2" 
            color="role-owner" 
        />
    </div>

</x-layouts.admin>
