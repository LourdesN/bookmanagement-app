<?php

namespace App\DataTables;

use App\Models\Inventory;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class InventoryDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
{
    $dataTable = new EloquentDataTable($query);

    return $dataTable
        ->addColumn('book_title', function ($row) {
            return $row->book->title ?? 'N/A';
        })
        ->addColumn('action', 'inventories.datatables_actions')
        ->rawColumns(['action']); // if using HTML in actions
}


    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Inventory $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Inventory $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false])
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'order'     => [[0, 'desc']],
                'buttons'   => [
                    // Enable Buttons as per your need
                //    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
                //    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner',],
                //    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner',],
                //    ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner',],
                //    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',],


                ],
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'book_title' => ['title' => 'Book Title'],
            'quantity',
            'location',
        ];
    }
    
    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'inventories_datatable_' . time();
    }
}
