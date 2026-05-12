<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proline</title>

    @vite(['resources/css/app.css'])
</head>
<body class="bg-white min-h-screen flex flex-col">
    {{-- Page content --}}
    <main class="@yield('main-class', 'max-w-7xl mx-auto px-6 py-10 w-full')">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
