<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>

    <body class="bg-white text-gray-900 antialiased min-h-screen flex flex-col">
        <!-- Alpine.js state wrapper -->
        <header x-data="{ mobileMenuOpen: false }" class="w-full pt-6 md:pt-12 pb-4 md:pb-6 border-b border-gray-100">

            <div class="relative flex items-center justify-between md:justify-center mb-4 md:mb-6 px-5 md:px-8">
                <a href="/"
                    class="text-2xl md:text-4xl font-light tracking-wide text-gray-900 hover:opacity-70 transition-opacity">
                    Woburn Gallery
                </a>

                <!-- Mobile Right Icons -->
                <div class="flex items-center space-x-5 md:hidden">
                    <a href="/cart" class="text-gray-900 hover:opacity-50 transition-opacity" aria-label="Cart">
                        <!-- Cart SVG here -->
                    </a>

                    <!-- Alpine click handler -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="text-gray-900 hover:opacity-50 transition-opacity" aria-label="Open menu">
                        <!-- Hamburger SVG here -->
                    </button>
                </div>

                <!-- Utility Icons (Desktop) -->
                <div
                    class="hidden md:flex absolute right-4 md:right-8 items-center space-x-4 md:space-x-6 text-gray-900">
                    <!-- Desktop SVGs here -->
                </div>
            </div>

            <!-- Navigation (Desktop) -->
            <nav
                class="hidden md:flex justify-center space-x-6 md:space-x-10 text-[11px] md:text-xs font-medium tracking-[0.2em] text-gray-400">
                <a href="/" class="hover:text-gray-900 transition-colors duration-300">HOME</a>
                <a href="/products" class="hover:text-gray-900 transition-colors duration-300">ARTWORK</a>
                <!-- Other links -->
            </nav>

            <!-- Alpine visibility toggle -->
            <div x-show="mobileMenuOpen" style="display: none;" class="md:hidden px-6 pb-4 pt-2">
                <!-- Mobile Menu Links -->
            </div>
        </header>

        <main class="flex-grow">
            {{ $slot }}
        </main>

        @livewireScripts
    </body>

</html>
