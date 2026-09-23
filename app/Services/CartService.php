<?php

namespace App\Services;

use App\Models\CatalogVariant;
use Illuminate\Support\Facades\Session;

class CartService
{
    private string $sessionKey = 'gallery_cart';

    public function add(CatalogVariant $variant, int $amount = 1): bool
    {
        $cart = Session::get($this->sessionKey, []);

        $currentQuantity = isset($cart[$variant->id]) ? $cart[$variant->id]['quantity'] : 0;
        $requestedQuantity = $currentQuantity + $amount;

        // Check inventory limits (-1 indicates unlimited stock)
        if ($variant->quantity > -1 && $requestedQuantity > $variant->quantity) {
            return false; 
        }

        if (isset($cart[$variant->id])) {
            $cart[$variant->id]['quantity'] = $requestedQuantity;
        } else {
            // Only load the relationships necessary for the cart display
            $variant->loadMissing('item.media');
            
            $cart[$variant->id] = [
                'catalog_item_id' => $variant->catalog_item_id,
                'variant_id' => $variant->id,
                'title' => $variant->item->title,
                'price' => $variant->price,
                'image' => $variant->item->media->first()?->full_path,
                'quantity' => $amount,
            ];
        }

        Session::put($this->sessionKey, $cart);

        return true;
    }

    public function remove(int $variantId): void
    {
        $cart = Session::get($this->sessionKey, []);

        if (isset($cart[$variantId])) {
            unset($cart[$variantId]);
            Session::put($this->sessionKey, $cart);
        }
    }

    public function getItems(): array
    {
        return Session::get($this->sessionKey, []);
    }

    public function getTotal(): float|int
    {
        $items = $this->getItems();
        
        return array_reduce($items, function ($total, $item) {
            return $total + ($item['price'] * $item['quantity']);
        }, 0);
    }

    public function getCount(): int
    {
        return count($this->getItems());
    }

    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }
}