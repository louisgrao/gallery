<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\ProductVariant;
use App\Models\Artist;


class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Artists
        $artistElena = Artist::create(['name' => 'Elena Rostova', 'url_slug' => 'elena-rostova', 'bio' => 'Contemporary painter focusing on nature.']);
        $artistMarcus = Artist::create(['name' => 'Marcus Chen', 'url_slug' => 'marcus-chen', 'bio' => 'Urban photographer and mixed media artist.']);
        $artistSarah = Artist::create(['name' => 'Sarah Jenkins', 'url_slug' => 'sarah-jenkins', 'bio' => 'Abstract expressionist based in London.']);
        $artistDavid = Artist::create(['name' => 'David Smith', 'url_slug' => 'david-smith', 'bio' => 'Sculptor and industrial artist.']);

        // 2. Create Hierarchical Categories
        $typeParent = Category::create(['title' => 'Type']);
        $mediumParent = Category::create(['title' => 'Medium']);
        $locationParent = Category::create(['title' => 'Location']);

        // Types
        $catPaintings = Category::create(['title' => 'Paintings', 'parent_id' => $typeParent->id]);
        $catPhotography = Category::create(['title' => 'Photography', 'parent_id' => $typeParent->id]);
        $catSculpture = Category::create(['title' => 'Sculpture', 'parent_id' => $typeParent->id]);
        $catMixedMedia = Category::create(['title' => 'Mixed Media', 'parent_id' => $typeParent->id]);

        // Mediums
        $catOil = Category::create(['title' => 'Oil', 'parent_id' => $mediumParent->id]);
        $catAcrylic = Category::create(['title' => 'Acrylic', 'parent_id' => $mediumParent->id]);
        $catCharcoal = Category::create(['title' => 'Charcoal on Paper', 'parent_id' => $mediumParent->id]);
        $catCeramic = Category::create(['title' => 'Ceramic', 'parent_id' => $mediumParent->id]);

        // Locations
        $catGallery = Category::create(['title' => 'In Gallery', 'parent_id' => $locationParent->id]);
        $catWeb = Category::create(['title' => 'Web Only', 'parent_id' => $locationParent->id]);

        // 3. Define the Artwork with JSON Attributes and Category Arrays
        $artworks = [
            [
                'title' => 'The Silent Harbor', 'base_price' => 850, 'url_slug' => 'the-silent-harbor', 
                'category_ids' => [$catPaintings->id, $catOil->id, $catGallery->id], 'artist_ids' => [$artistElena->id],
                'attributes' => ['width' => 100, 'height' => 80, 'depth' => 1.5, 'volume' => 12000, 'framing' => 'Unframed']
            ],
            [
                'title' => 'Midnight Study', 'base_price' => 1200, 'url_slug' => 'midnight-study', 
                'category_ids' => [$catPhotography->id, $catGallery->id], 'artist_ids' => [$artistMarcus->id],
                'attributes' => ['width' => 60, 'height' => 90, 'depth' => 2.0, 'volume' => 10800, 'framing' => 'Framed (Black Wood)']
            ],
            [
                'title' => 'Abstract Composition IV', 'base_price' => 2400, 'url_slug' => 'abstract-composition-iv', 
                'category_ids' => [$catPaintings->id, $catAcrylic->id, $catWeb->id], 'artist_ids' => [$artistSarah->id],
                'attributes' => ['width' => 120, 'height' => 120, 'depth' => 3.5, 'volume' => 50400, 'framing' => 'Unframed (Deep Canvas)']
            ],
            [
                'title' => 'Urban Decay', 'base_price' => 650, 'url_slug' => 'urban-decay', 
                'category_ids' => [$catSculpture->id, $catCeramic->id, $catGallery->id], 'artist_ids' => [$artistDavid->id],
                'attributes' => ['width' => 30, 'height' => 45, 'depth' => 30, 'volume' => 40500]
            ],
            [
                'title' => 'Spring Awakening', 'base_price' => 950, 'url_slug' => 'spring-awakening', 
                'category_ids' => [$catPaintings->id, $catOil->id, $catGallery->id], 'artist_ids' => [$artistElena->id],
                'attributes' => ['width' => 80, 'height' => 80, 'depth' => 1.5, 'volume' => 9600, 'framing' => 'Framed (Oak)']
            ],
            [
                'title' => 'The Collaboration', 'base_price' => 3200, 'url_slug' => 'the-collaboration', 
                'category_ids' => [$catMixedMedia->id, $catCharcoal->id, $catWeb->id], 'artist_ids' => [$artistElena->id, $artistMarcus->id],
                'attributes' => ['width' => 150, 'height' => 100, 'depth' => 2.5, 'volume' => 37500, 'framing' => 'Framed (Float Mount)']
            ],
        ];

        // 4. Seed Database
        foreach ($artworks as $index => $art) {
            $product = Product::create([
                'title' => $art['title'],
                'base_price' => $art['base_price'],
                'url_slug' => $art['url_slug'],
                'product_status' => true,
            ]);

            // Attach multiple Categories and Artists
            $product->categories()->attach($art['category_ids']);
            $product->artists()->attach($art['artist_ids']);

            // Create Variant with JSON Attributes
            ProductVariant::create([
                'product_id' => $product->id,
                'title' => 'Original Artwork',
                'sku' => 'ART-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'price' => $art['base_price'],
                'quantity' => 1,
                'attributes' => $art['attributes']
            ]);

            // Create Media
            $media = ProductMedia::create([
                'title' => $art['title'] . ' Image',
                'full_path' => 'https://placehold.co/600x800/f8f9fa/a1a1aa?text=Artwork+' . ($index + 1),
            ]);

            $product->media()->attach($media->id, ['position' => 1]);
        }
    }
}