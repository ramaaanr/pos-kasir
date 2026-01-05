<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

class CustomLogin extends Login
{
    public function getView(): string
    {
        return 'filament.admin.login';
    }
}
