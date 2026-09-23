<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Data Layer: Core Items
        Schema::create('catalog_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url_slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2)->default(0);
            $table->boolean('item_status')->default(true);
            $table->timestamps();
        });

        // 2. Data Layer: Item Variants (Physical/Digital specs)
        Schema::create('catalog_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('quantity')->default(-1);
            $table->json('attributes')->nullable();
            $table->timestamps();
        });

        // 3. Data Layer: Media 
        Schema::create('catalog_media', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('full_path');
            $table->timestamps();
        });

        Schema::create('catalog_item_catalog_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_media_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->default(0);
        });

        // 4. Data Layer: Universal Taxonomy (Accessories)
        Schema::create('catalog_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->default(0);
            $table->string('title');
            $table->string('url_slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });

        Schema::create('catalog_accessory_catalog_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('catalog_accessory_id')->constrained()->cascadeOnDelete();
        });

        // 5. Presentation Layer: Page Engine
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('page_type')->default('catalog_list'); 
            $table->json('query_rules')->nullable(); 
            $table->json('display_settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('catalog_accessory_catalog_item');
        Schema::dropIfExists('catalog_accessories');
        Schema::dropIfExists('catalog_item_catalog_media');
        Schema::dropIfExists('catalog_media');
        Schema::dropIfExists('catalog_variants');
        Schema::dropIfExists('catalog_items');
    }
};