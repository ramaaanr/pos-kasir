<div class="min-h-screen bg-[#f7f8fa] text-[#1a1c21]">
    <x-filament-panels::layout.base :livewire="$this">
        <div class="flex flex-col md:flex-row min-h-screen">
            <!-- Left Side (Visual/Brand) -->
            <div class="hidden md:flex md:w-1/2 bg-[#315efb] justify-center items-center p-12 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[radial-gradient(circle_at_50%_50%,_rgba(255,255,255,0.8),transparent_50%)]"></div>
                
                <div class="relative z-10 text-white text-center">
                    <div class="inline-block p-3 bg-white/20 backdrop-blur-md rounded-2xl mb-6">
                        <x-heroicon-s-shopping-cart class="w-12 h-12 text-white" />
                    </div>
                    <h1 class="text-5xl font-extrabold mb-4 tracking-tight">KASIR PRO</h1>
                    <p class="text-xl opacity-90 font-light">Efficient & Fast POS Solution</p>
                    
                    <div class="mt-12 flex gap-4 justify-center">
                        <span class="w-3 h-3 rounded-full bg-accent animate-pulse"></span>
                        <span class="w-3 h-3 rounded-full bg-accent opacity-50"></span>
                        <span class="w-3 h-3 rounded-full bg-accent opacity-25"></span>
                    </div>
                </div>

                <!-- Accent Overlay -->
                <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-accent opacity-20 rounded-full blur-3xl"></div>
            </div>

            <!-- Right Side (Login Form) -->
            <div class="w-full md:w-1/2 flex items-center justify-center p-8 bg-[#f7f8fa] relative">
                <div class="w-full max-w-md space-y-8">
                    <div class="text-center md:text-left">
                        <div class="flex items-center gap-2 mb-2 justify-center md:justify-start">
                            <div class="w-8 h-1 bg-primary rounded-full"></div>
                            <span class="text-xs font-bold uppercase tracking-widest text-primary">Secure Access</span>
                        </div>
                        <h2 class="text-4xl font-black tracking-tight text-foreground">
                            Welcome Back
                        </h2>
                        <p class="mt-3 text-muted-foreground">
                            Please sign in to your dashboard
                        </p>
                    </div>

                    <div class="mt-8 bg-card shadow-2xl shadow-primary/5 rounded-3xl p-8 border border-border">
                         <x-filament-panels::form wire:submit="authenticate">
                            {{ $this->form }}

                            <div class="mt-6">
                                <x-filament-panels::form.actions
                                    :actions="$this->getCachedFormActions()"
                                    :full-width="$this->hasFullWidthFormActions()"
                                />
                            </div>
                        </x-filament-panels::form>
                    </div>

                    <div class="text-center text-xs text-muted-foreground font-medium">
                        &copy; {{ date('Y') }} POS System &bull; <span class="text-accent">Fast & Secure</span>
                    </div>
                </div>
            </div>
        </div>
    </x-filament-panels::layout.base>
</div>
