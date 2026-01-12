<x-layouts.admin title="Dashboard Utama" subtitle="Ringkasan performa dan akses cepat fitur">
    
    {{-- Welcome Message --}}
    <div class="mb-8 animate-fade-in opacity-0">
        <h2 class="text-2xl font-black text-foreground flex items-center gap-3">
            <span class="w-2 h-8 bg-primary rounded-full"></span>
            Selamat Datang, Admin! 👋
        </h2>
        <p class="text-muted-foreground mt-1 text-sm font-medium">Sistem siap digunakan. Berikut adalah ringkasan hari ini.</p>
    </div>

    {{-- Feature Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <x-admin.card 
            title="Master Produk" 
            icon="package" 
            color="cyan" 
            delay="delay-100" 
        />
        <x-admin.card 
            title="Master Kategori" 
            icon="layout-grid" 
            color="pink" 
            delay="delay-200" 
        />
        <x-admin.card 
            title="Stok Masuk" 
            icon="truck" 
            color="emerald" 
            delay="delay-300" 
        />
        <x-admin.card 
            title="Laporan" 
            icon="bar-chart-3" 
            color="amber" 
            disabled="true" 
            delay="delay-400" 
        />
    </div>

    {{-- Quick Stats Section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <x-admin.stat-card 
            label="Total Pengguna" 
            value="24" 
            icon="users" 
            color="role-admin" 
        />
        <x-admin.stat-card 
            label="Produk Aktif" 
            value="156" 
            icon="package" 
            color="role-kasir" 
        />
        <x-admin.stat-card 
            label="Uptime Sistem" 
            value="89%" 
            icon="power" 
            color="role-owner" 
        />
    </div>

</x-layouts.admin>
