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
        'isbn',
        'description',
        'reorder_level',

    ];

    protected $casts = [
        'title' => 'string',
        'print_date' => 'date',
        'description' => 'string'
    ];

    public static array $rules = [
        'title' => 'required|string|max:500',
        'print_date' => 'required',
        'unit_cost' => 'required',
        'isbn' => 'nullable',
        'description' => 'nullable|string|max:65535',
        'reorder_level' => 'required|integer|min:0',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function sales(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Sale::class, 'book_id');
    }

    public function inventories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Inventory::class, 'book_id');
    }

    public function deliveries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Delivery::class, 'book_id');
    }
    public function getPrintDateAttribute($value)
{
    return \Carbon\Carbon::parse($value)->format('Y-m-d');
}

}
