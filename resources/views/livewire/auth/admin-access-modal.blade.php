<div>
    @if($showModal)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="margin: 0;">
        <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden transform transition-all">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-primary/10 rounded-lg text-primary">
                            <x-lucide-lock class="w-6 h-6" />
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Panel Admin</h3>
                    </div>
                    <button wire:click="$set('showModal', false)" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 rounded-lg transition-colors">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
                
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">
                    Halaman ini dikunci. Silakan masukkan password akses admin untuk melanjutkan ke dashboard.
                </p>
                
                <form wire:submit.prevent="checkPassword">
                    <div class="space-y-6">
                        <div>
                            <label for="admin_password" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2 block">
                                Password Akses
                            </label>
                            <div class="relative">
                                <input type="password" id="admin_password" wire:model="password" 
                                       class="w-full pl-3 pr-3 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all text-slate-900 dark:text-white"
                                       placeholder="••••••••" autofocus>
                            </div>
                            @error('password') 
                                <div class="flex items-center gap-2 mt-2 text-rose-500">
                                    <x-lucide-alert-circle class="w-4 h-4" />
                                    <span class="text-xs font-medium">{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        
                        <div class="flex gap-4 pt-4">
                            <button type="button" wire:click="$set('showModal', false)" 
                                    class="flex-1 px-4 py-3 text-sm font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                Kembali
                            </button>
                            <button type="submit" 
                                    class="flex-[2] px-4 py-3 text-sm font-semibold bg-primary text-white rounded-xl shadow-lg shadow-primary/20 hover:bg-primary/90 hover:shadow-primary/30 active:scale-95 transition-all">
                                Buka Dashboard
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
