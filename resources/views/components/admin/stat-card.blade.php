@props(['label', 'value', 'icon' => 'bar-chart-3', 'color' => 'role-admin'])

@php
    $bgClass = match($color) {
        'role-admin' => 'bg-role-admin/10 text-role-admin border-role-admin/20',
        'role-kasir' => 'bg-role-kasir/10 text-role-kasir border-role-kasir/20',
        'role-owner' => 'bg-role-owner/10 text-role-owner border-role-owner/20',
        default => 'bg-muted text-muted-foreground border-border',
    };
    
    $iconComponent = "lucide-$icon";
@endphp

<div class="flex items-center gap-4 bg-card p-5 rounded-2xl border border-border/50 shadow-sm animate-fade-in opacity-0 delay-300 transition-all duration-300 hover:shadow-md hover:border-border">
    <div class="w-12 h-12 rounded-xl flex items-center justify-center border {{ $bgClass }}">
        <x-dynamic-component :component="$iconComponent" class="w-6 h-6" />
    </div>
    <div class="flex flex-col">
        <span class="text-xs font-bold text-muted-foreground uppercase tracking-wider">{{ $label }}</span>
        <span class="text-2xl font-black text-foreground">{{ $value }}</span>
    </div>
</div>
