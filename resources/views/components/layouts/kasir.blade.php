<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Kasir' }} - POS System</title>
    @vite(['resources/css/app.css'])
    <style>
        .gradient-kasir { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    </style>
</head>
<body class="bg-muted/10 font-sans antialiased min-h-screen flex flex-col">
    {{-- Sticky Header --}}
    <header class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold">
                    <x-lucide-monitor class="h-5 w-5" />
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-none tracking-tight">Layar Kasir</h1>
                    <p class="text-[10px] text-muted-foreground font-medium uppercase tracking-wider">{{ $title ?? 'POS System' }}</p>
                </div>
            </div>

            {{-- Center: Navigation Menu --}}
            <nav class="flex items-center gap-1">
                <a href="{{ route('kasir.pos') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('kasir.pos') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                    <x-lucide-shopping-cart class="w-4 h-4 inline-block mr-1" />
                    Transaksi
                </a>
                <a href="{{ route('kasir.debt') }}" 
                   class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('kasir.debt') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
                    <x-lucide-wallet class="w-4 h-4 inline-block mr-1" />
                    Pembayaran Hutang
                </a>
            </nav>

            <div class="flex items-center gap-4">
                <div class="flex flex-col items-end mr-2">
                    <span class="text-sm font-semibold">{{ auth()->user()->name ?? 'Kasir' }}</span>
                    <span class="text-[10px] text-muted-foreground bg-muted px-2 py-0.5 rounded-full capitalize">Kasir</span>
                </div>
                <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                    @csrf
                    <button type="submit" class="p-2 hover:bg-destructive/10 hover:text-destructive rounded-lg transition-colors" title="Logout">
                        <x-lucide-log-out class="h-5 w-5" />
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

    {{-- Modals --}}
    @stack('modals')
    
    {{-- Toast --}}
    @livewire('notifications')
</body>
</html>
