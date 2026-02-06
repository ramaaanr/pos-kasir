<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    </style>
</head>
<body class="bg-[#f7f8fa] overflow-hidden">
    <div class="min-h-screen flex items-center justify-center p-4 relative">
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-[#315efb]/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-[#ff3b30]/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-md w-full glass p-8 rounded-3xl shadow-2xl text-center relative z-10 border border-white/50">
            <div class="mx-auto w-20 h-20 rounded-3xl bg-[#ff3b30]/10 flex items-center justify-center mb-6 shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ff3b30" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m2 \16 19.3 2.15-5.32c.11-.27.42-.35.63-.14C7.03 12.87 9.4 17 11.5 17c1.7 0 3.2-1.2 3.8-2.67.24-.58.11-1.25-.33-1.69a6 6 0 0 0-8.94 0c-.44.44-.57 1.11-.33 1.69C6.3 15.8 7.8 17 9.5 17c2.1 0 4.47-4.13 6.72-6.46a.5.5 0 0 1 .63-.14L19 12.7" />
                    <path d="m2 8 3 3" />
                    <path d="m22 16-3-3" />
                </svg>
            </div>
            
            <h1 class="text-5xl font-black text-[#1a1c21] mb-2 italic tracking-tighter">403</h1>
            <h2 class="text-xl font-bold tracking-tight text-[#1a1c21] mb-4">Akses Ditolak</h2>
            
            <p class="text-muted-foreground text-gray-500 mb-8 text-sm font-medium leading-relaxed">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. <br>Halaman ini dikunci oleh protokol keamanan.
            </p>
            
            <div class="space-y-3">
                <a href="/dashboard" 
                   class="flex items-center justify-center gap-2 w-full px-6 py-4 bg-[#315efb] text-white rounded-2xl font-bold hover:opacity-90 transition-all shadow-xl shadow-[#315efb]/20">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    Kembali ke Dashboard
                </a>
            </div>
            
            <div class="mt-8 text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] opacity-60">
                &copy; {{ date('Y') }} POS System &bull; Security Protocol
            </div>
        </div>
    </div>
</body>
</html>
