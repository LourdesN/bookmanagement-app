<?php

namespace App\Repositories;

use App\Models\Sale;
use App\Repositories\BaseRepository;

class SaleRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'book_id',
        'customer_id',
        'quantity',
        'unit_price',
        'total',
        'payment_status'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Sale::class;
    }
}
