<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // allow all users, or add your auth check
    }

    public function rules(): array
    {
        return [
            'book_id'      => 'required|exists:books,id',
            'customer_id'  => 'required|exists:customers,id',
            'quantity'     => 'required|integer|min:1',
            'unit_price'   => 'required|numeric|min:0',
            'total'        => 'required|numeric|min:0',
            'amount_paid'  => 'nullable|numeric|min:0',
            'balance_due'  => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => 'Please select a book.',
            'book_id.exists'   => 'The selected book does not exist.',
            'customer_id.required' => 'Please select a customer.',
            'customer_id.exists'   => 'The selected customer does not exist.',
            'quantity.required' => 'Quantity is required.',
            'quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
