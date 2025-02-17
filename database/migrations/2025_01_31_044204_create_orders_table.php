<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Stores details of customer orders, including the total price, vendor details, payment status, 
        // commissions, and Stripe transaction references.
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_price',20,4);
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(User::class, 'vendor_user_id');
            $table->string('status');
            $table->string('stripe_session_id')->nullable();
            $table->decimal('online_payment_commission',20,4)->nullable();
            $table->decimal('website_commission',20,4)->nullable();
            $table->decimal('vendor_subtotal',20,4)->nullable();
            $table->string('payment_intent')->nullable();
            $table->timestamps();

        });
        // stores individual products associated with an order. It includes product details, 
        // quantity, price, and selected variations, if any
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('price',20,4);
            $table->integer('quantity');
            $table->json('variation_type_option_ids')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_items');
    }
};
