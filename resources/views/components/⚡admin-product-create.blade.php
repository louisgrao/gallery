<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Artist;
use App\Models\Category;

new #[Layout('components.layouts.admin')] class extends Component {
    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|numeric|min:0')]
    public $base_price = '';

    #[Validate('required|array|min:1', message: 'Please select at least one artist.')]
    public $selectedArtists = [];

    #[Validate('array')]
    public $selectedCategories = [];

    // Variant Specifics
    public $width = '';
    public $height = '';
    public $depth = '';
    public $framing = 'Unframed';
    public $quantity = 1;

    public function save()
    {
        $this->validate();

        // 1. Create the base product
        $product = Product::create([
            'title' => $this->title,
            // Append a short unique ID to the slug to prevent collisions
            'url_slug' => Str::slug($this->title) . '-' . substr(uniqid(), -4),
            'description' => $this->description,
            'base_price' => $this->base_price,
            'product_status' => true, // Defaulting to active
        ]);

        // 2. Attach Many-to-Many Relationships
        $product->artists()->attach($this->selectedArtists);
        $product->categories()->attach($this->selectedCategories);

        // 3. Calculate Volume for Size Sorting
        $w = (float) $this->width;
        $h = (float) $this->height;
        $d = !empty($this->depth) ? (float) $this->depth : 1.5; // Default 1.5cm for paintings
        $volume = $w > 0 && $h > 0 ? $w * $h * $d : 0;

        // 4. Create the Default Variant
        ProductVariant::create([
            'product_id' => $product->id,
            'title' => 'Original Artwork',
            'sku' => 'ART-' . str_pad($product->id, 4, '0', STR_PAD_LEFT),
            'price' => $this->base_price,
            'quantity' => $this->quantity,
            'attributes' => [
                'width' => $this->width,
                'height' => $this->height,
                'depth' => $this->depth,
                'volume' => $volume,
                'framing' => $this->framing,
            ],
        ]);

        return redirect('/admin/products');
    }

    public function with(): array
    {
        return [
            'artists' => Artist::orderBy('name')->get(),
            'groupedCategories' => Category::where('parent_id', 0)->with('children')->get(),
        ];
    }
}; ?>

<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <a href="/admin/products" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">&larr; Back to
            Catalog</a>
        <h1 class="text-2xl font-light text-gray-900 mt-4">Add New Artwork</h1>
    </div>

    <form wire:submit="save" class="flex flex-col lg:flex-row gap-8">

        <!-- Main Form Content -->
        <div class="w-full lg:w-2/3 space-y-8">
            <div class="bg-white p-6 border border-gray-200 shadow-sm">
                <h2 class="text-sm font-medium tracking-[0.1em] uppercase text-gray-900 mb-6">Basic Information</h2>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input wire:model="title" type="text"
                            class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                        @error('title')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea wire:model="description" rows="4"
                            class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price (£)</label>
                            <input wire:model="base_price" type="number" step="0.01"
                                class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                            @error('base_price')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity (Stock)</label>
                            <input wire:model="quantity" type="number"
                                class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                            <p class="text-xs text-gray-500 mt-1">Use 1 for originals, -1 for unlimited.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 border border-gray-200 shadow-sm">
                <h2 class="text-sm font-medium tracking-[0.1em] uppercase text-gray-900 mb-6">Physical Specifications
                </h2>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Width (cm)</label>
                        <input wire:model="width" type="number" step="0.1"
                            class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Height (cm)</label>
                        <input wire:model="height" type="number" step="0.1"
                            class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Depth (cm)</label>
                        <input wire:model="depth" type="number" step="0.1"
                            class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Framing Details</label>
                    <input wire:model="framing" type="text" placeholder="e.g. Unframed, Framed (Black Wood)"
                        class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                </div>
            </div>
        </div>

        <!-- Sidebar (Relationships) -->
        <div class="w-full lg:w-1/3 space-y-8">

            <!-- Actions -->
            <div class="bg-white p-6 border border-gray-200 shadow-sm">
                <button type="submit"
                    class="w-full bg-gray-900 text-white px-4 py-3 text-xs font-medium tracking-[0.1em] uppercase hover:bg-black transition-colors cursor-pointer">
                    Save Artwork
                </button>
            </div>

            <!-- Artists -->
            <div class="bg-white p-6 border border-gray-200 shadow-sm">
                <h2 class="text-sm font-medium tracking-[0.1em] uppercase text-gray-900 mb-4">Artists</h2>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach ($artists as $artist)
                        <label class="flex items-center">
                            <input wire:model="selectedArtists" value="{{ $artist->id }}" type="checkbox"
                                class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900 cursor-pointer">
                            <span class="ml-2 text-sm text-gray-700">{{ $artist->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('selectedArtists')
                    <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
                @enderror
            </div>

            <!-- Categories -->
            <div class="bg-white p-6 border border-gray-200 shadow-sm">
                <h2 class="text-sm font-medium tracking-[0.1em] uppercase text-gray-900 mb-4">Categories</h2>
                <div class="space-y-6 max-h-96 overflow-y-auto">
                    @foreach ($groupedCategories as $parent)
                        <div>
                            <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">
                                {{ $parent->title }}</h3>
                            <div class="space-y-2">
                                @foreach ($parent->children as $category)
                                    <label class="flex items-center">
                                        <input wire:model="selectedCategories" value="{{ $category->id }}"
                                            type="checkbox"
                                            class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900 cursor-pointer">
                                        <span class="ml-2 text-sm text-gray-700">{{ $category->title }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </form>
</div>
