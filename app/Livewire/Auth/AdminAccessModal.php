<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Livewire\Attributes\On;

class AdminAccessModal extends Component
{
    public $password = '';
    public $showModal = false;

    #[On('open-admin-modal')]
    public function openModal()
    {
        $this->showModal = true;
        $this->password = '';
        $this->resetErrorBag();
    }

    public function checkPassword()
    {
        if ($this->password === 'KasirPos2026') {
            $admin = User::where('email', 'admin@pos.com')->first();
            if ($admin) {
                Auth::login($admin);
                return redirect()->to('/admin');
            }
        }

        $this->addError('password', 'Password salah!');
        $this->password = '';
    }

    public function render()
    {
        return view('livewire.auth.admin-access-modal');
    }
}
