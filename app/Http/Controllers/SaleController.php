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
    Log::info('🟢 SaleController@store triggered');
    $input = $request->all();
    Log::info('📥 Input received:', $input);

    DB::enableQueryLog();

    $book = Book::find($input['book_id']);
    if (!$book) {
        Log::warning('❌ Book not found: book_id=' . $input['book_id']);
        Alert::error('Selected book does not exist.');
        return redirect()->back()->withInput();
    }

    $customer = Customer::find($input['customer_id']);
    if (!$customer) {
        Log::warning('❌ Customer not found: customer_id=' . $input['customer_id']);
        Alert::error('Selected customer does not exist.');
        return redirect()->back()->withInput();
    }

    $total = number_format((float) $input['total'], 2, '.', '');
    $amountPaid = isset($input['amount_paid']) ? number_format((float) $input['amount_paid'], 2, '.', '') : '0.00';
    $balanceDue = number_format(max(0, (float) $input['total'] - (float) $input['amount_paid']), 2, '.', '');
    $paymentStatus = $amountPaid >= $total ? 'Paid' : ($amountPaid > 0 ? 'Partially Paid' : 'Unpaid');

    DB::beginTransaction();
    try {
        Log::info('🔍 Checking inventory for book_id=' . $input['book_id']);

        // Lock row with FOR UPDATE to prevent race conditions
        $inventory = DB::select('SELECT * FROM inventories WHERE book_id = ? FOR UPDATE', [$input['book_id']]);
        if (empty($inventory)) {
            Log::warning('❌ Inventory not found for book_id: ' . $input['book_id']);
            Alert::error('No inventory found for this book.');
            return redirect()->back()->withInput();
        }
        $inventory = $inventory[0];
        $quantity = (int) $inventory->quantity;
        if ($quantity < (int) $input['quantity']) {
            Log::warning("❌ Not enough inventory. Available: {$quantity}, Requested: {$input['quantity']}");
            Alert::error('Insufficient inventory quantity for this sale.');
            return redirect()->back()->withInput();
        }

        $newQuantity = $quantity - (int) $input['quantity'];
        Log::info('📦 Decrementing inventory...', [
            'book_id' => $input['book_id'],
            'current_quantity' => $quantity,
            'new_quantity' => $newQuantity,
        ]);

        // Atomic UPDATE
        $affected = DB::update(
            'UPDATE inventories SET quantity = ?, updated_at = ? WHERE id = ? AND quantity >= ?',
            [$newQuantity, now(), $inventory->id, (int) $input['quantity']]
        );
        if ($affected === 0) {
            Log::error('❌ Inventory update failed: No rows affected');
            throw new \Exception('Failed to update inventory');
        }
        Log::info('✅ Inventory decremented successfully', ['book_id' => $input['book_id'], 'new_quantity' => $newQuantity]);

        Log::info('✅ Attempting to create sale with data:', [
            'book_id' => $input['book_id'],
            'customer_id' => $input['customer_id'],
            'quantity' => $input['quantity'],
            'unit_price' => $input['unit_price'],
            'total' => $total,
            'amount_paid' => $amountPaid,
            'balance_due' => $balanceDue,
            'payment_status' => $paymentStatus,
        ]);

        $sale = $this->saleRepository->create([
            'book_id'        => (int) $input['book_id'],
            'customer_id'    => (int) $input['customer_id'],
            'quantity'       => (int) $input['quantity'],
            'unit_price'     => number_format((float) $input['unit_price'], 2, '.', ''),
            'total'          => $total,
            'amount_paid'    => $amountPaid,
            'balance_due'    => $balanceDue,
            'payment_status' => $paymentStatus,
        ]);
        Log::info('✅ Sale created with ID: ' . $sale->id);

        if ($amountPaid > 0) {
            Log::info("💰 Logging payment of {$amountPaid} for sale_id: {$sale->id}");
            Payment::create([
                'sale_id' => $sale->id,
                'amount' => $amountPaid,
                'payment_date' => now(),
            ]);
        }

        Log::info("📡 Checking reorder level...");
        $book = $inventory->book;
        if ($newQuantity <= $book->reorder_level) {
            Log::info('📨 Sending reorder alert emails...');
            FacadesNotification::route('mail', 'lourdeswairimu@gmail.com')
                ->notify(new ReorderLevelAlert($inventory));
            foreach (User::all() as $user) {
                $user->notify(new ReorderLevelAlert($inventory));
            }
        }

        DB::commit();
        Log::info('✅ Sale completed successfully');
        Alert::success('Success', 'Sale, payment, and inventory updated successfully.');
        return redirect(route('sales.index'));
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Exception occurred: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'input' => $input,
            'sql' => DB::getQueryLog(),
        ]);
        Alert::error('An error occurred while saving the sale: ' . $e->getMessage());
        return redirect()->back()->withInput();
    } finally {
        DB::disableQueryLog();
    }
}


public function testInventoryUpdateProd()
{
    Log::info('🟢 Testing inventory update in production (PostgreSQL)');
    DB::enableQueryLog();
    DB::beginTransaction();
    try {
        Log::info('🔄 Testing transaction state with SELECT 1');
        DB::select('SELECT 1');
        Log::info('✅ Transaction state test passed');
        $affected = DB::update(
            'UPDATE inventories SET quantity = quantity - 1, updated_at = ? WHERE id = 2 AND quantity >= 1',
            [now()]
        );
        if ($affected === 0) {
            Log::error('❌ Inventory update failed: No rows affected');
            throw new \Exception('Failed to update inventory');
        }
        Log::info('✅ Inventory updated successfully');
        DB::commit();
        return response()->json(['message' => 'Inventory updated']);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('❌ Failed to update inventory: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'sql' => DB::getQueryLog(),
        ]);
        return response()->json(['error' => $e->getMessage()], 500);
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
