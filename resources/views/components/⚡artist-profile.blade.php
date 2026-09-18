<?php

use Livewire\Component;
use App\Models\Artist;

new class extends Component {
    public Artist $artist;

    public function mount($slug)
    {
        // Fetch the artist and their approved products with media
        $this->artist = Artist::with([
            'products' => function ($query) {
                $query->where('product_status', true)->with('media');
            },
        ])
            ->where('url_slug', $slug)
            ->firstOrFail();
    }
}; ?>

<div class="max-w-[1200px] mx-auto px-5 md:px-8 py-8 md:py-12">

    <!-- Breadcrumbs -->
    <nav class="text-[10px] md:text-xs font-medium tracking-[0.2em] text-gray-400 mb-8 md:mb-12">
        <a href="/" class="hover:text-gray-900 transition-colors">HOME</a> /
        <a href="/artists" class="hover:text-gray-900 transition-colors">ARTISTS</a> /
        <span class="text-gray-900">{{ strtoupper($artist->name) }}</span>
    </nav>

    <!-- Artist Header -->
    <div class="flex flex-col md:flex-row gap-8 md:gap-16 border-b border-gray-100 pb-12 mb-12">

        <!-- Profile Image -->
        <div class="w-full md:w-1/3">
            <div class="w-full aspect-square bg-gray-100 overflow-hidden">
                <img src="{{ $artist->profile_image ?? 'https://placehold.co/800x800/e4e4e7/71717a?text=Portrait' }}"
                    alt="{{ $artist->name }}" class="w-full h-full object-cover grayscale">
            </div>
        </div>

        <!-- Biography -->
        <div class="w-full md:w-2/3 flex flex-col justify-center">
            <h1 class="text-3xl md:text-5xl font-light tracking-wide text-gray-900 mb-6">
                {{ $artist->name }}
            </h1>

            <div class="prose prose-sm md:prose-base text-gray-600 leading-relaxed max-w-none">
                <p>
                    {{ $artist->bio ?? 'Biography information is currently being updated by the gallery.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Artist's Artwork Grid -->
    <div class="mb-8">
        <h2 class="text-xl font-light tracking-wide text-gray-900 mb-8">Selected Works</h2>

        @if ($artist->products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
                @foreach ($artist->products as $product)
                    <a href="/products/{{ $product->url_slug }}" class="group block cursor-pointer">
                        <div class="w-full aspect-[3/4] bg-gray-100 mb-5 overflow-hidden relative">
                            <img src="{{ $product->media->first()?->full_path ?? 'https://placehold.co/600x800' }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out"
                                alt="{{ $product->title }}">
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">{{ $product->title }}</h3>
                        <p class="text-sm font-medium text-gray-900 mt-2">£{{ number_format($product->base_price) }}</p>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">No artwork currently available for this artist.</p>
        @endif
    </div>

</div>
