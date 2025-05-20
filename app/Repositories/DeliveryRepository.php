<?php

namespace App\Repositories;

use App\Models\Delivery;
use App\Repositories\BaseRepository;

class DeliveryRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'book_id',
        'supplier_id',
        'quantity',
        'delivery_date'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Delivery::class;
    }
}
