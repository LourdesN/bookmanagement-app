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
        $data = $request->only([
            'book_id', 'customer_id', 'quantity', 
            'unit_price', 'total', 'balance_due', 
            'amount_paid', 'payment_status'
        ]);

        Log::info("🟢 SaleController@store triggered");
        Log::info("📥 Input received: " . json_encode($data));

        try {
            // Step 1: Validate book exists
            $book = Book::find($data['book_id']);
            if (!$book) {
                $message = "Sale failed: Book not found for id {$data['book_id']}";
                Log::error($message);
                return response()->json(['success' => false, 'message' => $message]);
            }

            // Step 2: Validate customer exists
            $customer = Customer::find($data['customer_id']);
            if (!$customer) {
                $message = "Sale failed: Customer not found for id {$data['customer_id']}";
                Log::error($message);
                return response()->json(['success' => false, 'message' => $message]);
            }

            // Step 3: Validate inventory exists and stock is enough
            $inventory = Inventory::where('book_id', $data['book_id'])->first();
            if (!$inventory) {
                $message = "Sale failed: Inventory not found for book_id: {$data['book_id']}";
                Log::error($message);
                return response()->json(['success' => false, 'message' => $message]);
            }

            if ($inventory->quantity < $data['quantity']) {
                $message = "Sale failed: Not enough stock for book_id: {$data['book_id']}";
                Log::error($message);
                return response()->json(['success' => false, 'message' => $message]);
            }

            // Step 4: Start transaction
            DB::transaction(function () use ($data, $inventory) {
                // Decrement inventory
                $inventory->decrement('quantity', $data['quantity']);

                // Create sale
                Sale::create([
                    'book_id' => $data['book_id'],
                    'customer_id' => $data['customer_id'],
                    'quantity' => $data['quantity'],
                    'unit_price' => $data['unit_price'],
                    'total' => $data['total'],
                    'balance_due' => $data['balance_due'],
                    'amount_paid' => $data['amount_paid'] ?? 0,
                    'payment_status' => $data['payment_status'] ?? 'Unpaid',
                ]);
            });

            $message = "Sale successful for book_id: {$data['book_id']}";
            Log::info($message);
            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            $message = "Sale failed: " . $e->getMessage();
            Log::error($message, ['data' => $data]);
            return response()->json(['success' => false, 'message' => $message]);
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
