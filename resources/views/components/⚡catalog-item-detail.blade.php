<?php

use Livewire\Component;
use App\Models\CatalogItem;
use App\Models\CatalogVariant;
use App\Services\CartService;

new class extends Component {
    public CatalogItem $item;
    public $selectedVariantId;
    public $activeVariant;

    // Grouped accessories for dynamic display
    public $groupedAccessories = [];

    public function mount($slug)
    {
        $this->item = CatalogItem::where('url_slug', $slug)
            ->where('item_status', true)
            ->with(['variants', 'media', 'accessories.parent'])
            ->firstOrFail();

        $this->activeVariant = $this->item->variants->first();
        $this->selectedVariantId = $this->activeVariant?->id;

        // Group accessories by their parent taxonomy for the UI
        foreach ($this->item->accessories as $accessory) {
            if ($accessory->parent) {
                $this->groupedAccessories[$accessory->parent->title][] = $accessory;
            }
        }
    }

    public function updatedSelectedVariantId($value)
    {
        $this->activeVariant = $this->item->variants->firstWhere('id', $value);
    }

    public function addToCart(CartService $cartService)
    {
        if (!$this->activeVariant) {
            return;
        }

        $added = $cartService->add($this->activeVariant, 1);

        if ($added) {
            $this->dispatch('cart-updated');
            $this->dispatch('open-cart');
        } else {
            session()->flash('error', 'Item is out of stock.');
        }
    }
}; ?>

<div class="max-w-[1200px] mx-auto px-5 md:px-8 py-12 md:py-16">

    <!-- Breadcrumbs -->
    <nav class="mb-8 md:mb-12 text-xs font-medium tracking-[0.2em] uppercase text-gray-500">
        <a href="/" class="hover:text-gray-900 transition-colors">Catalog</a>
        <span class="mx-2">/</span>
        <span class="text-gray-900">{{ $item->title }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-12 lg:gap-20">

        <!-- Left Column: Media Gallery -->
        <div class="w-full lg:w-3/5">
            <div class="bg-gray-50 aspect-[4/5] flex items-center justify-center p-8 relative">
                @if ($item->media->isNotEmpty())
                    <img src="{{ $item->media->first()->full_path }}" alt="{{ $item->title }}"
                        class="max-w-full max-h-full object-contain mix-blend-multiply">
                @else
                    <span class="text-gray-400 text-sm tracking-widest uppercase">No Image Available</span>
                @endif
            </div>

            <!-- Thumbnails (if multiple images exist) -->
            @if ($item->media->count() > 1)
                <div class="grid grid-cols-4 gap-4 mt-4">
                    @foreach ($item->media->skip(1) as $media)
                        <div
                            class="bg-gray-50 aspect-square flex items-center justify-center p-2 cursor-pointer hover:border-gray-900 border border-transparent transition-colors">
                            <img src="{{ $media->full_path }}"
                                class="max-w-full max-h-full object-contain mix-blend-multiply">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right Column: Item Details -->
        <div class="w-full lg:w-2/5 flex flex-col justify-center">

            <h1 class="text-3xl md:text-5xl font-light tracking-wide text-gray-900 mb-4">{{ $item->title }}</h1>

            <div class="text-2xl font-light text-gray-900 mb-8">
                £{{ number_format($activeVariant?->price ?? $item->base_price) }}
            </div>

            <!-- Dynamic Accessory Metadata (e.g., Artists, Medium, Type) -->
            @if (!empty($groupedAccessories))
                <div class="space-y-3 border-t border-gray-100 py-6 mb-6">
                    @foreach ($groupedAccessories as $parentTitle => $accessories)
                        <div class="flex flex-wrap text-sm">
                            <span class="w-24 text-gray-500">{{ $parentTitle }}</span>
                            <span class="flex-1 text-gray-900">
                                @foreach ($accessories as $index => $accessory)
                                    @if ($parentTitle === 'Artists')
                                        <a href="/pages/{{ $accessory->url_slug }}"
                                            class="hover:underline underline-offset-4">{{ $accessory->title }}</a>
                                    @else
                                        {{ $accessory->title }}
                                    @endif
                                    @if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($item->description)
                <div class="prose prose-sm prose-gray font-light text-gray-600 mb-8 leading-relaxed">
                    <p>{{ $item->description }}</p>
                </div>
            @endif

            <!-- Variant Selection & Add to Cart -->
            <form wire:submit.prevent="addToCart" class="mt-auto">

                @if ($item->variants->count() > 1)
                    <div class="mb-6">
                        <label
                            class="block text-xs font-medium tracking-[0.2em] text-gray-900 uppercase mb-3">Options</label>
                        <select wire:model.live="selectedVariantId"
                            class="w-full border-gray-300 text-sm focus:ring-gray-900 focus:border-gray-900 rounded-none shadow-sm py-3 px-4">
                            @foreach ($item->variants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->title }} -
                                    £{{ number_format($variant->price) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="text-red-600 text-sm mb-4">{{ session('error') }}</div>
                @endif

                <button type="submit"
                    class="w-full bg-gray-900 text-white px-8 py-4 text-sm font-medium tracking-[0.2em] uppercase hover:bg-black transition-colors cursor-pointer disabled:bg-gray-300 disabled:cursor-not-allowed"
                    @if ($activeVariant && $activeVariant->quantity == 0) disabled @endif>
                    {{ $activeVariant && $activeVariant->quantity == 0 ? 'Out of Stock' : 'Add to Cart' }}
                </button>
            </form>

            <!-- Dynamic JSON Attributes (Dimensions, Formats, etc.) -->
            @if ($activeVariant && $activeVariant->attributes)
                <div class="mt-10 pt-8 border-t border-gray-100">
                    <h3 class="text-xs font-medium tracking-[0.2em] text-gray-900 uppercase mb-4">Specifications</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4 text-sm">
                        @foreach ($activeVariant->attributes as $key => $value)
                            @if ($value && $key !== 'volume' && $key !== 'download_path')
                                <div>
                                    <dt class="text-gray-500 capitalize">{{ str_replace('_', ' ', $key) }}</dt>
                                    <dd class="text-gray-900 mt-1">
                                        {{ is_array($value) ? implode(', ', $value) : $value }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </div>
            @endif

        </div>
    </div>
</div>
