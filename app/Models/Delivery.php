<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    public $table = 'deliveries';

    public $fillable = [
        'book_id',
        'supplier_id',
        'quantity',
        'location',
        'delivery_date'
    ];

    protected $casts = [
        'delivery_date' => 'date'
    ];

    public static array $rules = [
        'book_id' => 'required',
        'supplier_id' => 'required',
        'quantity' => 'required',
        'location',
        'delivery_date' => 'required'
    ];

    public function book(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Book::class, 'book_id');
    }

    public function supplier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id');
    }
    public function getDeliveryDateAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }
    
}
