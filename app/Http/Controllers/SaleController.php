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
    
public function store(CreateSaleRequest $request)
{
    Log::info('🟢 SaleController@store triggered');

    // 1️⃣ Force a fresh DB connection
    DB::disconnect();
    DB::reconnect();

    // 2️⃣ Get validated data
    $data = $request->validated();
    Log::info('📥 Input received:', $data);

    // 3️⃣ Cast numeric fields
    $data['quantity']    = (int) ($data['quantity'] ?? 0);
    $data['unit_price']  = (float) ($data['unit_price'] ?? 0);
    $data['total']       = (float) ($data['total'] ?? 0);
    $data['amount_paid'] = (float) ($data['amount_paid'] ?? 0);
    $data['balance_due'] = max(0, $data['total'] - $data['amount_paid']);

    // 4️⃣ Determine payment status
    if ($data['amount_paid'] >= $data['total']) {
        $data['payment_status'] = 'Paid';
    } elseif ($data['amount_paid'] > 0) {
        $data['payment_status'] = 'Partially Paid';
    } else {
        $data['payment_status'] = 'Unpaid';
    }

    try {
        // 5️⃣ Pre-check foreign keys
        if (!Customer::where('id', $data['customer_id'])->exists()) {
            throw new \Exception("Customer ID {$data['customer_id']} does not exist");
        }

        if (!Book::where('id', $data['book_id'])->exists()) {
            throw new \Exception("Book ID {$data['book_id']} does not exist");
        }

        // 6️⃣ Pre-check inventory
        $inventory = Inventory::where('book_id', $data['book_id'])->first();
        if (!$inventory) {
            throw new \Exception("Inventory not found for book ID {$data['book_id']}");
        }

        if ($inventory->quantity < $data['quantity']) {
            throw new \Exception("Insufficient inventory. Available: {$inventory->quantity}, Requested: {$data['quantity']}");
        }

        // 7️⃣ Wrap actual DB writes in transaction
        DB::transaction(function () use ($data, $inventory) {
            $sale = Sale::create($data);

            // Update inventory
            $inventory->decrement('quantity', $data['quantity']);

            // Record payment if any
            if ($data['amount_paid'] > 0) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'amount' => $data['amount_paid'],
                    'payment_date' => now(),
                ]);
            }
        });

        Log::info('✅ Sale completed successfully');
        Alert::success('Sale completed successfully');
        return redirect()->route('sales.index');

    } catch (\Throwable $e) {
        // 8️⃣ Log full error
        Log::error('❌ Sale failed: ' . $e->getMessage(), [
            'exception' => $e,
            'data' => $data,
        ]);

        // 9️⃣ Display error to user on page
        Alert::error('Sale failed: ' . $e->getMessage());
        return redirect()->back()->withInput();
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
