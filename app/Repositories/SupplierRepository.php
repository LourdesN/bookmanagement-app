<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\BaseRepository;

class SupplierRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'first_name',
        'last_name',
        'phone_number',
        'email'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Supplier::class;
    }
}
