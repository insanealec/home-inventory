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
        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shopping_list_id');
            $table->string('name');
            $table->integer('quantity')->default(1);
            $table->string('unit')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->integer('priority')->default(1);
            $table->unsignedBigInteger('inventory_item_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('shopping_list_id')->references('id')->on('shopping_lists')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('shopping_categories')->onDelete('set null');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
    }
};
