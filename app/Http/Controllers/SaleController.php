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


class SaleController extends AppBaseController
{
    /** @var SaleRepository $saleRepository*/
    private $saleRepository;

    public function __construct(SaleRepository $saleRepo)
    {
        $this->saleRepository = $saleRepo;
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
        $books = Book::pluck('title', 'id');
    $customers = Customer::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                         ->pluck('name', 'id');
        return view('sales.create', compact('books', 'customers', 'booksData'));
    }

    /**
     * Store a newly created Sale in storage.
     */

public function store(Request $request)
{
    // Log every query that runs during this request
    DB::listen(function ($query) {
        Log::info('📜 SQL Executed', [
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time
        ]);
    });

    Log::info('➡️ Starting sale transaction', $request->all());

    DB::beginTransaction();

    try {
        $sale = new Sale();
        $sale->book_id = $request->book_id;
        $sale->customer_id = $request->customer_id;
        $sale->quantity = $request->quantity;
        $sale->unit_price = $request->unit_price;
        $sale->total = $request->total;
        $sale->amount_paid = $request->amount_paid ?? 0;

        // set payment_status
        if ($sale->amount_paid >= $sale->total) {
            $sale->payment_status = 'Paid';
        } elseif ($sale->amount_paid > 0) {
            $sale->payment_status = 'Partially Paid';
        } else {
            $sale->payment_status = 'Unpaid';
        }

        $sale->balance_due = max(0, $sale->total - $sale->amount_paid);

        Log::info('📝 Preparing sale insert', $sale->toArray());

        $sale->save();
        Log::info('✅ Sale created', ['sale_id' => $sale->id]);

        // 2️⃣ Update Inventory
        $inventory = Inventory::where('book_id', $sale->book_id)->first();

        if (!$inventory) {
            throw new \Exception("Inventory not found for book_id {$sale->book_id}");
        }

        if ($inventory->quantity < $sale->quantity) {
            throw new \Exception("Not enough stock for book_id {$sale->book_id}");
        }

        $inventory->quantity -= $sale->quantity;
        $inventory->save();
        Log::info('📦 Inventory updated', [
            'inventory_id' => $inventory->id,
            'new_quantity' => $inventory->quantity,
        ]);

        // 3️⃣ Add Payment if necessary
        if ($sale->amount_paid > 0) {
            $payment = new Payment();
            $payment->sale_id = $sale->id;
            $payment->amount = $sale->amount_paid;
            $payment->save();
            Log::info('💰 Payment recorded', ['payment_id' => $payment->id]);
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Sale completed successfully']);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Sale failed', [
            'error_message' => $e->getMessage(),
            'request_data' => $request->all(),
        ]);
        return response()->json(['success' => false, 'message' => 'Sale failed: ' . $e->getMessage()]);
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
