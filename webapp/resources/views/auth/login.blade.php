<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-gray-900">
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
                            autofocus>
                    </div>

                    <div class="p-2">
                        <input 
                            placeholder="Password"
                            type="password" 
                            id="password" 
                            name="password" 
                            required>
                    </div>

                    <div class="p-3">
                        <button type="submit" class="bg-green-500 rounded-xl w-20 h-10 text-white">Login</button>
                    </div>

                    @error('login-error')
                        <div style="color: red;">{{ $message }}</div>
                    @enderror
                </form>
            </div>
        </div>
    </body>
</html>
