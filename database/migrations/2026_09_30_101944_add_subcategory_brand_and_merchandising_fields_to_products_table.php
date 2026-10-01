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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('subcategory_id')->nullable()->after('category_id')->constrained('categories')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->after('subcategory_id')->constrained('brands')->nullOnDelete();
            $table->unsignedInteger('minimum_order_quantity')->default(1)->after('retail_price');
            $table->boolean('is_new_arrival')->default(false)->after('is_featured');
            $table->boolean('is_sale')->default(false)->after('is_new_arrival');

            $table->index(['subcategory_id', 'status']);
            $table->index(['brand_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['subcategory_id', 'status']);
            $table->dropIndex(['brand_id', 'status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subcategory_id');
            $table->dropConstrainedForeignId('brand_id');
            $table->dropColumn(['minimum_order_quantity', 'is_new_arrival', 'is_sale']);
        });
    }
};
