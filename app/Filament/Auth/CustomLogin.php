<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

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

    protected function throwFailureValidationException(): never
    {
        Notification::make()
            ->title('Login Gagal')
            ->body('Email atau password yang Anda masukkan salah. Silakan coba lagi.')
            ->danger()
            ->duration(5000)
            ->send();

        throw ValidationException::withMessages([
            'data.email' => ' ',
        ]);
    }

    public function authenticate(): ?\Filament\Http\Responses\Auth\Contracts\LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (TooManyRequestsException $e) {
            Notification::make()
                ->title('Terlalu Banyak Percobaan')
                ->body('Anda terlalu sering mencoba login. Silakan tunggu beberapa saat sebelum mencoba lagi.')
                ->danger()
                ->duration(5000)
                ->send();

            throw $e;
        }
    }
}
