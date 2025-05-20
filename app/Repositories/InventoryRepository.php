<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Repositories\BaseRepository;

class InventoryRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'book_id',
        'quantity',
        'location',
        'delivery_date'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Inventory::class;
    }
}
