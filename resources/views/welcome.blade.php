<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kui - Aprende Kichwa</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex flex-col justify-between">
        <!-- Header -->
        <header class="w-full py-6 px-4 sm:px-6 lg:px-8">
            <nav class="flex justify-between items-center max-w-7xl mx-auto">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-amber-600 dark:text-amber-500">Kui</span>
                </div>
                {{-- @if (Route::has('login')) --}}
                <div class="flex space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="font-medium hover:text-amber-600 dark:hover:text-amber-400">Dashboard</a>
                    @else
                        <a href="{{ url('/admin/login') }}"
                            class="font-medium hover:text-amber-600 dark:hover:text-amber-400">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="font-medium hover:text-amber-600 dark:hover:text-amber-400">Register</a>
                        @endif
                    @endauth
                </div>
                {{-- @endif --}}
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="flex-grow flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h1
                    class="text-5xl sm:text-6xl font-black tracking-tight mb-8 bg-clip-text text-transparent bg-gradient-to-r from-amber-500 to-orange-600">
                    Aprende notas de música con Kui
                </h1>
                <p class="text-xl sm:text-2xl text-gray-600 dark:text-gray-400 mb-10 leading-relaxed">
                    Conecta con tus raíces. Kui te ayuda a aprender Kichwa de una manera divertida, interactiva y
                    gratuita.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#"
                        class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-amber-600 hover:bg-amber-700 md:py-4 md:text-lg transition duration-150 ease-in-out shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <span>Descargar APK</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                    </a>
                    <a href="{{ url('/admin/login') }}"
                        class="inline-flex items-center justify-center px-8 py-3 border border-gray-300 dark:border-gray-700 text-base font-medium rounded-full text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 md:py-4 md:text-lg transition duration-150 ease-in-out shadow-sm hover:shadow-md">
                        Comenzar en Web
                    </a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full py-6 px-4">
            <div class="max-w-7xl mx-auto text-center text-gray-500 dark:text-gray-400 text-sm">
                &copy; {{ date('Y') }} Kui App. Todos los derechos reservados.
            </div>
        </footer>
    </div>
</body>

</html>