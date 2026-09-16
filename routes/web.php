<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ProductCatalog;

// Route the homepage directly to the catalog component
Route::get('/', ProductCatalog::class);