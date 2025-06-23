<?php

namespace App\Http\Controllers;

use App\DataTables\SaleDataTable;
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\SaleRepository;
use Illuminate\Http\Request;
use Flash;
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
use RealRashid\SweetAlert\Facades\Alert;

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
        $books = Book::pluck('title', 'id');
    $customers = Customer::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                         ->pluck('name', 'id');
        return view('sales.create', compact('books', 'customers'));
    }

    /**
     * Store a newly created Sale in storage.
     */
    
  public function store(CreateSaleRequest $request)
{
    $input = $request->all();

    DB::beginTransaction();

    try {
        // Step 1: Check inventory
        $inventory = Inventory::where('book_id', $input['book_id'])->first();

        if (!$inventory) {
            Flash::error('No inventory found for this book.');
            return redirect()->back();
        }

        if ($inventory->quantity < $input['quantity']) {
            Flash::error('Insufficient inventory quantity for this sale.');
            return redirect()->back();
        }

        // Step 2: Determine payment status and balance due
        $total = $input['total'];
        $amountPaid = $input['amount_paid'] ?? 0;

        $input['payment_status'] = $amountPaid >= $total ? 'Paid' :
                                    ($amountPaid > 0 ? 'Partially Paid' : 'Unpaid');

        $input['balance_due'] = $total - $amountPaid;

        // Step 3: Create sale
        $sale = $this->saleRepository->create($input);

        // Step 4: Deduct inventory
        $inventory->decrement('quantity', $input['quantity']);

        // Step 5: Log payment if any
        if ($amountPaid > 0) {
            Payment::create([
                'sale_id' => $sale->id,
                'amount' => $amountPaid,
                'payment_date' => now(),
            ]);
        }

        // Step 6: Reorder level check and notify
        $book = $inventory->book;
        if ($inventory->fresh()->quantity <= $book->reorder_level) {
            FacadesNotification::route('mail', 'lourdeswairimu@gmail.com')
                ->notify(new ReorderLevelAlert($inventory));

            $users = User::all();
            foreach ($users as $user) {
                $user->notify(new ReorderLevelAlert($inventory));
            }
        }

        DB::commit();

        Alert::success('Success', 'Sale, payment, and inventory updated successfully.');
        return redirect(route('sales.index'));

    } catch (\Exception $e) {
        DB::rollBack();
        Flash::error('An error occurred while saving the sale: ' . $e->getMessage());
    
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
        Flash::error('Sale not found');
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
            Flash::error('Sale not found');

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
            Flash::error('Sale not found');

            return redirect(route('sales.index'));
        }

        $sale = $this->saleRepository->update($request->all(), $id);

        Flash::success('Sale updated successfully.');

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
            Flash::error('Sale not found');

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
