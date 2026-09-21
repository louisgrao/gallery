<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    private string $sessionKey = 'gallery_cart';

    /**
     * Add a variant to the cart. 
     * Returns false if the requested quantity exceeds available stock.
     */
    public function add(ProductVariant $variant, int $amount = 1): bool
    {
        $cart = Session::get($this->sessionKey, []);

        $currentQuantity = isset($cart[$variant->id]) ? $cart[$variant->id]['quantity'] : 0;
        $requestedQuantity = $currentQuantity + $amount;

        // Verify stock limits (quantity > -1 means strictly tracked inventory)
        if ($variant->quantity > -1 && $requestedQuantity > $variant->quantity) {
            return false; 
        }

        if (isset($cart[$variant->id])) {
            $cart[$variant->id]['quantity'] = $requestedQuantity;
        } else {
            $cart[$variant->id] = [
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'title' => $variant->product->title,
                'artist' => $variant->product->artists->pluck('name')->join(', '),
                'price' => $variant->price,
                'image' => $variant->product->media->first()?->full_path,
                'quantity' => $amount,
            ];
        }

        Session::put($this->sessionKey, $cart);

        return true;
    }

    /**
     * Remove a specific item from the cart.
     */
    public function remove(int $variantId): void
    {
        $cart = Session::get($this->sessionKey, []);

        if (isset($cart[$variantId])) {
            unset($cart[$variantId]);
            Session::put($this->sessionKey, $cart);
        }
    }

    /**
     * Retrieve all items currently in the cart.
     */
    public function getItems(): array
    {
        return Session::get($this->sessionKey, []);
    }

    /**
     * Calculate the total monetary value of the cart.
     */
    public function getTotal(): float|int
    {
        $items = $this->getItems();
        
        return array_reduce($items, function ($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0);
    }

    /**
     * Get the total number of unique items in the cart.
     */
    public function getCount(): int
    {
        return count($this->getItems());
    }

    /**
     * Empty the cart entirely.
     */
    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }
}