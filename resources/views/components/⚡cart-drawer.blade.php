<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Services\CartService;

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

        // Dispatch event to update the header badge count
        $this->dispatch('cart-updated');
    }
}; ?>

<div x-data="{ open: false }" x-on:open-cart-drawer.window="open = true" class="relative z-50"
    aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <!-- Dark Overlay -->
    <div x-show="open" style="display: none;" x-transition.opacity
        class="fixed inset-0 bg-gray-900/20 bg-opacity-50 backdrop-blur-xs transition-opacity" @click="open = false">
    </div>

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">

                <!-- Sliding Panel -->
                <div x-show="open" style="display: none;"
                    x-transition:enter="transform transition ease-in-out duration-500"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-500"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="pointer-events-auto w-screen max-w-md">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">

                        <!-- Drawer Header -->
                        <div class="flex items-start justify-between px-6 py-6 border-b border-gray-100">
                            <h2 class="text-lg font-light tracking-wide text-gray-900" id="slide-over-title">Your Cart
                            </h2>
                            <button @click="open = false" type="button"
                                class="relative -m-2 p-2 text-gray-400 hover:text-gray-900 transition-colors">
                                <span class="sr-only">Close panel</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Cart Items -->
                        <div class="flex-1 overflow-y-auto px-6 py-8">
                            <ul role="list" class="-my-6 divide-y divide-gray-100">
                                @forelse($items as $id => $item)
                                    <li class="flex py-6">
                                        <div class="h-24 w-20 flex-shrink-0 overflow-hidden bg-gray-50">
                                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}"
                                                class="h-full w-full object-cover">
                                        </div>
                                        <div class="ml-4 flex flex-1 flex-col">
                                            <div>
                                                <div class="flex justify-between text-sm font-medium text-gray-900">
                                                    <h3>
                                                        <a
                                                            href="/items/{{ App\Models\CatalogItem::find($item['catalog_item_id'])?->url_slug }}">
                                                            {{ $item['title'] }}
                                                        </a>
                                                    </h3>
                                                    <p class="ml-4">£{{ number_format($item['price']) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex flex-1 items-end justify-between text-sm">
                                                <p class="text-gray-500">Qty {{ $item['quantity'] }}</p>
                                                <button wire:click="removeItem({{ $id }})" type="button"
                                                    class="font-medium text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest text-[10px] cursor-pointer">Remove</button>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <div class="flex flex-col items-center justify-center h-full text-center mt-12">
                                        <p class="text-gray-500 text-sm mb-4">Your cart is currently empty.</p>
                                        <button @click="open = false"
                                            class="text-xs font-medium tracking-[0.2em] text-gray-900 underline underline-offset-4 hover:text-gray-500 transition-colors">CONTINUE
                                            BROWSING</button>
                                    </div>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Drawer Footer -->
                        @if (count($items) > 0)
                            <div class="border-t border-gray-100 px-6 py-8">
                                <div class="flex justify-between text-base font-light text-gray-900 mb-6">
                                    <p>Subtotal</p>
                                    <p>£{{ number_format($total) }}</p>
                                </div>
                                <a href="/cart"
                                    class="flex w-full items-center justify-center bg-gray-900 px-6 py-4 text-xs font-medium tracking-[0.2em] text-white hover:bg-black transition-colors">
                                    VIEW CART & CHECKOUT
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
