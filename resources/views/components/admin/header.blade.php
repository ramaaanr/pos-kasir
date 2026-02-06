@props(['title', 'subtitle' => null])

<header class="sticky top-0 z-40 w-full glass border-b border-border/40 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-4">
            
            {{-- Left Side: Logo + Title --}}
            <div class="flex items-center gap-4">
                @if(!request()->routeIs('dashboard'))
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md p-2 text-muted-foreground hover:bg-muted transition-colors" title="Back">
                    <x-lucide-arrow-left class="w-5 h-5" />
                </a>
                @endif
                
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gradient-admin flex items-center justify-center shadow-lg shadow-primary/20">
                        <x-lucide-shield class="w-6 h-6 text-white" />
                    </div>
                    <div class="flex flex-col">
                        <h1 class="text-sm font-bold tracking-tight text-foreground sm:text-base">{{ $title }}</h1>
                        @if($subtitle)
                        <p class="text-xs text-muted-foreground font-medium">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Center: Navigation Menu --}}
            <nav class="hidden md:flex items-center gap-1">
                <a href="{{ route('products.index') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('products.*') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                    <x-lucide-package class="w-4 h-4 inline-block mr-1" />
                    Produk
                </a>
                <a href="{{ route('product-categories.index') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('product-categories.*') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                    <x-lucide-layout-grid class="w-4 h-4 inline-block mr-1" />
                    Kategori
                </a>
                <a href="{{ route('stok-masuk.index') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('stok-masuk.*') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                    <x-lucide-truck class="w-4 h-4 inline-block mr-1" />
                    Stok Masuk
                </a>
                <a href="{{ route('admin.stock-adjustments.index') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.stock-adjustments.*') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                    <x-lucide-clipboard-edit class="w-4 h-4 inline-block mr-1" />
                    Stock Adjustment
                </a>
            </nav>

            {{-- Right Side: User Info --}}
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-sm font-semibold text-foreground">{{ Auth::user()->name }}</span>
                    <span class="text-[10px] uppercase tracking-wider font-bold text-primary px-1.5 py-0.5 rounded-full bg-primary/10 border border-primary/20">
                        {{ strtoupper(Auth::user()->roles->first()->name ?? 'User') }}
                    </span>
                </div>
                
                <div class="h-8 w-px bg-border/50 mx-2 hidden sm:block"></div>
                
                <form action="{{ route('filament.admin.auth.logout') }}" method="POST" class="inline-block">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-destructive hover:bg-destructive/5 rounded-lg border border-transparent hover:border-destructive/20 transition-all duration-200">
                        <x-lucide-log-out class="w-4 h-4" />
                        <span class="hidden md:inline">Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
