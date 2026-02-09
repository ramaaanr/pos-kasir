<x-layouts.admin>
    <div class="mb-10">
        <h1 class="text-4xl font-black text-foreground tracking-tight">Admin Dashboard</h1>
        <p class="text-muted-foreground mt-1 text-sm font-medium">Sistem siap digunakan. Berikut adalah ringkasan hari ini.</p>
    </div>

    {{-- Part 1: Statistik --}}
    <livewire:admin.admin-dashboard part="stats" />

    {{-- Part 2: Fitur2 / Shortcut Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10 mt-10">
        <x-admin.card
            title="Master Produk"
            icon="package"
            color="cyan"
            href="{{ route('products.index') }}"
            delay="delay-100" />
        <x-admin.card
            title="Master Kategori"
            icon="layout-grid"
            color="pink"
            href="{{ route('product-categories.index') }}"
            delay="delay-200" />
        <x-admin.card
            title="Stok Masuk"
            icon="truck"
            color="emerald"
            href="{{ route('stok-masuk.index') }}"
            delay="delay-300" />
        <x-admin.card
            title="Laporan"
            icon="bar-chart-3"
            color="orange"
            href="{{ route('admin.reports.index') }}"
            delay="delay-400" />
        <x-admin.card
            title="Data Penjualan"
            icon="receipt"
            color="indigo"
            href="{{ route('admin.sales.index') }}"
            delay="delay-500" />
        <x-admin.card
            title="Stock Adjustment"
            icon="clipboard-edit"
            color="rose"
            subtitle="Koreksi stok rusak/hilang"
            href="{{ route('admin.stock-adjustments.index') }}"
            delay="delay-500" />
    </div>

    {{-- Part 3: Penjualan dan Hutang --}}
    <livewire:admin.admin-dashboard part="activity" />

</x-layouts.admin>