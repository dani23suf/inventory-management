<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'from_warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
                'different:to_warehouse_id',
            ],

            'to_warehouse_id' => [
                'required',
                'integer',
                'exists:warehouses,id',
                'different:from_warehouse_id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Product wajib dipilih.',
            'product_id.exists' => 'Product tidak ditemukan.',

            'from_warehouse_id.required' => 'Gudang asal wajib dipilih.',
            'from_warehouse_id.exists' => 'Gudang asal tidak ditemukan.',

            'to_warehouse_id.required' => 'Gudang tujuan wajib dipilih.',
            'to_warehouse_id.exists' => 'Gudang tujuan tidak ditemukan.',
            'to_warehouse_id.different' => 'Gudang tujuan harus berbeda dari gudang asal.',

            'quantity.required' => 'Quantity wajib diisi.',
            'quantity.min' => 'Quantity minimal 1.',
        ];
    }
}