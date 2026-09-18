<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'home-page');
Route::livewire('/products', 'product-catalog');
Route::livewire('/products/{slug}', 'product-detail'); 
Route::livewire('/artists', 'artist-directory');
Route::livewire('/artists/{slug}', 'artist-profile');