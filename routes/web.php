<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'home-page');
Route::livewire('/cart', 'cart-page');
Route::livewire('/', 'page-renderer', ['slug' => 'catalog']);
Route::livewire('/pages/{slug}', 'page-renderer');
Route::livewire('/items/{slug}', 'catalog-item-detail');

Route::prefix('admin')->group(function () {
    Route::livewire('/catalog/items', 'admin-catalog-item-list');
    Route::livewire('/catalog/items/create', 'admin-catalog-item-create');
    Route::livewire('/catalog/accessories', 'admin-accessory-manager'); // Add this line
});