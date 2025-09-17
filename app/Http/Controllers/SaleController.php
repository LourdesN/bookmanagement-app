<?php

namespace App\Http\Controllers;

use App\DataTables\SaleDataTable;
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\StoreSaleRequest;
use App\Repositories\SaleRepository;
use App\Models\Book;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\ReorderLevelAlert;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Laracasts\Alert\Alert as AlertAlert;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Log;


use App\Http\Controllers\InventoryController;

class SaleController extends AppBaseController
{
    /** @var SaleRepository $saleRepository*/
    private $saleRepository;

    /** @var InventoryController $inventoryController */
    private $inventoryController;

    public function __construct(SaleRepository $saleRepo, InventoryController $inventoryController)
    {
        $this->saleRepository = $saleRepo;
        $this->inventoryController = $inventoryController;
    }

    /**
     * Display a listing of the Sale.
     */
    public function index(SaleDataTable $saleDataTable)
    {
    return $saleDataTable->render('sales.index');
    }


    /**
     * Show the form for creating a new Sale.
     */

    public function create()
    {
        $booksData = Book::pluck('unit_cost', 'id'); 
        Log::info('📚 Books Data:', $booksData->toArray());
        $books = Book::pluck('title', 'id');
    $customers = Customer::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                         ->pluck('name', 'id');
        return view('sales.create', compact('books', 'customers', 'booksData'));
    }

    /**
     * Store a newly created Sale in storage.
     */
    public function store(CreateSaleRequest $request)
    {
        Log::info('🟢 SaleController@store', ['input' => $request->all()]);

        $input = $request->all();
        $book = \App\Models\Book::find($input['book_id']);
        if (!$book) {
            Log::warning('❌ Book not found', ['book_id' => $input['book_id']]);
            Alert::error('Book does not exist.');
            return redirect()->back()->withInput();
        }

        $customer = Customer::find($input['customer_id']);
        if (!$customer) {
            Log::warning('❌ Customer not found', ['customer_id' => $input['customer_id']]);
            Alert::error('Customer does not exist.');
            return redirect()->back()->withInput();
        }

        $total = number_format((float) $input['total'], 2, '.', '');
        $amountPaid = number_format((float) ($input['amount_paid'] ?? 0), 2, '.', '');
        $balanceDue = number_format(max(0, (float) $total - (float) $amountPaid), 2, '.', '');
        $paymentStatus = $amountPaid >= $total ? 'Paid' : ($amountPaid > 0 ? 'Partially Paid' : 'Unpaid');

        DB::beginTransaction();
        try {
            // Create sale
            $sale = $this->saleRepository->create([
                'book_id' => (int) $input['book_id'],
                'customer_id' => (int) $input['customer_id'],
                'quantity' => (int) $input['quantity'],
                'unit_price' => number_format((float) $input['unit_price'], 2, '.', ''),
                'total' => $total,
                'amount_paid' => $amountPaid,
                'balance_due' => $balanceDue,
                'payment_status' => $paymentStatus,
            ]);
            Log::info('✅ Sale created', ['sale_id' => $sale->id]);

            if ($amountPaid > 0) {
                Payment::create(['sale_id' => $sale->id, 'amount' => $amountPaid, 'payment_date' => now()]);
            }

            // Decrement inventory
            Log::info('📦 Calling decrementInventory', ['book_id' => $input['book_id'], 'quantity' => $input['quantity']]);
            $this->inventoryController->decrementInventory($input['book_id'], $input['quantity']);
            Log::info('✅ Inventory decremented');

            DB::commit();
            Alert::success('Success', 'Sale and inventory updated.');
            return redirect(route('sales.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error: ' . $e->getMessage(), ['sql' => DB::getQueryLog()]);
            Alert::error('Error: ' . $e->getMessage());
            return redirect()->back()->withInput();
        } finally {
            DB::disableQueryLog();
        }
    }


    /**
     * Display the specified Sale.
     */
   public function show($id)
   {
    $saleId = (int) $id; // cast directly
    $sale = $this->saleRepository->find($saleId);

    if (empty($sale)) {
        Alert::error('Sale not found');
        return redirect(route('sales.index'));
    }

    $books = Book::pluck('title', 'id');
    $customers = Customer::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                         ->pluck('name', 'id');

    return view('sales.show', compact('sale', 'books', 'customers'));
   }

    /**
     * Show the form for editing the specified Sale.
     */
    public function edit($id)
     {
        $sale = $this->saleRepository->find($id);

        if (empty($sale)) {
            Alert::error('Sale not found');

            return redirect(route('sales.index'));
        }
        $booksData = Book::pluck('unit_cost', 'id');
        $books = Book::pluck('title', 'id');
        $customers = Customer::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                             ->pluck('name', 'id');

        return view('sales.edit', compact('sale', 'books', 'customers', 'booksData'));
    }

    /**
     * Update the specified Sale in storage.
     */
    public function update($id, UpdateSaleRequest $request)
     {
        $sale = $this->saleRepository->find($id);

        if (empty($sale)) {
            Alert::error('Sale not found');

            return redirect(route('sales.index'));
        }

        $sale = $this->saleRepository->update($request->all(), $id);

        Alert::success('Sale updated successfully.');

        return redirect(route('sales.index'));
    }

    /**
     * Remove the specified Sale from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $sale = $this->saleRepository->find($id);

        if (empty($sale)) {
            Alert::error('Sale not found');

            return redirect(route('sales.index'));
        }

        $this->saleRepository->delete($id);

        Alert::success('Success', 'Sale deleted successfully.');

        return redirect(route('sales.index'));
    }
   public function debtors()
   {
    $debtors = Sale::with('customer', 'book')
        ->where('payment_status', '!=', 'Paid')
        ->get();

    return view('sales.debtors', compact('debtors'));
   }


}
