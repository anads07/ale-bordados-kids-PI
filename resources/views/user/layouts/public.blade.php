<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Alê Bordados Kids — enxovais e roupas infantis com bordados personalizados.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('mock.store.name').' | Bordados personalizados')</title>

    {{-- Estrutura pública compartilhada pelas páginas da loja. --}}
    @vite(['resources/css/app.css', 'resources/js/user/app.js'])
</head>
<body>
    @include('user.partials.header')
    @include('user.partials.flash')

    <main>
        @yield('content')
    </main>

    @include('user.partials.footer')
    @include('user.partials.cart-drawer')

    <x-shared::coming-soon-dialog />
</body>
</html>
