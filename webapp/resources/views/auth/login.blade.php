<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Proline</title>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-white">
        <div class="flex flex-row min-h-screen justify-center items-center">
            <div class="bg-white rounded-xl w-110 h-80 text-center">
                <h1 class="text-3xl p-2">Welcome</h1>
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="p-2">
                        <input 
                            placeholder="Email"
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus
                            class="input">
                    </div>

                    <div class="p-2">
                        <input 
                            placeholder="Password"
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="input">
                    </div>

                    <div class="p-3">
                        <button type="submit" class="btn-dark w-full">Sign in</button>
                    </div>

                    @error('login-error')
                        <div class="px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>
    </body>
</html>
