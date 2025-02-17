<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

     // Creating a database table for the vendors which consist the details of the vendors.
     // Defines a vendor's basic information and links it to a specific user.
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned()->primary();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->string('status');
            $table->string('store_name');
            $table->string('store_address')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
