<div class="min-h-screen bg-gray-50 dark:bg-gray-950">
    <x-filament-panels::layout.base :livewire="$this">
        <div class="flex flex-col md:flex-row min-h-screen">
            <!-- Left Side (Visual/Brand) -->
            <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-amber-400 to-orange-600 justify-center items-center p-12 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10 bg-[radial-gradient(circle_at_50%_50%,_rgba(255,255,255,0.8),transparent_50%)]"></div>
                <div class="absolute bottom-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full mix-blend-overlay filter blur-3xl transform translate-x-1/2 translate-y-1/2"></div>
                
                <div class="relative z-10 text-white text-center">
                    <h1 class="text-5xl font-extrabold mb-4 tracking-tight">KASIR PRO</h1>
                    <p class="text-xl opacity-90 font-light">Modern Point of Sale System</p>
                    <div class="mt-12 p-6 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20 shadow-xl max-w-sm mx-auto transform rotate-2 hover:rotate-0 transition-all duration-500">
                        <p class="text-sm font-medium">"Efficiency at your fingertips"</p>
                    </div>
                </div>
            </div>

            <!-- Right Side (Login Form) -->
            <div class="w-full md:w-1/2 flex items-center justify-center p-8 relative">
                <div class="w-full max-w-md space-y-8">
                    <div class="text-center md:text-left">
                        <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                            Welcome Back
                        </h2>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            Please sign in to continue
                        </p>
                    </div>

                    <div class="mt-8 bg-white dark:bg-gray-900 shadow-xl rounded-2xl p-8 border border-gray-100 dark:border-gray-800">
                         <x-filament-panels::form wire:submit="authenticate">
                            {{ $this->form }}

                            <x-filament-panels::form.actions
                                :actions="$this->getCachedFormActions()"
                                :full-width="$this->hasFullWidthFormActions()"
                            />
                        </x-filament-panels::form>
                    </div>

                    <div class="text-center text-xs text-gray-400">
                        &copy; {{ date('Y') }} POS System. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </x-filament-panels::layout.base>
</div>
