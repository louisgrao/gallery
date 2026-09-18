<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Artist;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            // Eager-load only the most recent active product and its media for the fallback image
            'artists' => Artist::with([
                'products' => function ($query) {
                    $query->where('product_status', true)->latest()->with('media');
                },
            ])
                ->orderBy('name', 'asc')
                ->paginate(12),
        ];
    }
}; ?>

<div class="max-w-[1200px] mx-auto px-5 md:px-8 py-8 md:py-12">

    <!-- Page Header -->
    <div class="border-b border-gray-100 pb-6 mb-12">
        <h1 class="text-2xl md:text-4xl font-light tracking-wide text-gray-900 mb-2">Artists</h1>
        <p class="text-sm text-gray-500">Discover the creators behind our collection.</p>
    </div>

    <!-- 2-Column Artist Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16">
        @foreach ($artists as $artist)
            @php
                // Image Fallback Logic
                $displayImage =
                    $artist->profile_image ??
                    ($artist->products->first()?->media->first()?->full_path ??
                        'https://placehold.co/800x800/e4e4e7/71717a?text=' . urlencode($artist->name));
            @endphp

            <a href="/artists/{{ $artist->url_slug }}"
                class="group flex flex-col sm:flex-row gap-6 items-start cursor-pointer">

                <!-- Artist Image -->
                <div class="w-full sm:w-2/5 aspect-square bg-gray-100 overflow-hidden flex-shrink-0">
                    <img src="{{ $displayImage }}" alt="{{ $artist->name }}"
                        class="w-full h-full object-cover grayscale group-hover:scale-105 transition-transform duration-700 ease-out">
                </div>

                <!-- Artist Info -->
                <div class="w-full sm:w-3/5 flex flex-col pt-2">
                    <h2
                        class="text-xl font-light tracking-wide text-gray-900 mb-3 group-hover:text-gray-500 transition-colors">
                        {{ $artist->name }}
                    </h2>

                    <!-- Bio with CSS line clamping -->
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-4 mb-4">
                        {{ $artist->bio ?? 'Biography information is currently being updated by the gallery.' }}
                    </p>

                    <span
                        class="text-[10px] font-medium tracking-[0.2em] text-gray-900 uppercase underline decoration-transparent group-hover:decoration-gray-300 underline-offset-4 transition-all">
                        View Profile
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Pagination Controls -->
    <div class="mt-16 pt-8 border-t border-gray-100 flex justify-center">
        {{ $artists->links() }}
    </div>

</div>
