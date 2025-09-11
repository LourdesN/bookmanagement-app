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

    // ✅ Validate request
    $data = $request->validated();
    Log::info('📥 Input received:', $data);

    // Cast numeric fields to correct types
    $data['quantity']    = (int) ($data['quantity'] ?? 0);
    $data['unit_price']  = (int) ($data['unit_price'] ?? 0);      // INTEGER in DB
    $data['total']       = (float) ($data['total'] ?? 0);        // NUMERIC(10,2)
    $data['amount_paid'] = (float) ($data['amount_paid'] ?? 0);  // NUMERIC(10,2)
    $data['balance_due'] = max(0, $data['total'] - $data['amount_paid']); // NUMERIC(10,2)

    // Determine payment status
    $data['payment_status'] = match (true) {
        $data['amount_paid'] >= $data['total'] => 'Paid',
        $data['amount_paid'] > 0 => 'Partially Paid',
        default => 'Unpaid',
    };

    DB::beginTransaction();

    try {
        // ✅ Foreign key checks
        if (!Book::where('id', $data['book_id'])->exists()) {
            throw new \Exception("Book with ID {$data['book_id']} does not exist.");
        }

        if (!Customer::where('id', $data['customer_id'])->exists()) {
            throw new \Exception("Customer with ID {$data['customer_id']} does not exist.");
        }

        // 🔍 Check inventory
        $inventory = Inventory::where('book_id', $data['book_id'])->first();
        if (!$inventory) {
            throw new \Exception("No inventory found for this book.");
        }

        if ($inventory->quantity < $data['quantity']) {
            throw new \Exception("Insufficient inventory. Available: {$inventory->quantity}");
        }

        // ✅ Create sale
        $sale = Sale::create([
            'book_id'        => $data['book_id'],
            'customer_id'    => $data['customer_id'],
            'quantity'       => $data['quantity'],
            'unit_price'     => $data['unit_price'],
            'total'          => $data['total'],
            'balance_due'    => $data['balance_due'],
            'amount_paid'    => $data['amount_paid'],
            'payment_status' => $data['payment_status'], // plain string
        ]);

        // 📦 Update inventory
        $inventory->decrement('quantity', $data['quantity']);

        // 💰 Record payment if any
        if ($data['amount_paid'] > 0) {
            Payment::create([
                'sale_id'      => $sale->id,
                'amount'       => $data['amount_paid'],
                'payment_date' => now(),
            ]);
        }

        // 📡 Reorder notification
        if ($inventory->fresh()->quantity <= $inventory->book->reorder_level) {
            $this->sendReorderNotifications($inventory);
        }

        DB::commit();
        Log::info('✅ Sale completed successfully');
        Alert::success('Success', 'Sale, payment, and inventory updated successfully.');

        return redirect()->route('sales.index');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Exception occurred: ' . $e->getMessage());
        Alert::error('An error occurred: ' . $e->getMessage());
        return redirect()->back()->withInput();
    }
}

/**
 * Sends reorder notifications to admin and users.
 */
private function sendReorderNotifications(Inventory $inventory)
{
    Log::info('📨 Sending reorder alert emails...');
    FacadesNotification::route('mail', 'lourdeswairimu@gmail.com')
        ->notify(new ReorderLevelAlert($inventory));

    User::all()->each(fn($user) => $user->notify(new ReorderLevelAlert($inventory)));
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
        $books = Book::pluck('title', 'id');
        $customers = Customer::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                             ->pluck('name', 'id');

        return view('sales.edit', compact('sale', 'books', 'customers'));
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
