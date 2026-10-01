<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('suppliers')->cascadeOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('method')->default('cash');
            $table->date('paid_on');
            $table->string('status')->default('paid');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
