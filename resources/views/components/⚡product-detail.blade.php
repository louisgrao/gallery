<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Product;
use App\Services\CartService;

new class extends Component {
    public Product $product;
    public int $inCartAmount = 0;

    public function mount($slug, CartService $cartService)
    {
        $this->product = Product::with(['artists', 'media', 'variants', 'categories.parent'])
            ->where('url_slug', $slug)
            ->where('product_status', true)
            ->firstOrFail();

        $this->checkCartState($cartService);
    }

    #[On('cart-updated')]
    public function checkCartState(CartService $cartService)
    {
        $variant = $this->product->variants->first();
        $cart = $cartService->getItems();

        $this->inCartAmount = isset($cart[$variant?->id]) ? $cart[$variant->id]['quantity'] : 0;
    }
    /*
    public function addToCart(CartService $cartService)
    {
        // Get the default variant (you can expand this later when adding size/frame selection)
        $variant = $this->product->variants->first();

        if ($variant) {
            $success = $cartService->add($variant);

            if ($success) {
                // Tell the global layout to update the cart counter and open the slide-out
                $this->dispatch('cart-updated');
                $this->dispatch('open-cart-drawer');
            } else {
                // Optional: Dispatch a front-end notification that stock is maxed out
                $this->dispatch('notify', message: 'Maximum available stock already in cart.');
            }
        }
    }*/

    public function addToCart(CartService $cartService)
    {
        $variant = $this->product->variants->first();

        if ($variant) {
            $success = $cartService->add($variant);

            if ($success) {
                $this->checkCartState($cartService);
                $this->dispatch('cart-updated');
                $this->dispatch('open-cart-drawer');
            }
        }
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
            <!--
            <button wire:click="addToCart"
                class="w-full py-4 bg-gray-900 text-white text-xs font-medium tracking-[0.2em] hover:bg-black transition-colors mb-10">
                ADD TO CART
            </button>
            -->

            @php
                $variant = $product->variants->first();
                $isSoldOut = $variant && $variant->quantity > -1 && $inCartAmount >= $variant->quantity;
            @endphp

            <button wire:click="addToCart" @if ($isSoldOut) disabled @endif
                class="w-full py-4 text-xs font-medium tracking-[0.2em] transition-colors mb-10 cursor-pointer disabled:cursor-not-allowed disabled:bg-gray-200 disabled:text-gray-400 @if (!$isSoldOut) bg-gray-900 text-white hover:bg-black @endif">
                {{ $isSoldOut ? 'ALREADY IN CART' : 'ADD TO CART' }}
            </button>

            <div class="border-t border-gray-100 pt-8 mt-4">
                <h3 class="text-xs font-medium tracking-[0.2em] text-gray-900 mb-4">ABOUT THIS PIECE</h3>
                <p class="text-sm text-gray-600 leading-relaxed">
                    {{ $product->description ?? 'Original artwork available for purchase. Contact the gallery for detailed condition reports or to arrange a private viewing.' }}
                </p>
            </div>

            <!-- Specifications -->
            @php
                $attrs = $variant?->attributes ?? [];
                $mediums = $product->categories->where('parent.title', 'Medium')->pluck('title')->join(', ');
                $locations = $product->categories->where('parent.title', 'Location')->pluck('title')->join(', ');
            @endphp

            <!-- Specifications -->
            <div class="border-t border-gray-100 pt-8 mt-8">
                <h3 class="text-xs font-medium tracking-[0.2em] text-gray-900 mb-4">SPECIFICATIONS</h3>
                <ul class="text-sm text-gray-600 space-y-3">
                    @if ($mediums)
                        <li><span class="font-medium text-gray-900">Medium:</span> {{ $mediums }}</li>
                    @endif

                    @if (isset($attrs['width']) && isset($attrs['height']))
                        <li>
                            <span class="font-medium text-gray-900">Dimensions:</span>
                            {{ $attrs['width'] }} × {{ $attrs['height'] }}cm @if (isset($attrs['depth']))
                                × {{ $attrs['depth'] }}cm
                            @endif
                        </li>
                    @endif

                    @if (isset($attrs['framing']))
                        <li><span class="font-medium text-gray-900">Framing:</span> {{ $attrs['framing'] }}</li>
                    @endif

                    @if ($locations)
                        <li><span class="font-medium text-gray-900">Location:</span> {{ $locations }}</li>
                    @endif
                </ul>
            </div>

        </div>
    </div>

</div>
