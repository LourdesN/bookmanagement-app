<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    public $table = 'inventories';

    public $fillable = [
        'book_id',
        'quantity',
        'location'
    ];

    protected $casts = [
        'location' => 'string',
    ];

    public static array $rules = [
        'book_id' => 'required',
        'quantity' => 'required',
        'location' => 'required|string|max:255',
    ];

    public function book(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Book::class, 'book_id');
    }
}
