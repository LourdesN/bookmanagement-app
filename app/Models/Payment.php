<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'amount',
        'payment_date',
    ];
    protected $casts = [
        'payment_date' => 'date',
    ];
    
    public function sale()
{
    return $this->belongsTo(Sale::class);
}

}
