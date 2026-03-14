<div
    x-data="{ 
        toasts: [], 
        add(toast) { 
            this.toasts.push({ 
                id: Date.now(), 
                ...toast 
            }); 
            setTimeout(() => { 
                this.toasts = this.toasts.filter(t => t.id !== toast.id); 
            }, 3000); 
        } 
    }"
    @toast.window="add($event.detail[0] || $event.detail)"
    class="fixed bottom-4 right-4 z-[100] flex flex-col gap-2 pointer-events-none">
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-8"
            class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-lg border shadow-lg overflow-hidden min-w-[300px]"
            :class="{
                'bg-card border-border border-l-4 border-l-green-500': toast.type === 'success',
                'bg-card border-border border-l-4 border-l-red-500': toast.type === 'error',
                'bg-card border-border border-l-4 border-l-blue-500': toast.type === 'info'
            }">
            <div :class="{
                'text-green-500': toast.type === 'success',
                'text-red-500': toast.type === 'error',
                'text-blue-500': toast.type === 'info'
            }">
                <template x-if="toast.type === 'success'">
                    <x-lucide-check-circle class="h-5 w-5" />
                </template>
                <template x-if="toast.type === 'error'">
                    <x-lucide-alert-circle class="h-5 w-5" />
                </template>
                <template x-if="toast.type === 'info'">
                    <x-lucide-info class="h-5 w-5" />
                </template>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-foreground" x-text="toast.message"></p>
            </div>
            <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-muted-foreground hover:text-foreground">
                <x-lucide-x class="h-4 w-4" />
            </button>
        </div>
    </template>
</div>