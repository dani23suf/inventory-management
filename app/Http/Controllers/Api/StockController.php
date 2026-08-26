<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockInRequest;
use App\Http\Requests\StockOutRequest;
use App\Http\Requests\StockTransferRequest;
use App\Http\Resources\StockLedgerResource;
use App\Models\StockLedger;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockController extends Controller
{
    public function __construct(
        protected StockService $stockService
    ) {
    }

    public function stockIn(
        StockInRequest $request
    ): JsonResponse {
        $ledger = $this->stockService->stockIn(
            $request->validated()
        );

        $ledger->load([
            'product',
            'warehouse',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock added successfully.',
            'data' => new StockLedgerResource($ledger),
        ], 201);
    }

    public function stockOut(
        StockOutRequest $request
    ): JsonResponse {
        try {
            $ledger = $this->stockService->stockOut(
                $request->validated()
            );

            $ledger->load([
                'product',
                'warehouse',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock removed successfully.',
                'data' => new StockLedgerResource($ledger),
            ], 201);

        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ], 422);
        }
    }

    public function transfer(
        StockTransferRequest $request
    ): JsonResponse {
        try {

            $result = $this->stockService->transfer(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock berhasil ditransfer.',
                'data' => [
                    'reference_id' => $result['reference_id'],
                    'out' => new StockLedgerResource(
                        $result['out']
                    ),
                    'in' => new StockLedgerResource(
                        $result['in']
                    ),
                ],
            ], 201);

        } catch (RuntimeException $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'errors' => null,
            ], 422);
        }
    }

   public function report(
    Request $request
): AnonymousResourceCollection {

    $validated = $request->validate([
        'warehouse_id' => [
            'nullable',
            'integer',
            'exists:warehouses,id',
        ],

        'product_id' => [
            'nullable',
            'integer',
            'exists:products,id',
        ],

        'type' => [
            'nullable',
            'string',
            'in:in,out,transfer',
        ],
    ]);

    $query = StockLedger::query()
        ->with([
            'product:id,name,sku,unit',
            'warehouse:id,name,location',
        ])
        ->latest();

    if (!empty($validated['warehouse_id'])) {
        $query->where(
            'warehouse_id',
            $validated['warehouse_id']
        );
    }

    if (!empty($validated['product_id'])) {
        $query->where(
            'product_id',
            $validated['product_id']
        );
    }

    if (!empty($validated['type'])) {
        $query->where(
            'type',
            $validated['type']
        );
    }

    $movements = $query->paginate(20);

    return StockLedgerResource::collection($movements)
        ->additional([
            'success' => true,
            'message' => 'Stock movement berhasil diambil.',
        ]);
}

    
}