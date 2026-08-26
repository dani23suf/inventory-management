<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained('warehouses')
                ->cascadeOnDelete();

            $table->enum('type', [
                'in',
                'out',
                'transfer',
            ]);

            $table->unsignedInteger('quantity');

            $table->unsignedBigInteger('reference_id')
                ->nullable();

            $table->text('note')
                ->nullable();

            $table->timestamps();

            $table->index([
                'product_id',
                'warehouse_id',
            ]);

            $table->index('reference_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledgers');
    }
};