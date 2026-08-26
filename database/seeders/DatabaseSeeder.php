<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockLedger;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        $products = Product::create([
            'name' => 'Laptop ASUS VivoBook',
            'sku' => 'LAP-ASUS-001',
            'unit' => 'pcs',
        ]);

        $mouse = Product::create([
            'name' => 'Wireless Mouse Logitech',
            'sku' => 'MOU-LOG-001',
            'unit' => 'pcs',
        ]);

        $keyboard = Product::create([
            'name' => 'Mechanical Keyboard',
            'sku' => 'KEY-MEC-001',
            'unit' => 'pcs',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Warehouses
        |--------------------------------------------------------------------------
        */

        $warehouseA = Warehouse::create([
            'name' => 'Gudang Utama',
            'location' => 'Surabaya',
        ]);

        $warehouseB = Warehouse::create([
            'name' => 'Gudang Cabang',
            'location' => 'Sidoarjo',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Stock Movements
        |--------------------------------------------------------------------------
        */

        StockLedger::create([
            'product_id' => $products->id,
            'warehouse_id' => $warehouseA->id,
            'type' => 'in',
            'quantity' => 20,
            'reference_id' => null,
            'note' => 'Stok awal laptop',
        ]);

        StockLedger::create([
            'product_id' => $mouse->id,
            'warehouse_id' => $warehouseA->id,
            'type' => 'in',
            'quantity' => 50,
            'reference_id' => null,
            'note' => 'Stok awal mouse',
        ]);

        StockLedger::create([
            'product_id' => $keyboard->id,
            'warehouse_id' => $warehouseA->id,
            'type' => 'in',
            'quantity' => 30,
            'reference_id' => null,
            'note' => 'Stok awal keyboard',
        ]);

        StockLedger::create([
            'product_id' => $products->id,
            'warehouse_id' => $warehouseB->id,
            'type' => 'in',
            'quantity' => 10,
            'reference_id' => null,
            'note' => 'Stok awal laptop cabang',
        ]);

        StockLedger::create([
            'product_id' => $mouse->id,
            'warehouse_id' => $warehouseA->id,
            'type' => 'out',
            'quantity' => 5,
            'reference_id' => 1001,
            'note' => 'Penjualan mouse',
        ]);

        StockLedger::create([
            'product_id' => $keyboard->id,
            'warehouse_id' => $warehouseA->id,
            'type' => 'out',
            'quantity' => 3,
            'reference_id' => 1002,
            'note' => 'Penjualan keyboard',
        ]);

        StockLedger::create([
            'product_id' => $products->id,
            'warehouse_id' => $warehouseA->id,
            'type' => 'out',
            'quantity' => 2,
            'reference_id' => 1003,
            'note' => 'Penjualan laptop',
        ]);

        StockLedger::create([
            'product_id' => $mouse->id,
            'warehouse_id' => $warehouseA->id,
            'type' => 'transfer',
            'quantity' => 10,
            'reference_id' => 2001,
            'note' => 'Transfer ke Gudang Cabang',
        ]);

        StockLedger::create([
            'product_id' => $keyboard->id,
            'warehouse_id' => $warehouseA->id,
            'type' => 'transfer',
            'quantity' => 5,
            'reference_id' => 2002,
            'note' => 'Transfer ke Gudang Cabang',
        ]);

        StockLedger::create([
            'product_id' => $products->id,
            'warehouse_id' => $warehouseB->id,
            'type' => 'out',
            'quantity' => 1,
            'reference_id' => 1004,
            'note' => 'Penjualan laptop cabang',
        ]);
    }
}