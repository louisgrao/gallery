<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Products</h1>

        <!-- Sorting Dropdown bound to Livewire -->
        <select wire:model.live="sort"
            class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            <option value="newest">Newest Arrivals</option>
            <option value="lowest">Price: Low to High</option>
            <option value="highest">Price: High to Low</option>
            <option value="popular">Most Popular</option>
        </select>
    </div>

    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @forelse ($products as $product)
            <a href="/product/{{ $product->url_slug }}" class="group block">
                <div
                    class="w-full bg-white rounded-lg overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                    <!-- Placeholder Image -->
                    <div class="h-48 bg-gray-100 flex items-center justify-center">
                        <span class="text-gray-400">Image Placeholder</span>
                    </div>

                    <div class="p-4">
                        <h2 class="text-lg font-medium text-gray-900 group-hover:underline">{{ $product->title }}</h2>
                        <div class="mt-2 flex justify-between items-center">
                            <span class="text-gray-600 font-bold">${{ number_format($product->base_price, 2) }}</span>

                            @if ($product->sales_count > 0)
                                <span class="text-xs text-gray-400">{{ $product->sales_count }} sold</span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-500">No products found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination Links -->
    <div class="mt-8">
        {{ $products->links() }}
    </div>
</div>
