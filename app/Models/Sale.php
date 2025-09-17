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
        static::creating(function ($sale) {
            Log::info('🔄 Sale creating event triggered', [
                'attributes' => $sale->getAttributes(),
            ]);
        });

        static::created(function ($sale) {
            Log::info('🔄 Sale created event triggered', [
                'id' => $sale->id,
                'attributes' => $sale->getAttributes(),
            ]);
        });

        static::saving(function ($sale) {
            Log::info('🔄 Sale saving event triggered', [
                'attributes' => $sale->getAttributes(),
                'total' => $sale->total,
                'amount_paid' => $sale->amount_paid,
            ]);
            Log::info('✅ Sale saving event processed', [
                'payment_status' => $sale->payment_status,
                'balance_due' => $sale->balance_due,
            ]);
        });

        static::updated(function ($sale) {
            Log::info('🔄 Sale updated event triggered', [
                'id' => $sale->id,
                'attributes' => $sale->getAttributes(),
            ]);
        });

        static::deleted(function ($sale) {
            Log::info('🔄 Sale deleted event triggered', [
                'id' => $sale->id,
                'attributes' => $sale->getAttributes(),
            ]);
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
