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
    DB::beginTransaction();

    try {
        \Log::info('➡️ Starting sale transaction', $request->all());

        // Validate book & customer exist
        $book = Book::findOrFail($request->book_id);
        $customer = Customer::findOrFail($request->customer_id);

        // Create sale
        $sale = new Sale();
        $sale->book_id = $book->id;
        $sale->customer_id = $customer->id;
        $sale->quantity = $request->quantity;
        $sale->unit_price = $request->unit_price;
        $sale->total = $request->total;
        $sale->amount_paid = $request->amount_paid ?? 0;
        $sale->save(); // will trigger payment_status + balance_due boot logic

        \Log::info('✅ Sale saved in DB', ['sale_id' => $sale->id]);

        // Adjust inventory
        $inventory = Inventory::where('book_id', $book->id)->lockForUpdate()->first();
        if (!$inventory) {
            throw new \Exception("Inventory not found for book_id {$book->id}");
        }
        if ($inventory->quantity < $sale->quantity) {
            throw new \Exception("Not enough stock: have {$inventory->quantity}, need {$sale->quantity}");
        }
        $inventory->decrement('quantity', $sale->quantity);
        \Log::info('➖ Inventory decremented', ['remaining' => $inventory->quantity]);

        // Record payment (if any)
        if ($sale->amount_paid > 0) {
            Payment::create([
                'sale_id' => $sale->id,
                'amount' => $sale->amount_paid,
                'payment_date' => now(),
            ]);
            \Log::info('💵 Payment recorded', ['amount' => $sale->amount_paid]);
        }

        // Check for low stock and notify
        if ($inventory->quantity <= $book->reorder_level) {
            $users = User::whereNotNull('device_token')->get();
            if ($users->isNotEmpty()) {
                FacadesNotification::send($users, new ReorderLevelAlert($book, $inventory->quantity));
                \Log::info('🔔 Reorder level alert sent', ['book_id' => $book->id, 'remaining_stock' => $inventory->quantity]);
            } else {
                \Log::warning('⚠️ No users with device tokens to notify for low stock');
            }
        }

        DB::commit();
        return response()->json(['success' => true, 'message' => 'Sale successful']);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('❌ Sale failed', [
            'error_message' => $e->getMessage(),
            'request_data' => $request->all()
        ]);
        return response()->json(['success' => false, 'message' => 'Sale failed: '.$e->getMessage()]);
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
