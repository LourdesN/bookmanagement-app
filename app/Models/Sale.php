<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Sale extends Model
{
    protected $table = 'sales';

    protected $fillable = [
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
        'payment_status' => 'string',
        'amount_paid'    => 'decimal:2',
        'balance_due'    => 'decimal:2',
        'total'          => 'decimal:2',
        'unit_price'     => 'decimal:2',
    ];

    public static array $rules = [
        'book_id'      => 'required|integer',
        'customer_id'  => 'required|integer',
        'quantity'     => 'required|integer|min:1',
        'unit_price'   => 'required|numeric|min:0',
        'total'        => 'required|numeric|min:0',
        'payment_status'=> 'nullable|string|max:50',
        'balance_due'  => 'nullable|numeric',
    ];

    protected static function booted()
    {
        static::creating(function ($sale) {
            Log::info('🔄 Sale creating event triggered', $sale->getAttributes());
        });

        static::created(function ($sale) {
            Log::info('✅ Sale created event triggered', [
                'id' => $sale->id,
                'attributes' => $sale->getAttributes(),
            ]);
        });

        static::saving(function ($sale) {
            Log::info('💾 Sale saving event triggered', [
                'attributes' => $sale->getAttributes(),
            ]);
        });

        static::updated(function ($sale) {
            Log::info('🔄 Sale updated event triggered', [
                'id' => $sale->id,
                'attributes' => $sale->getAttributes(),
            ]);
        });

        static::deleted(function ($sale) {
            Log::info('🗑️ Sale deleted event triggered', [
                'id' => $sale->id,
                'attributes' => $sale->getAttributes(),
            ]);
        });
    }

    // Relationships
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
