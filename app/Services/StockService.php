<?php

namespace App\Services;

use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function stockIn(array $data): StockLedger
    {
        return DB::transaction(function () use ($data) {
            return StockLedger::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'type' => 'in',
                'quantity' => $data['quantity'],
                'reference_id' => $data['reference_id'] ?? null,
                'note' => $data['note'] ?? null,
            ]);
        });
    }

    public function stockOut(array $data): StockLedger
    {
        return DB::transaction(function () use ($data) {
            $currentStock = $this->getCurrentStock(
                $data['product_id'],
                $data['warehouse_id']
            );

            if ($currentStock < $data['quantity']) {
                throw new RuntimeException(
                    "Insufficient stock. Current stock: {$currentStock}."
                );
            }

            return StockLedger::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'type' => 'out',
                'quantity' => $data['quantity'],
                'reference_id' => $data['reference_id'] ?? null,
                'note' => $data['note'] ?? null,
            ]);
        });
    }

    public function getCurrentStock(
        int $productId,
        int $warehouseId
    ): int {
        $stockIn = StockLedger::query()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('type', 'in')
            ->sum('quantity');

        $stockOut = StockLedger::query()
            ->where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->whereIn('type', ['out', 'transfer'])
            ->sum('quantity');

        return (int) ($stockIn - $stockOut);
    }

    public function transfer(array $data): array
    {
        return DB::transaction(function () use ($data) {

            /*
             * Ambil seluruh ledger product pada
             * gudang asal dan LOCK menggunakan
             * SELECT ... FOR UPDATE.
             */
            $ledgers = StockLedger::query()
                ->where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['from_warehouse_id'])
                ->lockForUpdate()
                ->get();

            /*
             * Hitung stock saat ini.
             */
            $stockIn = $ledgers
                ->where('type', 'in')
                ->sum('quantity');

            $stockOut = $ledgers
                ->where('type', 'out')
                ->sum('quantity');

            $currentStock = $stockIn - $stockOut;

            /*
             * Validasi stock.
             */
            if ($currentStock < $data['quantity']) {
                throw new RuntimeException(
                    "Stok tidak mencukupi. Stok tersedia: {$currentStock}."
                );
            }

            /*
             * Reference ID untuk menghubungkan
             * OUT dan IN sebagai satu transfer.
             */
            $referenceId = time();

            /*
             * Kurangi stock dari gudang asal.
             */
            $out = StockLedger::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['from_warehouse_id'],
                'type' => 'out',
                'quantity' => $data['quantity'],
                'reference_id' => $referenceId,
                'note' => $data['note'] ?? 'Stock transfer',
            ]);

            /*
             * Tambahkan stock ke gudang tujuan.
             */
            $in = StockLedger::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['to_warehouse_id'],
                'type' => 'in',
                'quantity' => $data['quantity'],
                'reference_id' => $referenceId,
                'note' => $data['note'] ?? 'Stock transfer',
            ]);

            return [
                'reference_id' => $referenceId,
                'out' => $out,
                'in' => $in,
            ];
        });
    }
}