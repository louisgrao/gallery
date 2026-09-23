<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\CatalogItem;

new #[Layout('components.layouts.admin')] class extends Component {
    use WithPagination;

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        return [
            'items' => CatalogItem::with(['accessories', 'variants'])
                ->where('title', 'like', '%' . $this->search . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(10),
        ];
    }
}; ?>

<div>
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-light text-gray-900">Catalog Items</h1>
            <p class="mt-2 text-sm text-gray-600">Manage global inventory, pricing, and availability.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="/admin/catalog/items/create"
                class="inline-block bg-gray-900 text-white px-4 py-2 text-xs font-medium tracking-[0.1em] uppercase hover:bg-black transition-colors">
                Add Item
            </a>
        </div>
    </div>

    <div class="mb-6">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search items by title..."
            class="w-full max-w-md text-sm border-gray-200 rounded-md focus:ring-gray-900 focus:border-gray-900 py-2 shadow-sm">
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Accessories</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->title }}</div>
                                <div class="text-xs text-gray-500">{{ $item->variants->first()?->sku ?? 'No SKU' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                {{ $item->accessories->pluck('title')->join(', ') ?: 'None' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                £{{ number_format($item->base_price) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($item->item_status)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Draft</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="#" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                <button class="text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                No items found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $items->links() }}
        </div>
    </div>
</div>
