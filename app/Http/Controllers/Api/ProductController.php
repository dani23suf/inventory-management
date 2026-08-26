<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\StockLedger;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->orderBy('id')
            ->paginate(15);

        $productIds = $products->getCollection()
            ->pluck('id');

        $warehouses = Warehouse::query()
            ->orderBy('id')
            ->get([
                'id',
                'name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Calculate current stock
        |--------------------------------------------------------------------------
        |
        | IN       = +
        | OUT      = -
        | TRANSFER = -
        |
        */

        $stocks = DB::table('stock_ledgers')
            ->select(
                'product_id',
                'warehouse_id',
                DB::raw("
                    SUM(
                        CASE
                            WHEN type = 'in' THEN quantity
                            WHEN type IN ('out', 'transfer') THEN -quantity
                            ELSE 0
                        END
                    ) as quantity
                ")
            )
            ->whereIn('product_id', $productIds)
            ->groupBy(
                'product_id',
                'warehouse_id'
            )
            ->get()
            ->groupBy('product_id');

        /*
        |--------------------------------------------------------------------------
        | Attach stock to each product
        |--------------------------------------------------------------------------
        */

        $products->getCollection()->transform(function ($product) use (
            $stocks,
            $warehouses
        ) {
            $productStocks = $stocks->get($product->id, collect())
                ->keyBy('warehouse_id');

            $currentStocks = $warehouses->map(function ($warehouse) use (
                $productStocks
            ) {
                $stock = $productStocks->get($warehouse->id);

                return (object) [
                    'warehouse_id' => $warehouse->id,
                    'warehouse_name' => $warehouse->name,
                    'quantity' => $stock
                        ? (int) $stock->quantity
                        : 0,
                ];
            });

            $product->current_stocks = $currentStocks;

            return $product;
        });

        return ProductResource::collection($products)
            ->additional([
                'success' => true,
                'message' => 'Products retrieved successfully.',
            ]);
    }

    public function show(Product $product): JsonResponse
    {
        $warehouses = Warehouse::query()
            ->get();

        $stockByWarehouse = $warehouses->map(function ($warehouse) use ($product) {

            $stockIn = StockLedger::query()
                ->where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->where('type', 'in')
                ->sum('quantity');

            $stockOut = StockLedger::query()
                ->where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->where('type', 'out')
                ->sum('quantity');

            return [
                'warehouse_id' => $warehouse->id,
                'warehouse_name' => $warehouse->name,
                'location' => $warehouse->location,
                'stock' => $stockIn - $stockOut,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully.',
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit,
                'stock_by_warehouse' => $stockByWarehouse,
            ],
        ]);
    }
}