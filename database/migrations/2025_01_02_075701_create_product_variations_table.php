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
        // Stores different types of variations for products.
        Schema::create('variation_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products') // Enforces a foreign key constraint linking to 'products'.
                  ->index() // Adds an index to improve query performance when searching by 'product_id'.
                  ->cascadeOnDelete(); // Automatically deletes variations when the related product is deleted.
            $table->string('name'); // Creates a 'name' column to store the variation type name (e.g., "Color", "Size").
            $table->string('type'); // Creates a 'type' column to define the type of variation (e.g., "Select", "Radio", "Image").
        });
        // Stores specific options for each variation type.
        Schema::create('variation_type_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_type_id') 
                  ->constrained('variation_types') // Enforces a foreign key constraint linking to 'variation_types'.
                  ->index() // Adds an index to speed up queries involving 'variation_type_id'.
                  ->cascadeOnDelete(); // Automatically deletes options if the associated variation type is deleted.
            $table->string('name'); // Creates a 'name' column to store the option's name (e.g., "Red", "Blue", "Small", "Large").
        });
        // Stores different variations of a product, such as size, color, and price.
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->index()
                  ->cascadeOnDelete();

            // Creates a 'variation_type_option_ids' column to store selected variation options in JSON format.
            // Example: {"color": "red", "size": "large"}.
            $table->json('variation_type_option_ids');

            // Adds a 'quantity' column to store the number of items available for this variation.
            // Allows NULL values if quantity is not specified.
            $table->integer('quantity')->nullable();

            // Adds a 'price' column to store the price for this specific variation.
            // Uses 20 digits in total, with 4 digits reserved for decimal precision.
            // Allows NULL values if the price is not specified.
            $table->decimal('price',20,4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
        Schema::dropIfExists('variation_type_options');
        Schema::dropIfExists('variation_types');
    }
};
