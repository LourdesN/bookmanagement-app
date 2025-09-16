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
    $data = $request->all();

    DB::beginTransaction();

    try {
        // Step 1: Create Sale
        $sale = Sale::create([
            'book_id'      => $data['book_id'],
            'customer_id'  => $data['customer_id'],
            'quantity'     => $data['quantity'],
            'unit_price'   => $data['unit_price'],
            'total'        => $data['total'],
            'amount_paid'  => $data['amount_paid'] ?? 0,
            'balance_due'  => $data['balance_due'] ?? ($data['total'] - ($data['amount_paid'] ?? 0)),
            'payment_status' => $data['payment_status'] ?? 'Unpaid',
        ]);

        Log::info('✅ Sale created', ['sale_id' => $sale->id]);

        // Step 2: Update Inventory
        $inventory = Inventory::where('book_id', $data['book_id'])->first();

        if (!$inventory) {
            throw new \Exception("Inventory not found for book_id {$data['book_id']}");
        }

        if ($inventory->quantity < $data['quantity']) {
            throw new \Exception("Not enough stock. Available: {$inventory->quantity}, requested: {$data['quantity']}");
        }

        $inventory->decrement('quantity', $data['quantity']);

        Log::info('📦 Inventory updated', [
            'inventory_id' => $inventory->id,
            'new_quantity' => $inventory->quantity
        ]);

        // Step 3: Check for Reorder Level
        if ($inventory->quantity <= $inventory->reorder_level) {
            $users = User::whereNotNull('phone')->get();
            FacadesNotification::send($users, new ReorderLevelAlert($inventory)); 
            Log::warning('⚠️ Reorder level reached', [
                'inventory_id' => $inventory->id,
                'current_quantity' => $inventory->quantity,
                'reorder_level' => $inventory->reorder_level
            ]);
        }
        
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Sale processed successfully',
            'sale_id' => $sale->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('❌ Sale failed', [
            'error_message' => $e->getMessage(),
            'error_code'    => $e->getCode(),
            'file'          => $e->getFile(),
            'line'          => $e->getLine(),
            'request_data'  => $data
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Sale failed: ' . $e->getMessage()
        ], 500);
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
