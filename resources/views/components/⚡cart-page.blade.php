<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\CartService;
use App\Models\Product;

new class extends Component {
    public array $items = [];
    public float $total = 0;

    public function mount(CartService $cartService)
    {
        $this->loadCart($cartService);
    }

    #[On('cart-updated')]
    public function loadCart(CartService $cartService)
    {
        $this->items = $cartService->getItems();
        $this->total = $cartService->getTotal();
    }

    public function removeItem(CartService $cartService, $variantId)
    {
        $cartService->remove($variantId);
        $this->loadCart($cartService);

        // Keep the global header badge accurate
        $this->dispatch('cart-updated');
    }
}; ?>

<div class="max-w-[1200px] mx-auto px-5 md:px-8 py-8 md:py-16">

    <div class="mb-12 border-b border-gray-100 pb-6">
        <h1 class="text-3xl md:text-5xl font-light tracking-wide text-gray-900 mb-2">Shopping Cart</h1>
    </div>

    @if (count($items) > 0)
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-24">

            <!-- Left Column: Cart Items -->
            <div class="w-full lg:w-2/3">
                <ul role="list" class="divide-y divide-gray-100">
                    @foreach ($items as $id => $item)
                        <li class="flex py-8">
                            <div class="h-32 w-24 md:h-48 md:w-36 flex-shrink-0 bg-gray-50 overflow-hidden">
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                                    class="h-full w-full object-cover">
                            </div>

                            <div class="ml-6 flex flex-1 flex-col justify-between">
                                <div>
                                    <div class="flex justify-between font-medium text-gray-900 mb-2">
                                        <h3 class="text-lg md:text-xl font-light"><a
                                                href="/products/{{ Product::find($item['product_id'])->url_slug }}">{{ $item['title'] }}</a>
                                        </h3>
                                        <p class="text-lg font-light ml-4">£{{ number_format($item['price']) }}</p>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ $item['artist'] }}</p>
                                </div>

                                <div class="flex flex-1 items-end justify-between">
                                    <p class="text-sm text-gray-500">Quantity: {{ $item['quantity'] }}</p>
                                    <button wire:click="removeItem({{ $id }})" type="button"
                                        class="text-xs font-medium tracking-[0.2em] text-gray-400 hover:text-gray-900 transition-colors cursor-pointer uppercase">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-gray-50 p-8 sticky top-8">
                    <h2 class="text-lg font-light tracking-wide text-gray-900 mb-6 border-b border-gray-200 pb-4">Order
                        Summary</h2>

                    <dl class="space-y-4 text-sm text-gray-600 mb-8">
                        <div class="flex justify-between">
                            <dt>Subtotal</dt>
                            <dd class="font-medium text-gray-900">£{{ number_format($total) }}</dd>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-4">
                            <dt>Shipping</dt>
                            <dd>Calculated at checkout</dd>
                        </div>
                        <div
                            class="flex justify-between border-t border-gray-200 pt-4 text-base font-light text-gray-900">
                            <dt>Total</dt>
                            <dd>£{{ number_format($total) }}</dd>
                        </div>
                    </dl>

                    <button
                        class="w-full py-4 bg-gray-900 text-white text-xs font-medium tracking-[0.2em] hover:bg-black transition-colors cursor-pointer">
                        PROCEED TO CHECKOUT
                    </button>

                    <div class="mt-6 text-center">
                        <a href="/products"
                            class="text-xs font-medium tracking-[0.2em] text-gray-500 hover:text-gray-900 transition-colors underline underline-offset-4">
                            CONTINUE BROWSING
                        </a>
                    </div>
                </div>
            </div>

        </div>
    @else
        <div class="text-center py-24 bg-gray-50">
            <h2 class="text-2xl font-light text-gray-900 mb-4">Your cart is empty</h2>
            <p class="text-gray-500 mb-8">Discover original artwork by our featured artists.</p>
            <a href="/products"
                class="inline-block py-4 px-8 border border-gray-900 text-gray-900 text-xs font-medium tracking-[0.2em] hover:bg-gray-900 hover:text-white transition-colors">
                BROWSE CATALOG
            </a>
        </div>
    @endif

</div>
