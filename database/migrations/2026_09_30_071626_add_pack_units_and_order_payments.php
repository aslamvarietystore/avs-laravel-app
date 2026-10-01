<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('pieces_per_packet')->default(0);
            $table->unsignedInteger('pieces_per_box')->default(0);
            $table->unsignedInteger('pieces_per_carton')->default(0);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->default('cash');
            $table->decimal('amount_paid', 12, 2)->default(0);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('unit')->default('pcs');
            $table->unsignedInteger('base_quantity')->default(0);
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->string('unit')->default('pcs');
            $table->unsignedInteger('base_quantity')->default(0);
        });

        DB::table('orders')->where('payment_status', 'paid')->update([
            'payment_method' => 'cash',
            'amount_paid' => DB::raw('total'),
        ]);

        DB::table('orders')->where('payment_status', '!=', 'paid')->update([
            'payment_method' => 'credit',
        ]);

        DB::table('order_items')->update(['base_quantity' => DB::raw('quantity')]);
        DB::table('purchase_order_items')->update(['base_quantity' => DB::raw('quantity')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['unit', 'base_quantity']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['unit', 'base_quantity']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'amount_paid']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['pieces_per_packet', 'pieces_per_box', 'pieces_per_carton']);
        });
    }
};
