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

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()
                    ->prefixIcon('lucide-mail'),
                $this->getPasswordFormComponent()
                    ->prefixIcon('lucide-lock'),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    public function getRedirectUrl(): string
    {
        return route('dashboard');
    }
}
