<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules\Password;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $userId = null;
    
    // Form fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $selected_roles = [];

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal($id = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'password', 'selected_roles', 'userId']);

        if ($id) {
            $user = User::findOrFail($id);
            $this->userId = $user->id;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->selected_roles = $user->roles->pluck('name')->toArray();
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'selected_roles' => 'required|array|min:1',
            'password' => $this->userId ? 'nullable|min:8' : 'required|min:8',
        ];

        $this->validate($rules);

        if ($this->userId) {
            $user = User::find($this->userId);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
            ];
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            $user->syncRoles($this->selected_roles);
            $message = 'User berhasil diperbarui';
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);
            $user->assignRole($this->selected_roles);
            $message = 'User berhasil ditambahkan';
        }

        $this->showModal = false;
        $this->dispatch('toast', ['type' => 'success', 'message' => $message]);
    }

    public function delete($id)
    {
        if ($id == auth()->id()) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Anda tidak bisa menghapus diri sendiri']);
            return;
        }

        $user = User::findOrFail($id);
        $user->delete();
        $this->dispatch('toast', ['type' => 'success', 'message' => 'User berhasil dihapus']);
    }

    public function render()
    {
        $users = User::with('roles')
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.user-management', [
            'users' => $users,
            'available_roles' => ['owner', 'admin', 'kasir']
        ])->layout('components.layouts.admin', [
            'title' => 'Manajemen User',
            'subtitle' => 'Kelola hak akses dan akun pengguna sistem'
        ]);
    }
}
