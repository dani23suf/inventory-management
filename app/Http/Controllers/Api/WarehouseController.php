<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Warehouses retrieved successfully.',
            'data' => Warehouse::query()
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'location',
                ]),
        ]);
    }
}