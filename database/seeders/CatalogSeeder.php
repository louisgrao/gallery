<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatalogAccessory;
use App\Models\CatalogItem;
use App\Models\CatalogMedia;
use App\Models\CatalogVariant;
use App\Models\Page;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Accessory Groups (Taxonomy Parents)
        $artistGroup = CatalogAccessory::create(['title' => 'Artists', 'url_slug' => 'artists']);
        $typeGroup = CatalogAccessory::create(['title' => 'Type']);
        $mediumGroup = CatalogAccessory::create(['title' => 'Medium']);
        $locationGroup = CatalogAccessory::create(['title' => 'Location']);

        // 2. Create Specific Accessories (Taxonomy Terms)
        $marcus = CatalogAccessory::create(['title' => 'Marcus Chen', 'parent_id' => $artistGroup->id, 'url_slug' => 'marcus-chen']);
        $sarah = CatalogAccessory::create(['title' => 'Sarah Jenkins', 'parent_id' => $artistGroup->id, 'url_slug' => 'sarah-jenkins']);

        $painting = CatalogAccessory::create(['title' => 'Painting', 'parent_id' => $typeGroup->id]);
        $sculpture = CatalogAccessory::create(['title' => 'Sculpture', 'parent_id' => $typeGroup->id]);
        $oil = CatalogAccessory::create(['title' => 'Oil', 'parent_id' => $mediumGroup->id]);
        $inGallery = CatalogAccessory::create(['title' => 'In Gallery', 'parent_id' => $locationGroup->id]);

        // 3. Create a Physical Catalog Item
        $item1 = CatalogItem::create([
            'title' => 'The Silent Harbor',
            'url_slug' => 'the-silent-harbor',
            'base_price' => 850,
            'item_status' => true,
        ]);
        $item1->accessories()->attach([$marcus->id, $painting->id, $oil->id, $inGallery->id]);

        CatalogVariant::create([
            'catalog_item_id' => $item1->id,
            'title' => 'Original Painting',
            'sku' => 'ART-0001',
            'price' => 850,
            'quantity' => 1,
            'attributes' => ['width' => 100, 'height' => 80, 'depth' => 1.5, 'volume' => 12000, 'framing' => 'Unframed']
        ]);

        // 4. Create a Digital Download Item
        $item2 = CatalogItem::create([
            'title' => 'The Silent Harbor (Digital Print)',
            'url_slug' => 'the-silent-harbor-digital',
            'base_price' => 25,
            'item_status' => true,
        ]);
        $item2->accessories()->attach([$marcus->id]);

        CatalogVariant::create([
            'catalog_item_id' => $item2->id,
            'title' => 'High-Res PDF Download',
            'sku' => 'DIG-0001',
            'price' => 25,
            'quantity' => -1, // Unlimited stock
            'attributes' => ['format' => 'PDF', 'resolution' => '300dpi', 'download_path' => 'secure/files/harbor-digital.zip']
        ]);

        // 5. Build the Page Engine Configuration
        Page::create([
            'title' => 'Main Artwork Catalog',
            'slug' => 'catalog',
            'page_type' => 'catalog_list',
            'query_rules' => [
                'must_have_accessories' => [] 
            ],
            'display_settings' => [
                'layout_style' => 'grid_4_col',
                'sidebar_filters' => [$typeGroup->id, $mediumGroup->id, $locationGroup->id],
                'show_accessories_on_card' => [$artistGroup->id, $mediumGroup->id]
            ],
            'is_active' => true,
        ]);
    }
}