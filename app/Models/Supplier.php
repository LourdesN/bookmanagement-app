<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    public $table = 'suppliers';

    public $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email'
    ];

    protected $casts = [
        'first_name' => 'string',
        'last_name' => 'string',
        'email' => 'string'
    ];

    public static array $rules = [
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'phone_number' => 'required',
        'email' => 'required|string|max:191'
    ];

    public function deliveries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Delivery::class, 'supplier_id');
    }
}
