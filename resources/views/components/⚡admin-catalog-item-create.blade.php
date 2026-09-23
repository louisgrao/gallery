<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Support\Str;
use App\Models\CatalogItem;
use App\Models\CatalogVariant;
use App\Models\CatalogAccessory;

new #[Layout('components.layouts.admin')] class extends Component {
    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|numeric|min:0')]
    public $base_price = '';

    #[Validate('array')]
    public $selectedAccessories = [];

    // Base Variant Specs (Stored in JSON)
    public $sku = '';
    public $quantity = 1;
    public $width = '';
    public $height = '';
    public $depth = '';
    public $framing = 'Unframed';

    public function save()
    {
        $this->validate();

        // 1. Create the base item
        $item = CatalogItem::create([
            'title' => $this->title,
            'url_slug' => Str::slug($this->title) . '-' . substr(uniqid(), -4),
            'description' => $this->description,
            'base_price' => $this->base_price,
            'item_status' => true,
        ]);

        // 2. Attach selected accessories
        if (!empty($this->selectedAccessories)) {
            $item->accessories()->attach($this->selectedAccessories);
        }

        // 3. Calculate Volume (if physical dimensions provided)
        $w = (float) $this->width;
        $h = (float) $this->height;
        $d = !empty($this->depth) ? (float) $this->depth : 1.5;
        $volume = $w > 0 && $h > 0 ? $w * $h * $d : 0;

        // 4. Create the Default Variant
        CatalogVariant::create([
            'catalog_item_id' => $item->id,
            'title' => 'Default',
            'sku' => $this->sku ?: 'ITM-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
            'price' => $this->base_price,
            'quantity' => $this->quantity,
            'attributes' => array_filter([
                'width' => $this->width,
                'height' => $this->height,
                'depth' => $this->depth,
                'volume' => $volume > 0 ? $volume : null,
                'framing' => $this->framing,
            ]),
        ]);

        return redirect('/admin/catalog/items');
    }

    public function with(): array
    {
        return [
            // Fetch all top-level accessory groups (parents) and their children
            'accessoryGroups' => CatalogAccessory::where('parent_id', 0)->with('children')->get(),
        ];
    }
}; ?>

<div class="max-w-5xl mx-auto">
    <div class="mb-8">
        <a href="/admin/catalog/items" class="text-sm text-gray-500 hover:text-gray-900 transition-colors">&larr; Back to
            Items</a>
        <h1 class="text-2xl font-light text-gray-900 mt-4">Add New Catalog Item</h1>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Base Price (£)</label>
                            <input wire:model="base_price" type="number" step="0.01"
                                class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                            @error('base_price')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU (Optional)</label>
                            <input wire:model="sku" type="text"
                                class="w-full border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                        <input wire:model="quantity" type="number"
                            class="w-full max-w-xs border-gray-300 rounded-sm focus:ring-gray-900 focus:border-gray-900">
                        <p class="text-xs text-gray-500 mt-1">Use -1 for unlimited stock (e.g., digital downloads or
                            print-on-demand).</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 border border-gray-200 shadow-sm">
                <h2 class="text-sm font-medium tracking-[0.1em] uppercase text-gray-900 mb-6">Physical Attributes
                    (Optional)</h2>

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

        <!-- Sidebar (Taxonomy) -->
        <div class="w-full lg:w-1/3 space-y-8">

            <div class="bg-white p-6 border border-gray-200 shadow-sm">
                <button type="submit"
                    class="w-full bg-gray-900 text-white px-4 py-3 text-xs font-medium tracking-[0.1em] uppercase hover:bg-black transition-colors cursor-pointer">
                    Save Catalog Item
                </button>
            </div>

            <!-- Dynamic Accessory Groups -->
            <div class="bg-white p-6 border border-gray-200 shadow-sm">
                <h2 class="text-sm font-medium tracking-[0.1em] uppercase text-gray-900 mb-4">Accessories & Tags</h2>
                <div class="space-y-6 max-h-[600px] overflow-y-auto pr-2">
                    @foreach ($accessoryGroups as $group)
                        @if ($group->children->count() > 0)
                            <div>
                                <h3
                                    class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2 border-b border-gray-100 pb-1">
                                    {{ $group->title }}</h3>
                                <div class="space-y-2 mt-2">
                                    @foreach ($group->children as $accessory)
                                        <label class="flex items-center">
                                            <input wire:model="selectedAccessories" value="{{ $accessory->id }}"
                                                type="checkbox"
                                                class="w-4 h-4 text-gray-900 border-gray-300 rounded focus:ring-gray-900 cursor-pointer">
                                            <span class="ml-2 text-sm text-gray-700">{{ $accessory->title }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

        </div>
    </form>
</div>
