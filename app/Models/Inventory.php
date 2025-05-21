<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public $table = 'inventories';

    public $fillable = [
        'book_id',
        'quantity',
        'location',
        'delivery_date'
    ];

    protected $casts = [
        'location' => 'string',
        'delivery_date' => 'date'
    ];

    public static array $rules = [
        'book_id' => 'required',
        'quantity' => 'required',
        'location' => 'required|string|max:255',
        'delivery_date' => 'required'
    ];

    public function book(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Book::class, 'book_id');
    }
    public function getDeliveryDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }
}
