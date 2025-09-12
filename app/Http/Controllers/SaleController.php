<?php

namespace App\Http\Controllers;

use App\DataTables\SaleDataTable;
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\SaleRepository;
use App\Models\Book;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\User;
use App\Notifications\ReorderLevelAlert;
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
        // Validate request first
        $request->validate([
            'book_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'balance_due' => 'required|numeric|min:0',
        ]);

        try {
            // Fetch inventory
            $inventory = Inventory::where('book_id', $request->book_id)->first();

            if (!$inventory) {
                throw new \Exception("Inventory not found for book_id: {$request->book_id}");
            }

            // Check stock availability
            if ($inventory->quantity < $request->quantity) {
                throw new \Exception(
                    "Not enough stock for book_id {$request->book_id}. " .
                    "Requested: {$request->quantity}, Available: {$inventory->quantity}"
                );
            }

            // Start transaction
            DB::beginTransaction();

            // Decrement inventory
            $inventory->decrement('quantity', $request->quantity);

            // Create sale
            $sale = Sale::create([
                'book_id' => $request->book_id,
                'customer_id' => $request->customer_id,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price,
                'total' => $request->total,
                'balance_due' => $request->balance_due,
                'amount_paid' => $request->amount_paid ?? 0,
                'payment_status' => $request->payment_status ?? 'Unpaid',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale successful',
                'sale_id' => $sale->id,
                'remaining_stock' => $inventory->quantity
            ]);

        } catch (\Exception $e) {
            // Rollback any partial operations
            DB::rollBack();

            // Log full details for debugging
            Log::error('Sale failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'inventory_id' => $inventory->id ?? null,
                'inventory_quantity' => $inventory->quantity ?? null
            ]);

            // Return user-friendly error
            return response()->json([
                'success' => false,
                'message' => "Sale failed: " . $e->getMessage(),
                'inventory_id' => $inventory->id ?? null,
                'remaining_stock' => $inventory->quantity ?? null
            ]);
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
