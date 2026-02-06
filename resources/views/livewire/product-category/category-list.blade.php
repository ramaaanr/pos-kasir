<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-foreground">Kategori Produk</h1>
            <p class="text-muted-foreground mt-1">Kelola kategori produk untuk organisasi yang lebih baik.</p>
        </div>
        <button 
            wire:click="openModal"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium transition-colors rounded-md bg-primary text-primary-foreground hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50"
        >
            <x-lucide-plus class="h-4 w-4" />
            Tambah Kategori
        </button>
    </div>

    {{-- Modal Form --}}
    <div 
        x-data="{ show: @entangle('showModal') }" 
        x-show="show" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="show" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                class="absolute inset-0 transition-opacity" 
                aria-hidden="true"
            >
                <div class="absolute inset-0 bg-background/80 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div 
                x-show="show" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="relative inline-block align-bottom bg-card rounded-lg border border-border text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full z-10"
            >
                <div class="bg-card px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-foreground" id="modal-title">
                                {{ $selectedCategoryId ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-muted-foreground mb-1">Nama Kategori</label>
                                    <input 
                                        wire:model="name"
                                        type="text" 
                                        id="name"
                                        placeholder="Masukkan nama kategori"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 @error('name') border-destructive @enderror"
                                        @keydown.enter="$wire.save()"
                                    >
                                    @error('name')
                                        <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-muted/30 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button 
                        wire:click="save"
                        type="button" 
                        class="inline-flex w-full justify-center rounded-md bg-primary px-3 py-2 text-sm font-semibold text-primary-foreground shadow-sm hover:bg-primary/90 sm:ml-3 sm:w-auto"
                    >
                        {{ $selectedCategoryId ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                    </button>
                    <button 
                        wire:click="closeModal"
                        type="button" 
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-card px-3 py-2 text-sm font-semibold text-foreground shadow-sm border border-border hover:bg-muted sm:mt-0 sm:w-auto"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div 
        x-data="{ show: @entangle('showDeleteModal') }" 
        x-show="show" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="show" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0" 
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0" 
                class="absolute inset-0 transition-opacity" 
                aria-hidden="true"
            >
                <div class="absolute inset-0 bg-background/80 backdrop-blur-sm"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div 
                x-show="show" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                class="relative inline-block align-bottom bg-card rounded-lg border border-border text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full z-10"
            >
                <div class="bg-card px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <x-lucide-alert-triangle class="h-6 w-6 text-red-600" />
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-bold text-foreground" id="modal-title">
                                Hapus Kategori
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-muted-foreground">
                                    Apakah Anda yakin ingin menghapus kategori <span class="font-bold text-foreground">"{{ $categoryToDelete?->name }}"</span>? Tindakan ini dapat dibatalkan melalui sistem admin jika diperlukan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-muted/30 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button 
                        wire:click="delete"
                        type="button" 
                        class="inline-flex w-full justify-center rounded-md bg-destructive px-3 py-2 text-sm font-semibold text-destructive-foreground shadow-sm hover:bg-destructive/90 sm:ml-3 sm:w-auto"
                    >
                        Hapus Kategori
                    </button>
                    <button 
                        wire:click="closeDeleteModal"
                        type="button" 
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-card px-3 py-2 text-sm font-semibold text-foreground shadow-sm border border-border hover:bg-muted sm:mt-0 sm:w-auto"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Notification System --}}
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
        @toast.window="add($event.detail[0])"
        class="fixed bottom-4 right-4 z-[100] flex flex-col gap-2"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0"
                x-transition:leave-end="opacity-0 translate-x-8"
                class="flex items-center gap-3 px-4 py-3 rounded-lg border shadow-lg overflow-hidden min-w-[300px]"
                :class="{
                    'bg-card border-border border-l-4 border-l-green-500': toast.type === 'success',
                    'bg-card border-border border-l-4 border-l-red-500': toast.type === 'error'
                }"
            >
                <div :class="toast.type === 'success' ? 'text-green-500' : 'text-red-500'">
                    <template x-if="toast.type === 'success'">
                        <x-lucide-check-circle class="h-5 w-5" />
                    </template>
                    <template x-if="toast.type === 'error'">
                        <x-lucide-alert-circle class="h-5 w-5" />
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

    {{-- Summary Cards (Requested Improvement) --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="flex items-center gap-4 p-4 rounded-lg border border-border bg-card shadow-sm">
            <div class="p-2 rounded-full bg-primary/10 text-primary">
                <x-lucide-tags class="h-5 w-5" />
            </div>
            <div>
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Total Kategori</p>
                <h3 class="text-xl font-bold text-foreground">{{ $summary['total'] }}</h3>
            </div>
        </div>
        <div class="flex items-center gap-4 p-4 rounded-lg border border-border bg-card shadow-sm border-l-4 border-l-green-500">
            <div class="p-2 rounded-full bg-green-500/10 text-green-500">
                <x-lucide-check-circle class="h-5 w-5" />
            </div>
            <div>
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Aktif</p>
                <h3 class="text-xl font-bold text-foreground">{{ $summary['active'] }}</h3>
            </div>
        </div>
        <div class="flex items-center gap-4 p-4 rounded-lg border border-border bg-card shadow-sm border-l-4 border-l-red-500">
            <div class="p-2 rounded-full bg-red-500/10 text-red-500">
                <x-lucide-power-off class="h-5 w-5" />
            </div>
            <div>
                <p class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Nonaktif</p>
                <h3 class="text-xl font-bold text-foreground">{{ $summary['inactive'] }}</h3>
            </div>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="flex flex-col gap-4 sm:flex-row">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-muted-foreground">
                <x-lucide-search class="h-4 w-4" />
            </div>
            <input 
                wire:model.live.debounce.300ms="search"
                type="text" 
                class="flex h-10 w-full rounded-md border border-input bg-card px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-10" 
                placeholder="Cari kategori..."
            >
        </div>
        <select 
            wire:model.live="status"
            class="flex h-10 w-full sm:w-[150px] rounded-md border border-input bg-card px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
        >
            <option value="all">Semua Status</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
        </select>
    </div>

    {{-- Table Section --}}
    <div class="rounded-lg border border-border bg-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-muted/30">
                        <th 
                            wire:click="sort('name')"
                            class="px-4 py-3 text-left font-medium text-muted-foreground cursor-pointer hover:text-foreground transition-colors group"
                        >
                            <div class="flex items-center gap-2">
                                Nama Kategori
                                @if($sortBy === 'name')
                                    @if($sortDirection === 'asc')
                                        <x-lucide-chevron-up class="h-4 w-4 text-primary" />
                                    @else
                                        <x-lucide-chevron-down class="h-4 w-4 text-primary" />
                                    @endif
                                @else
                                    <x-lucide-chevrons-up-down class="h-3 w-3 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>
                        <th 
                            wire:click="sort('products_count')"
                            class="px-4 py-3 text-center font-medium text-muted-foreground cursor-pointer hover:text-foreground transition-colors group"
                        >
                            <div class="flex items-center justify-center gap-2">
                                Jumlah Produk
                                @if($sortBy === 'products_count')
                                    @if($sortDirection === 'asc')
                                        <x-lucide-chevron-up class="h-4 w-4 text-primary" />
                                    @else
                                        <x-lucide-chevron-down class="h-4 w-4 text-primary" />
                                    @endif
                                @else
                                    <x-lucide-chevrons-up-down class="h-3 w-3 opacity-0 group-hover:opacity-100" />
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-center font-medium text-muted-foreground">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground w-[70px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($categories as $category)
                        <tr class="hover:bg-muted/50 transition-colors">
                            <td class="px-4 py-3 font-medium text-foreground">
                                {{ $category->name }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-secondary text-secondary-foreground">
                                    {{ $category->products_count }} produk
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($category->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-500/10 text-green-500 border border-green-500/20">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-muted text-muted-foreground border border-border">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end" x-data="{ open: false }">
                                    <div class="relative">
                                        <button 
                                            @click="open = !open"
                                            @click.away="open = false"
                                            class="p-1.5 hover:bg-muted rounded-md transition-colors text-muted-foreground hover:text-foreground"
                                        >
                                            <x-lucide-more-horizontal class="h-4 w-4" />
                                        </button>
                                        
                                        <div 
                                            x-show="open"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-0 mt-1 w-48 rounded-md shadow-lg bg-card border border-border z-10 py-1"
                                            style="display: none;"
                                        >
                                            <button 
                                                wire:click="edit({{ $category->id }})"
                                                @click="open = false"
                                                class="flex w-full items-center gap-2 px-4 py-2 text-sm text-foreground hover:bg-muted transition-colors text-left"
                                            >
                                                <x-lucide-pencil class="h-4 w-4" />
                                                Edit Kategori
                                            </button>
                                            <button 
                                                wire:click="toggleStatus({{ $category->id }})"
                                                @click="open = false"
                                                class="flex w-full items-center gap-2 px-4 py-2 text-sm text-foreground hover:bg-muted transition-colors text-left"
                                            >
                                                <x-lucide-power class="h-4 w-4 {{ $category->is_active ? 'text-orange-500' : 'text-green-500' }}" />
                                                {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                            <hr class="my-1 border-border">
                                            <button 
                                                wire:click="confirmDelete({{ $category->id }})"
                                                @click="open = false"
                                                @if($category->products_count > 0) disabled @endif
                                                class="flex w-full items-center gap-2 px-4 py-2 text-sm {{ $category->products_count > 0 ? 'text-muted-foreground opacity-50 cursor-not-allowed' : 'text-destructive hover:bg-red-500/10' }} transition-colors text-left"
                                                @if($category->products_count > 0) title="Kategori ini tidak dapat dihapus karena memiliki produk" @endif
                                            >
                                                <x-lucide-trash-2 class="h-4 w-4" />
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <x-lucide-tags class="h-8 w-8 text-muted-foreground/30" />
                                    <p class="text-sm text-muted-foreground">Tidak ada kategori ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($categories->hasPages())
            <div class="border-t border-border px-4 py-3">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    {{-- Summary Footer --}}
    <div class="flex gap-4 text-sm text-muted-foreground items-center">
        <span>Total: <strong>{{ $summary['total'] }}</strong> kategori</span>
        <span class="h-1 w-1 rounded-full bg-muted-foreground/30"></span>
        <span>Aktif: <strong>{{ $summary['active'] }}</strong></span>
        <span class="h-1 w-1 rounded-full bg-muted-foreground/30"></span>
        <span>Nonaktif: <strong>{{ $summary['inactive'] }}</strong></span>
    </div>
</div>
