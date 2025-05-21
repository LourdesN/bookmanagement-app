<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\BaseRepository;

class BookRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'title',
        'print_date',
        'unit_cost',
        'isbn',
        'description'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Book::class;
    }
}
