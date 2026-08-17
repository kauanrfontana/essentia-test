<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Clientes')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50 text-gray-900 antialiased">
    <div class="mx-auto max-w-4xl px-4 py-10">
        <header class="mb-8 flex items-center justify-between">
            <a href="{{ route('customers.index') }}" class="text-2xl font-semibold">
                Cadastro de Clientes
            </a>
            @hasSection('header-action')
                @yield('header-action')
            @endif
        </header>

        @if (session('status'))
            <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
