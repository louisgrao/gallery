<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Page;
use App\Models\CatalogItem;
use App\Models\CatalogAccessory;

new class extends Component {
    use WithPagination;

    public Page $page;
    public array $activeFilters = [];

    public function mount($slug)
    {
        $this->page = Page::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Initialize empty arrays for the UI checkboxes based on the JSON configuration
        $sidebarFilters = $this->page->display_settings['sidebar_filters'] ?? [];
        foreach ($sidebarFilters as $groupId) {
            $this->activeFilters[$groupId] = [];
        }
    }

    public function updatedActiveFilters()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $query = CatalogItem::with(['variants', 'media', 'accessories.parent'])->where('item_status', true);

        // 1. Apply strict page rules (e.g., if this page is configured to ONLY show "Paintings")
        $mustHave = $this->page->query_rules['must_have_accessories'] ?? [];
        if (!empty($mustHave)) {
            foreach ($mustHave as $accessoryId) {
                $query->whereHas('accessories', function ($q) use ($accessoryId) {
                    $q->where('catalog_accessories.id', $accessoryId);
                });
            }
        }

        // 2. Apply interactive sidebar filters (AND logic across different groups, OR within a group)
        foreach ($this->activeFilters as $groupId => $selectedIds) {
            // Filter out empty values from unchecking boxes
            $selectedIds = array_filter($selectedIds);
            if (!empty($selectedIds)) {
                $query->whereHas('accessories', function ($q) use ($selectedIds) {
                    $q->whereIn('catalog_accessories.id', $selectedIds);
                });
            }
        }

        // 3. Fetch the specific taxonomy groups allowed in the sidebar for this page
        $sidebarGroupIds = $this->page->display_settings['sidebar_filters'] ?? [];
        $filterGroups = CatalogAccessory::whereIn('id', $sidebarGroupIds)->with('children')->get();

        return [
            'items' => $query->paginate(12),
            'filterGroups' => $filterGroups,
            // Pass the card UI rules to the frontend
            'cardAccessoryGroups' => $this->page->display_settings['show_accessories_on_card'] ?? [],
        ];
    }
}; ?>

<div class="max-w-[1200px] mx-auto px-5 md:px-8 py-8 md:py-12">

    <div class="mb-10 md:mb-16 text-center">
        <h1 class="text-3xl md:text-5xl font-light tracking-wide text-gray-900 mb-4">{{ $page->title }}</h1>
    </div>

    <div class="flex flex-col md:flex-row gap-10">

        <!-- Dynamic Sidebar Filters -->
        @if ($filterGroups->count() > 0)
            <div class="w-full md:w-64 flex-shrink-0">
                <div class="sticky top-8 space-y-8">
                    @foreach ($filterGroups as $group)
                        @if ($group->children->count() > 0)
                            <div>
                                <h3 class="text-xs font-medium tracking-[0.2em] text-gray-900 mb-4 uppercase">
                                    {{ $group->title }}</h3>
                                <div class="space-y-3">
                                    @foreach ($group->children as $accessory)
                                        <label class="flex items-center group cursor-pointer">
                                            <div
                                                class="relative flex items-center justify-center w-4 h-4 mr-3 border border-gray-300 group-hover:border-gray-900 transition-colors">
                                                <input wire:model.live="activeFilters.{{ $group->id }}"
                                                    type="checkbox" value="{{ $accessory->id }}"
                                                    class="peer absolute opacity-0 w-full h-full cursor-pointer">
                                                <div class="hidden peer-checked:block w-2.5 h-2.5 bg-gray-900"></div>
                                            </div>
                                            <span
                                                class="text-sm font-light text-gray-600 group-hover:text-gray-900 transition-colors">{{ $accessory->title }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Catalog Grid -->
        <div class="flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-12">
                @forelse($items as $item)
                    <div class="group relative flex flex-col">
                        <a href="/items/{{ $item->url_slug }}" class="absolute inset-0 z-10"><span class="sr-only">View
                                {{ $item->title }}</span></a>

                        <div
                            class="w-full bg-gray-50 aspect-[3/4] mb-4 flex items-center justify-center p-4 overflow-hidden relative">
                            @if ($item->media->isNotEmpty())
                                <img src="{{ $item->media->first()->full_path }}" alt="{{ $item->title }}"
                                    class="max-w-full max-h-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-700 ease-out">
                            @else
                                <span class="text-gray-400 text-sm tracking-widest uppercase">No Image</span>
                            @endif
                        </div>

                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-lg font-light text-gray-900 leading-tight mb-1">{{ $item->title }}</h2>

                                <!-- Dynamic Card Accessories -->
                                @if (!empty($cardAccessoryGroups))
                                    @php
                                        // Filter the item's accessories to only show those belonging to the approved parent groups
$displayTags = $item->accessories
    ->whereIn('parent_id', $cardAccessoryGroups)
    ->pluck('title')
    ->join(', ');
                                    @endphp
                                    @if ($displayTags)
                                        <p class="text-sm text-gray-500 font-light mb-1">{{ $displayTags }}</p>
                                    @endif
                                @endif
                            </div>
                            <p class="text-base text-gray-900 font-medium">£{{ number_format($item->base_price) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <p class="text-gray-500 font-light text-lg">No items match your current selection.</p>
                        <button wire:click="$set('activeFilters', [])"
                            class="mt-4 text-xs font-medium tracking-[0.2em] text-gray-900 border-b border-gray-900 pb-1 hover:text-gray-500 transition-colors">CLEAR
                            FILTERS</button>
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $items->links() }}
            </div>
        </div>

    </div>
</div>
