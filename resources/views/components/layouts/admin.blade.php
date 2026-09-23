<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Woburn Gallery</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="bg-gray-50 text-gray-900 font-sans antialiased flex h-screen overflow-hidden">

        <!-- Admin Sidebar -->
        <aside class="w-64 bg-gray-900 text-white flex flex-col hidden md:flex flex-shrink-0">
            <div class="h-16 flex items-center px-6 border-b border-gray-800">
                <span class="text-sm font-medium tracking-[0.2em] uppercase">Gallery Admin</span>
            </div>
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="/admin/catalog/items"
                            class="block px-6 py-3 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                            Catalog Items
                        </a>
                    </li>
                    <li>
                        <a href="/admin/catalog/accessories"
                            class="block px-6 py-3 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                            Taxonomy & Accessories
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="block px-6 py-3 text-sm text-gray-300 hover:text-white hover:bg-gray-800 transition-colors">
                            Categories
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- Main Content Area
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 flex-shrink-0">
                <div class="md:hidden text-sm font-medium tracking-[0.2em] uppercase">Gallery Admin</div>
                <div class="text-sm text-gray-500 flex-1 text-right">
                    Admin User
                </div>
            </header>
        -->

            <!-- Top Header -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 flex-shrink-0">
                <div class="text-sm font-medium tracking-[0.2em] uppercase text-gray-900">
                    <span class="md:hidden">Admin</span>
                    <span class="hidden md:inline">Artwork Catalog</span>
                </div>
                <div class="text-sm text-gray-500">
                    Admin User
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 md:p-10">
                {{ $slot }}
            </main>
        </div>

    </body>

</html>
