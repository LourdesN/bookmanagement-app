<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public $table = 'books';

    public $fillable = [
        'title',
        'print_date',
        'unit_cost',
        'isbn'
    ];

    protected $casts = [
        'title' => 'string',
        'print_date' => 'date'
    ];

    public static array $rules = [
        'title' => 'required|string|max:500',
        'print_date' => 'required',
        'unit_cost' => 'required',
        'isbn' => 'nullable'
    ];

    public function deliveries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Delivery::class, 'book_id');
    }

    public function sales(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Sale::class, 'book_id');
    }

    public function inventories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Inventory::class, 'book_id');
    }
}
