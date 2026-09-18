<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;

new class extends Component {
    use WithPagination;

    public $sort = 'newest';
    public $selectedCategories = [];
    public $minPrice = null;
    public $maxPrice = null;

    public function updated($property)
    {
        // Reset to page 1 if any filter or sort property changes
        if (in_array($property, ['sort', 'selectedCategories', 'minPrice', 'maxPrice'])) {
            $this->resetPage();
        }
    }

    public function with(): array
    {
        $query = Product::query()->where('product_status', true);

        // Apply Category Filter (AND across groups, OR within groups)
        if (!empty($this->selectedCategories)) {
            $groupedSelections = Category::whereIn('id', $this->selectedCategories)->get()->groupBy('parent_id');

            foreach ($groupedSelections as $parentId => $categories) {
                $query->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('categories.id', $categories->pluck('id'));
                });
            }
        }

        // Apply Price Filters
        if (!empty($this->minPrice)) {
            $query->where('base_price', '>=', $this->minPrice);
        }
        if (!empty($this->maxPrice)) {
            $query->where('base_price', '<=', $this->maxPrice);
        }

        // Apply Sorting
        match ($this->sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            'price_asc' => $query->orderBy('base_price', 'asc'),
            'price_desc' => $query->orderBy('base_price', 'desc'),
            'size_desc' => $query->orderByDesc(ProductVariant::selectRaw("CAST(JSON_EXTRACT(attributes, '$.volume') AS UNSIGNED)")->whereColumn('product_variants.product_id', 'products.id')->take(1)),
            default => $query->orderBy('created_at', 'desc'),
        };

        return [
            // Eager-load categories with their parents and variants for the UI
            'products' => $query->with(['media', 'artists', 'categories.parent', 'variants'])->paginate(12),
            'groupedCategories' => Category::where('parent_id', 0)->with('children')->get(),
        ];
    }
}; ?>

<div class="max-w-[1200px] mx-auto px-5 md:px-8 py-8 md:py-12">

    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 md:mb-12 border-b border-gray-100 pb-6">
        <div>
            <h1 class="text-2xl md:text-4xl font-light tracking-wide text-gray-900 mb-2">Artwork</h1>
            <p class="text-sm text-gray-500">{{ $products->total() }} pieces available</p>
        </div>

        <div class="mt-6 md:mt-0">
            <label for="sort" class="sr-only">Sort by</label>
            <select wire:model.live="sort" id="sort"
                class="text-sm border-0 border-b border-gray-200 focus:ring-0 focus:border-gray-900 bg-transparent py-2 pl-0 pr-8 cursor-pointer text-gray-700">
                <option value="newest">Date, new to old</option>
                <option value="oldest">Date, old to new</option>
                <option value="price_desc">Price, high to low</option>
                <option value="price_asc">Price, low to high</option>
                <option value="size_desc">Size, largest to smallest</option>
            </select>
        </div>
    </div>


    <div class="flex flex-col md:flex-row gap-10">

        <!-- Sidebar Filters -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <!-- Dynamic Hierarchical Categories -->
            @foreach ($groupedCategories as $parent)
                <div class="mb-8">
                    <h3 class="text-sm font-medium tracking-[0.1em] text-gray-900 mb-4 uppercase">{{ $parent->title }}
                    </h3>
                    <div class="space-y-3">
                        @foreach ($parent->children as $category)
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}"
                                    class="w-4 h-4 border-gray-300 rounded text-gray-900 focus:ring-gray-900">
                                <span class="ml-3 text-sm text-gray-600">{{ $category->title }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Price Range -->
            <div class="mb-8">
                <h3 class="text-sm font-medium tracking-[0.1em] text-gray-900 mb-4 uppercase">Price Range</h3>
                <div class="flex items-center space-x-2">
                    <input type="number" wire:model.live.debounce.500ms="minPrice" placeholder="Min £"
                        class="w-full text-sm border-gray-200 rounded focus:ring-gray-900 focus:border-gray-900 py-2">
                    <span class="text-gray-400">-</span>
                    <input type="number" wire:model.live.debounce.500ms="maxPrice" placeholder="Max £"
                        class="w-full text-sm border-gray-200 rounded focus:ring-gray-900 focus:border-gray-900 py-2">
                </div>
            </div>
        </aside>

        <!-- Product Grid -->
        <div class="flex-grow">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                @foreach ($products as $product)
                    <a href="/products/{{ $product->url_slug }}" class="group block cursor-pointer">
                        <div class="w-full aspect-[3/4] bg-gray-100 mb-5 overflow-hidden relative">
                            <img src="{{ $product->media->first()?->full_path ?? 'https://placehold.co/600x800' }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                                alt="{{ $product->title }}">
                        </div>

                        <h3 class="text-sm font-medium text-gray-900">{{ $product->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $product->artists->pluck('name')->join(', ') }}</p>

                        @php
                            $variant = $product->variants->first();
                            $attrs = $variant?->attributes ?? [];
                            // Extract only the categories where the parent is "Medium"
                            $mediums = $product->categories
                                ->where('parent.title', 'Medium')
                                ->pluck('title')
                                ->join(', ');
                        @endphp

                        <!-- Metadata Row: Medium & Dimensions -->
                        <p class="text-[11px] text-gray-400 mb-2 uppercase tracking-[0.05em]">
                            @if ($mediums)
                                {{ $mediums }}
                            @endif

                            @if ($mediums && isset($attrs['width']) && isset($attrs['height']))
                                <span class="mx-1 text-gray-300">|</span>
                            @endif

                            @if (isset($attrs['width']) && isset($attrs['height']))
                                {{ $attrs['width'] }} × {{ $attrs['height'] }} @if (isset($attrs['depth']))
                                    × {{ $attrs['depth'] }}
                                @endif
                            @endif
                        </p>

                        <p class="text-sm font-medium text-gray-900">£{{ number_format($product->base_price) }}
                        </p>
                    </a>
                @endforeach
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100 flex justify-center">
                {{ $products->links() }}
            </div>
        </div>

    </div>
</div>
