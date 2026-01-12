@props([
    'title',
    'icon' => 'package',
    'color' => 'primary',
    'disabled' => false,
    'href' => '#',
    'delay' => 'delay-0'
])

@php
    $colorClasses = match($color) {
        'pink' => 'bg-pink-500/10 text-pink-600 border-pink-500/20 group-hover:bg-pink-500 group-hover:text-white',
        'cyan' => 'bg-cyan-500/10 text-cyan-600 border-cyan-500/20 group-hover:bg-cyan-500 group-hover:text-white',
        'emerald' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20 group-hover:bg-emerald-500 group-hover:text-white',
        'amber' => 'bg-amber-500/10 text-amber-600 border-amber-500/20 group-hover:bg-amber-500 group-hover:text-white',
        'muted' => 'bg-muted text-muted-foreground border-border group-hover:bg-muted-foreground group-hover:text-white',
        default => 'bg-primary/10 text-primary border-primary/20 group-hover:bg-primary group-hover:text-white',
    };

    $iconComponent = "lucide-$icon";
@endphp

<a href="{{ $disabled ? 'javascript:void(0)' : $href }}" 
   class="group relative flex flex-col items-center justify-center p-6 bg-card border border-border/50 rounded-2xl shadow-sm transition-all duration-300 animate-fade-in opacity-0 {{ $delay }} 
   {{ $disabled ? 'opacity-60 cursor-not-allowed filter grayscale' : 'hover:shadow-xl hover:-translate-y-1 hover:border-primary/30' }}">
    
    @if($disabled)
        <span class="absolute top-3 right-3 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-muted text-muted-foreground rounded-full border border-border">Segera</span>
    @endif

    <div class="w-14 h-14 rounded-xl flex items-center justify-center border transition-all duration-300 {{ $colorClasses }} mb-4 {{ $disabled ? '' : 'group-hover:scale-110 group-hover:rotate-3' }}">
        <x-dynamic-component :component="$iconComponent" class="w-8 h-8" />
    </div>

    <h3 class="text-sm font-bold text-foreground group-hover:text-primary transition-colors text-center">{{ $title }}</h3>
    
    @if(!$disabled)
        <div class="mt-3 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 text-[10px] font-bold uppercase tracking-tighter text-primary">
            Buka Dashboard <x-lucide-arrow-right class="w-3 h-3" />
        </div>
    @endif
</a>
