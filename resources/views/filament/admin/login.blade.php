<div class="min-h-screen flex items-start justify-center bg-[#f7f8fa] p-4 pt-12 md:pt-20 relative overflow-hidden">
    <x-filament-panels::layout.base :livewire="$this">
        <!-- Background decoration -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-accent/10 rounded-full blur-3xl"></div>
        </div>

        <div class="w-full max-w-md glass animate-fade-in relative z-10 rounded-3xl shadow-2xl p-6 md:px-8 md:pt-8 md:pb-6 border-white/50">
            <div class="text-center mb-6">
                <div class="mx-auto w-16 h-16 rounded-3xl flex items-center justify-center shadow-xl mb-4">
                    <x-lucide-store class="w-8 h-8 text-primary" />
                </div>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-[#1a1c21]">
                        POS System
                    </h2>
                    <p class="mt-1 text-sm text-muted-foreground">
                        Masuk ke akun Anda untuk melanjutkan
                    </p>
                </div>
            </div>

            <x-filament-panels::form wire:submit="authenticate">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </div>
            </x-filament-panels::form>

            <div class="mt-6 text-center text-[10px] text-muted-foreground font-medium uppercase tracking-widest opacity-60">
                &copy; {{ date('Y') }} POS System &bull; Fast & Secure
            </div>
        </div>
    </x-filament-panels::layout.base>
</div>
