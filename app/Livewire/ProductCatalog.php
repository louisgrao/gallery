<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class ProductCatalog extends Component
{
    use WithPagination;

    public $sort = 'newest';

    public function render()
    {
        $query = Product::where('product_status', true)->with('variants');

        // Apply basic sorting
        if ($this->sort === 'highest') {
            $query->orderBy('base_price', 'desc');
        } elseif ($this->sort === 'lowest') {
            $query->orderBy('base_price', 'asc');
        } elseif ($this->sort === 'popular') {
            $query->orderBy('sales_count', 'desc');
        } else {
            // Default: newest
            $query->latest();
        }

        return view('livewire.product-catalog', [
            'products' => $query->paginate(12),
        ]);
    }
}