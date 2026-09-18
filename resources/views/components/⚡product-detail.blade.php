<?php

use Livewire\Component;
use App\Models\Product;

new class extends Component {
    public Product $product;

    public function mount($slug)
    {
        // Fetch the product and eager-load relationships, or throw a 404 if not found
        $this->product = Product::with(['artists', 'media', 'variants'])
            ->where('url_slug', $slug)
            ->where('product_status', true)
            ->firstOrFail();
    }
}; ?>

<div class="max-w-[1200px] mx-auto px-5 md:px-8 py-8 md:py-12">

    <div class="flex flex-col md:flex-row gap-10 md:gap-16">

        <!-- Left Column: Image Gallery -->
        <div class="w-full md:w-3/5 bg-gray-50 flex items-center justify-center p-4">
            <img src="{{ $product->media->first()?->full_path ?? 'https://placehold.co/800x1000/e4e4e7/71717a?text=Image+Unavailable' }}"
                alt="{{ $product->title }}" class="w-full max-h-[80vh] object-contain shadow-sm">
        </div>

        <!-- Right Column: Product Details -->
        <div class="w-full md:w-2/5 flex flex-col justify-center">

            <!-- Breadcrumbs -->
            <nav class="text-[10px] md:text-xs font-medium tracking-[0.2em] text-gray-400 mb-6 md:mb-10">
                <a href="/" class="hover:text-gray-900 transition-colors">HOME</a> /
                <a href="/products" class="hover:text-gray-900 transition-colors">ARTWORK</a> /
                <span class="text-gray-900">{{ strtoupper($product->title) }}</span>
            </nav>

            <h1 class="text-3xl md:text-5xl font-light tracking-wide text-gray-900 mb-3">
                {{ $product->title }}
            </h1>

            <!-- Linked Artists -->
            <div class="text-lg text-gray-500 mb-6">
                @foreach ($product->artists as $artist)
                    <a href="/artists/{{ $artist->url_slug }}"
                        class="hover:text-gray-900 transition-colors underline decoration-transparent hover:decoration-gray-300 underline-offset-4">
                        {{ $artist->name }}
                    </a>
                    @if (!$loop->last)
                        ,
                    @endif
                @endforeach
            </div>

            <p class="text-2xl font-light text-gray-900 mb-10">
                £{{ number_format($product->base_price) }}
            </p>

            <!-- Add to Cart Action -->
            <button
                class="w-full py-4 bg-gray-900 text-white text-xs font-medium tracking-[0.2em] hover:bg-black transition-colors mb-10">
                ADD TO CART
            </button>

            <!-- Description -->
            <div class="border-t border-gray-100 pt-8 mt-4">
                <h3 class="text-xs font-medium tracking-[0.2em] text-gray-900 mb-4">ABOUT THIS PIECE</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $product->description ?? 'Original artwork available for purchase. Contact the gallery for detailed condition reports or to arrange a private viewing.' }}
                </p>
            </div>

        </div>
    </div>

</div>
