<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'balance_due'
   
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
        'payment_status' => 'required|string|max:65535',
        'balance_due' => 'nullable|numeric',
    ];

    public function getBalanceDueAttribute()
{
    return $this->total - $this->amount_paid;
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
