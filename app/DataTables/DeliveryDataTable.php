<?php

namespace App\DataTables;

use App\Models\Delivery;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class DeliveryDataTable extends DataTable
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
                return optional($row->book)->title ?? 'N/A';
            })
            ->addColumn('supplier_name', function ($row) {
                if ($row->supplier) {
                    return $row->supplier->first_name . ' ' . $row->supplier->last_name;
                }
                return 'N/A';
            })
            ->addColumn('action', 'deliveries.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Delivery $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Delivery $model)
    {
        return $model->newQuery()->with(['book', 'supplier']);
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
                    // ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner'],
                    // ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner'],
                    // ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner'],
                    // ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner'],
                    // ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner'],
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
            'supplier_name' => ['title' => 'Supplier Name'],
            'quantity',
            'location',
            'delivery_date',
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'deliveries_datatable_' . time();
    }
}
