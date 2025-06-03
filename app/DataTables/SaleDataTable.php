<?php

namespace App\DataTables;

use App\Models\Sale;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class SaleDataTable extends DataTable
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

    $dataTable
        ->addColumn('book_title', function ($sale) {
            return optional($sale->book)->title;
        })
        ->addColumn('customer_name', function ($sale) {
            return optional($sale->customer)->first_name . ' ' . optional($sale->customer)->last_name;
        })
        ->editColumn('unit_price', function ($sale) {
            return 'Kshs. ' . number_format($sale->unit_price, 2);
        })
        ->editColumn('total', function ($sale) {
            return 'Kshs. ' . number_format($sale->total, 2);
        })
        ->editColumn('balance_due', function ($sale) {
            return 'Kshs. ' . number_format($sale->total - $sale->amount_paid, 2);
        })
        ->editColumn('amount_paid', function ($sale) {
            return 'Kshs. ' . number_format($sale->amount_paid, 2);
        })
        ->addColumn('new payment', function ($sale) {
    if ($sale->payment_status !== 'Paid') {
        return '<a href="' . route('payments.create', ['sale_id' => $sale->id]) . '" class="btn btn-sm btn-primary">Make Payment</a>';
    } else {
        return '<span class="badge badge-success">Paid</span>';
    }
})

        ->addColumn('action', 'sales.datatables_actions')
        ->rawColumns(['new payment', 'action']);

    return $dataTable;
}

    

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Sale $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Sale $model)
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
            'book_title' => ['title' => 'Book Title', 'data' => 'book_title'],
            'customer_name' => ['title' => 'Customer Name', 'data' => 'customer_name'],
            'quantity',
            'unit_price',
            'total',
            'balance_due',
            'amount_paid',
            'payment_status',
            'new payment' => ['title' => 'New Payment', 'data' => 'new payment', 'orderable' => false, 'searchable' => false],
        ];
    }
    

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'sales_datatable_' . time();
    }
}
