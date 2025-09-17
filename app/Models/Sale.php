<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Sale extends Model
{
    public $table = 'sales';

    public $fillable = [
        'book_id',
        'customer_id',
        'quantity',
        'unit_price',
        'total',
        'payment_status',
        'amount_paid',
        'balance_due',
    ];

    protected $casts = [
        'payment_status' => 'string'
    ];

    public static array $rules = [
        'book_id' => 'required',
        'customer_id' => 'required',
        'quantity' => 'required',
        'unit_price' => 'required',
        'total' => 'required',
        'payment_status' => 'nullable|string|max:50',
        'balance_due' => 'nullable|numeric',
    ];

   protected static function booted()
    {
        static::saving(function ($sale) {
            Log::info('🔄 Sale saving event triggered', [
                'attributes' => $sale->getAttributes(),
                'total' => $sale->total,
                'amount_paid' => $sale->amount_paid,
            ]);

            try {
                if ($sale->amount_paid >= $sale->total) {
                    $sale->payment_status = 'Paid';
                } elseif ($sale->amount_paid > 0) {
                    $sale->payment_status = 'Partially Paid';
                } else {
                    $sale->payment_status = 'Unpaid';
                }
                $sale->balance_due = number_format(max(0, (float) $sale->total - (float) $sale->amount_paid), 2, '.', '');
                Log::info('✅ Sale saving event processed', [
                    'payment_status' => $sale->payment_status,
                    'balance_due' => $sale->balance_due,
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Error in Sale saving event: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e; // Re-throw to ensure the transaction aborts
            }
        });
    }

    public function book(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Book::class, 'book_id');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
    }
    public function payments()
{
    return $this->hasMany(Payment::class);
}

}
