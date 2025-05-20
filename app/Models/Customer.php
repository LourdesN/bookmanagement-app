<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $table = 'customers';

    public $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'address'
    ];

    protected $casts = [
        'first_name' => 'string',
        'last_name' => 'string',
        'email' => 'string',
        'address' => 'string'
    ];

    public static array $rules = [
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'email' => 'required|string|max:191',
        'phone_number' => 'required',
        'address' => 'nullable|string|max:100'
    ];

    public function sales(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Sale::class, 'customer_id');
    }
}
