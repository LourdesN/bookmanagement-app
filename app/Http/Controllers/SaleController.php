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

public function store(CreateSaleRequest $request)
{
    Log::info('🟢 SaleController@store triggered');

    $input = $request->all();
    Log::info('📥 Input received:', $input);

    // ✅ Calculate amounts
    $total = (float) $input['total'];
    $amountPaid = (float) ($input['amount_paid'] ?? 0);

    $input['payment_status'] = $amountPaid >= $total
        ? 'Paid'
        : ($amountPaid > 0 ? 'Partially Paid' : 'Unpaid');

    $input['balance_due'] = max(0, $total - $amountPaid);

    DB::beginTransaction();

    try {
        Log::info("🔍 Checking inventory for book_id={$input['book_id']}");

        $inventory = Inventory::where('book_id', $input['book_id'])->first();

        if (!$inventory) {
            Log::warning("❌ No inventory found for book_id={$input['book_id']}");
            Alert::error('No inventory found for this book.');
            return redirect()->back();
        }

        if ($inventory->quantity < $input['quantity']) {
            Log::warning("❌ Not enough stock. Available={$inventory->quantity}, Requested={$input['quantity']}");
            Alert::error('Insufficient inventory quantity for this sale.');
            return redirect()->back();
        }

        Log::info('✅ Creating sale...', $input);

        $sale = $this->saleRepository->create($input);

        Log::info("📦 Decrementing inventory by {$input['quantity']}");
        $inventory->decrement('quantity', $input['quantity']);

        if ($amountPaid > 0) {
            Log::info("💰 Logging payment of {$amountPaid} for sale_id={$sale->id}");
            Payment::create([
                'sale_id'      => $sale->id,
                'amount'       => $amountPaid,
                'payment_date' => now(),
            ]);
        }

        Log::info("📡 Checking reorder level...");
        $book = $inventory->book;

        if ($inventory->fresh()->quantity <= $book->reorder_level) {
            Log::info("📨 Sending reorder level alerts...");
            FacadesNotification::route('mail', 'lourdeswairimu@gmail.com')
                ->notify(new ReorderLevelAlert($inventory));

            foreach (User::all() as $user) {
                $user->notify(new ReorderLevelAlert($inventory));
            }
        }

        DB::commit();

        Log::info("✅ Sale #{$sale->id} completed successfully");
        Alert::success('Success', 'Sale, payment, and inventory updated successfully.');

        return redirect(route('sales.index'));

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error("❌ Exception occurred: " . $e->getMessage());
        Alert::error('An error occurred while saving the sale: ' . $e->getMessage());
        return redirect()->back();
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
