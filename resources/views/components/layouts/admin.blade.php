<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Dashboard' }} - POS System</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-background text-foreground font-sans antialiased min-h-screen">
    {{-- Header Component --}}
    <x-admin.header :title="$title ?? 'Dashboard'" :subtitle="$subtitle ?? 'Overview of your business'" />

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    {{-- Modals Placeholder --}}
    @stack('modals')
</body>
</html>
