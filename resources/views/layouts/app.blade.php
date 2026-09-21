<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Woburn Gallery' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>

    <body class="bg-white text-gray-900 antialiased min-h-screen flex flex-col">

        <!-- Alpine.js state initialized on the header -->
        <header x-data="{ mobileMenuOpen: false }"
            class="max-w-[1200px] mx-auto w-full pt-6 md:pt-12 pb-4 md:pb-6 border-b border-gray-100">

            <div class="relative flex items-center justify-between md:justify-center mb-4 md:mb-6 px-5 md:px-8">
                <a href="/"
                    class="text-2xl md:text-4xl font-light tracking-wide text-gray-900 hover:opacity-70 transition-opacity">
                    Woburn Gallery
                </a>

                <div class="flex items-center space-x-5 md:hidden">
                    <livewire:cart-badge />

                    <!-- Alpine click event toggles the menu state -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="text-gray-900 hover:opacity-50 transition-opacity" aria-label="Open menu">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-7 h-7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>

                <div
                    class="hidden md:flex absolute right-4 md:right-8 items-center space-x-4 md:space-x-6 text-gray-900">
                    <a href="#" class="hover:opacity-50 transition-opacity" aria-label="Account">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </a>
                    <a href="#" class="hover:opacity-50 transition-opacity" aria-label="Wishlist">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                    </a>
                    <livewire:cart-badge />
                </div>
            </div>

            <nav
                class="hidden md:flex justify-center space-x-6 md:space-x-10 text-[11px] md:text-xs font-medium tracking-[0.2em] text-gray-400">
                <a href="/" class="hover:text-gray-900 transition-colors duration-300">HOME</a>
                <a href="/products" class="hover:text-gray-900 transition-colors duration-300">ARTWORK</a>
                <a href="/artists" class="hover:text-gray-900 transition-colors duration-300">ARTISTS</a>
                <a href="#" class="hover:text-gray-900 transition-colors duration-300">NEWS</a>
                <a href="#" class="hover:text-gray-900 transition-colors duration-300">ABOUT</a>
            </nav>

            <!-- Alpine visibility toggle -->
            <div x-show="mobileMenuOpen" style="display: none;" class="md:hidden px-6 pb-4 pt-2">
                <nav class="flex flex-col space-y-5 text-sm font-medium tracking-[0.2em] text-gray-700">
                    <a href="/" class="hover:text-gray-900 transition-colors">HOME</a>
                    <a href="/products" class="hover:text-gray-900 transition-colors">ARTWORK</a>
                    <a href="/artists" class="hover:text-gray-900 transition-colors">ARTISTS</a>
                    <a href="#" class="hover:text-gray-900 transition-colors">NEWS</a>
                    <a href="#" class="hover:text-gray-900 transition-colors">ABOUT</a>
                </nav>

                <div class="flex items-center space-x-6 mt-8 pt-6 border-t border-gray-100 text-gray-700">
                    <a href="#" class="flex items-center hover:text-gray-900 transition-colors space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span class="text-xs tracking-[0.1em] font-medium">ACCOUNT</span>
                    </a>
                    <a href="#" class="flex items-center hover:text-gray-900 transition-colors space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                        <span class="text-xs tracking-[0.1em] font-medium">WISHLIST</span>
                    </a>
                </div>
            </div>

        </header>

        <main class="max-w-[1200px] mx-auto px-5 md:px-8 py-8 md:py-12">
            {{ $slot }}
        </main>

        <livewire:cart-drawer />

    </body>

</html>
