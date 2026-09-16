<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('parent_id')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('url_slug')->unique();
            $table->decimal('base_price', 10, 2);
            $table->unsignedBigInteger('sales_count')->default(0);
            $table->boolean('product_status')->default(true);
            $table->boolean('subscription')->default(false);
            $table->integer('subscription_period')->default(0);
            $table->string('subscription_period_type')->default('day');
            $table->timestamps();
        });

        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('caption')->nullable();
            $table->string('full_path');
            $table->timestamps();
        });

        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('sku')->unique();
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(-1);
            $table->decimal('weight', 10, 2)->default(0.00);
            $table->json('attributes')->nullable();
            $table->timestamps();
        });

        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['text', 'datetime']);
            $table->decimal('price_modifier', 10, 2)->default(0.00);
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });

        Schema::create('product_media_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_media_id')->constrained('product_media')->cascadeOnDelete();
            $table->integer('position')->default(0);
        });

        Schema::create('product_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->unique(['product_id', 'file_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_downloads');
        Schema::dropIfExists('product_media_product');
        Schema::dropIfExists('product_addons');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        
    }
};
