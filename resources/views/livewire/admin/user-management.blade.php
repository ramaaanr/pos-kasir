<div class="space-y-6 animate-fade-in">
    {{-- Header Section --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-foreground">Manajemen User</h2>
            <p class="text-muted-foreground text-sm mt-1">Kelola data pengguna dan hak akses sistem</p>
        </div>
        <button wire:click="openModal()" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-semibold transition-all duration-200 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 shadow-lg shadow-primary/20">
            <x-lucide-plus class="h-4 w-4" />
            Tambah User
        </button>
    </div>

    {{-- Filter Section --}}
    <div class="bg-card border border-border/50 rounded-xl p-4 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
            {{-- Search --}}
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-muted-foreground">
                    <x-lucide-search class="h-4 w-4" />
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari nama atau email..."
                    class="flex h-10 w-full rounded-lg border border-input bg-background/50 pl-10 pr-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring transition-all">
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="rounded-xl border border-border/50 bg-card overflow-hidden shadow-sm min-h-[400px] flex flex-col">
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="border-b border-border bg-muted/30">
                        <th class="px-4 py-3 font-semibold text-muted-foreground">Nama</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground">Email</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-center">Role</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground">Dibuat Pada</th>
                        <th class="px-4 py-3 font-semibold text-muted-foreground text-right w-[100px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($users as $user)
                    <tr class="hover:bg-muted/50 transition-colors group">
                        <td class="px-4 py-4 font-bold text-foreground">
                            {{ $user->name }}
                        </td>
                        <td class="px-4 py-4 text-muted-foreground">
                            {{ $user->email }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-tight 
                                @if($role->name === 'owner') bg-rose-500/10 text-rose-600 border border-rose-500/20 
                                @elseif($role->name === 'admin') bg-amber-500/10 text-amber-600 border border-amber-500/20 
                                @else bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 @endif">
                                {{ $role->name }}
                            </span>
                            @endforeach
                        </td>
                        <td class="px-4 py-4 text-xs text-muted-foreground font-medium">
                            {{ $user->created_at->translatedFormat('d F Y') }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openModal({{ $user->id }})" class="p-2 hover:bg-primary/10 rounded-lg text-primary transition-colors">
                                    <x-lucide-pencil class="h-4 w-4" />
                                </button>
                                @if($user->id !== auth()->id())
                                <button onclick="confirm('Yakin ingin menghapus user ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $user->id }})" class="p-2 hover:bg-destructive/10 rounded-lg text-destructive transition-colors">
                                    <x-lucide-trash-2 class="h-4 w-4" />
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-muted-foreground italic">Tidak ada data user found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-4 py-4 border-t border-border">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Form Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-[100] overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-6">
            <div class="fixed inset-0 bg-background/80 backdrop-blur-md transition-opacity" wire:click="closeModal"></div>
            
            <div class="relative bg-card w-full max-w-lg rounded-3xl shadow-2xl border border-border overflow-hidden animate-in zoom-in duration-300">
                <div class="p-6 border-b border-border bg-muted/30 flex items-center justify-between">
                    <h3 class="text-xl font-black text-foreground tracking-tight">{{ $userId ? 'Edit User' : 'Tambah User Baru' }}</h3>
                    <button wire:click="closeModal" class="p-2 hover:bg-muted rounded-full transition-colors">
                        <x-lucide-x class="w-5 h-5 text-muted-foreground" />
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-5">
                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="w-full h-11 px-4 rounded-xl border border-border bg-background/50 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-medium text-sm">
                        @error('name') <span class="text-xs text-destructive font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1">Alamat Email</label>
                        <input type="email" wire:model="email" class="w-full h-11 px-4 rounded-xl border border-border bg-background/50 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-medium text-sm">
                        @error('email') <span class="text-xs text-destructive font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1">Role / Hak Akses</label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach($available_roles as $role)
                            <label wire:key="role-{{ $role }}" class="relative flex items-center justify-center h-12 rounded-xl border-2 transition-all cursor-pointer group
                                @if(in_array($role, $selected_roles)) border-primary bg-primary/5 @else border-border bg-muted/10 hover:border-primary/30 @endif">
                                <input type="checkbox" value="{{ $role }}" wire:model="selected_roles" class="absolute inset-0 opacity-0 cursor-pointer">
                                <span class="text-[10px] font-black uppercase tracking-widest @if(in_array($role, $selected_roles)) text-primary @else text-muted-foreground @endif pointer-events-none">
                                    {{ $role }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                        @error('selected_roles') <span class="text-xs text-destructive font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black uppercase tracking-widest text-muted-foreground ml-1">Password {{ $userId ? '(Kosongkan jika tidak ingin diubah)' : '' }}</label>
                        <input type="password" wire:model="password" class="w-full h-11 px-4 rounded-xl border border-border bg-background/50 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all font-medium text-sm">
                        @error('password') <span class="text-xs text-destructive font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="closeModal" class="flex-1 h-12 rounded-2xl bg-muted text-foreground font-black text-xs uppercase tracking-widest hover:bg-muted/80 transition-all border border-border">Batal</button>
                        <button type="submit" class="flex-1 h-12 rounded-2xl bg-primary text-white font-black text-xs uppercase tracking-widest hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">Simpan Detail</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
