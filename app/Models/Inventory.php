<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = ['book_id', 'quantity', 'location'];

    protected $casts = [
        'location' => 'string',
        'quantity' => 'integer',
    ];

    public static array $rules = [
        'book_id'  => 'required|integer',
        'quantity' => 'required|integer|min:0',
        'location' => 'required|string|max:255',
    ];

    protected static function booted()
    {
        static::saving(function ($inventory) {
            Log::info('🔄 Inventory saving event triggered', $inventory->getAttributes());

            if ($inventory->quantity < 0) {
                Log::error('❌ Inventory quantity cannot be negative', $inventory->getAttributes());
                throw new \Exception('Inventory quantity cannot be negative.');
            }
        });

        static::updated(function ($inventory) {
            Log::info('✅ Inventory updated event triggered', $inventory->getAttributes());
        });

        static::created(function ($inventory) {
            Log::info('✅ Inventory created event triggered', $inventory->getAttributes());
        });

        static::deleted(function ($inventory) {
            Log::info('🗑️ Inventory deleted event triggered', $inventory->getAttributes());
        });
    }

    // Relationships
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
    