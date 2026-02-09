<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-muted/10 overflow-hidden font-sans">
    <div class="min-h-screen flex items-center justify-center p-4 relative">
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-destructive/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-md w-full glass p-8 rounded-3xl shadow-2xl text-center relative z-10">
            <div class="mx-auto w-20 h-20 rounded-3xl bg-destructive/10 flex items-center justify-center mb-6 shadow-lg">
                <x-lucide-shield-x class="w-10 h-10 text-destructive" />
            </div>
            
            <h1 class="text-5xl font-black text-foreground mb-2 italic tracking-tighter">403</h1>
            <h2 class="text-xl font-bold tracking-tight text-foreground mb-4">Akses Ditolak</h2>
            
            <p class="text-muted-foreground mb-8 text-sm font-medium leading-relaxed">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. <br>Halaman ini dikunci oleh protokol keamanan.
            </p>
            
            <div class="space-y-3">
                <a href="/dashboard" 
                   class="flex items-center justify-center gap-2 w-full px-6 py-4 bg-primary text-primary-foreground rounded-2xl font-bold hover:opacity-90 transition-all shadow-xl shadow-primary/20">
                    <x-lucide-layout-dashboard class="w-5 h-5" />
                    Kembali ke Dashboard
                </a>
            </div>
            
            <div class="mt-8 text-[10px] text-muted-foreground font-bold uppercase tracking-[0.2em] opacity-60">
                &copy; {{ date('Y') }} POS System &bull; Security Protocol
            </div>
        </div>
    </div>
</body>
</html>
