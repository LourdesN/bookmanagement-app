<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
    protected static function booted()
    {
        static::saving(function ($inventory) {
            Log::info('🔄 Inventories saving event triggered', [
                'book_id' => $inventory->book_id,
                'quantity' => $inventory->quantity,
                'attributes' => $inventory->getAttributes(),
            ]);

            if ($inventory->quantity < 0) {
                Log::warning('❌ Inventory quantity cannot be negative', [
                    'book_id' => $inventory->book_id,
                    'quantity' => $inventory->quantity,
                ]);
                throw new \Exception('Inventory quantity cannot be negative.');
            }

            Log::info('✅ Inventories saving event processed', [
                'book_id' => $inventory->book_id,
                'quantity' => $inventory->quantity,
            ]);
        });
    }
}
