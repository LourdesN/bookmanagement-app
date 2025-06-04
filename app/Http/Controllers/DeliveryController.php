<?php

namespace App\Http\Controllers;

use App\DataTables\DeliveryDataTable;
use App\Http\Requests\CreateDeliveryRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\DeliveryRepository;
use Illuminate\Http\Request;
use Flash;
use App\Models\Book;
use App\Models\Delivery;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\User;
use App\Notifications\ReorderLevelAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;


class DeliveryController extends AppBaseController
{
    /** @var DeliveryRepository $deliveryRepository*/
    private $deliveryRepository;

    public function __construct(DeliveryRepository $deliveryRepo)
    {
        $this->deliveryRepository = $deliveryRepo;
    }

    /**
     * Display a listing of the Delivery.
     */
    public function index(DeliveryDataTable $deliveryDataTable)
    {
    return $deliveryDataTable->render('deliveries.index');
    }


    /**
     * Show the form for creating a new Delivery.
     */
    public function create()
    {
        $books = Book::pluck('title', 'id');
        $suppliers = Supplier::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                             ->pluck('name', 'id');
        return view('deliveries.create', compact('books', 'suppliers'));
    }

    /**
     * Store a newly created Delivery in storage.
     */
public function store(Request $request)
{
    $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'book_id' => 'required|exists:books,id',
        'quantity' => 'required|integer|min:1',
        'delivery_date' => 'required|date',
    ]);

    // Store delivery
    $delivery = Delivery::create([
        'supplier_id' => $request->supplier_id,
        'book_id' => $request->book_id,
        'quantity' => $request->quantity,
        'delivery_date' => $request->delivery_date,
    ]);

    // Update inventory
    $inventory = Inventory::firstOrCreate(
        ['book_id' => $request->book_id],
        [
            'quantity' => 0,
            'location' => 'KMA Center UpperHill'
        ]
    );

    $inventory->quantity += $request->quantity;
    $inventory->save();

    // Check against reorder level
    $book = $inventory->book;
    if ($inventory->quantity <= $book->reorder_level) {
        // Notify users only if stock is at or below reorder level
        Notification::route('mail', 'lourdeswairimu@gmail.com')
            ->notify(new ReorderLevelAlert($inventory));

        $users = User::all(); // Filter roles if needed
        foreach ($users as $user) {
            $user->notify(new ReorderLevelAlert($inventory));
        }
    }

    Alert::success('Success', 'Delivery saved and inventory updated successfully.');
    return redirect(route('deliveries.index'));
}



    /**
     * Display the specified Delivery.
     */
    public function show($id)
    {
        $delivery = $this->deliveryRepository->find($id);

        if (empty($delivery)) {
            Flash::error('Delivery not found');

            return redirect(route('deliveries.index'));
        }

        $books = Book::pluck('title', 'id');
        $suppliers = Supplier::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                             ->pluck('name', 'id');

        return view('deliveries.show', compact('delivery', 'books', 'suppliers'));
    }

    /**
     * Show the form for editing the specified Delivery.
     */
    /**
 * Show the form for editing the specified Delivery.
 */
public function edit($id)
{
    $delivery = $this->deliveryRepository->find($id);

    if (empty($delivery)) {
        Flash::error('Delivery not found');
        return redirect(route('deliveries.index'));
    }

    $books = Book::pluck('title', 'id');
    $suppliers = Supplier::selectRaw("CONCAT(first_name, ' ', last_name) AS name, id")
                         ->pluck('name', 'id');

    return view('deliveries.edit', compact('delivery', 'books', 'suppliers'));
}


    /**
     * Update the specified Delivery in storage.
     */
    public function update($id, UpdateDeliveryRequest $request)
    {
        $delivery = $this->deliveryRepository->find($id);

        if (empty($delivery)) {
            Flash::error('Delivery not found');

            return redirect(route('deliveries.index'));
        }

        $delivery = $this->deliveryRepository->update($request->all(), $id);

        Flash::success('Delivery updated successfully.');

        return redirect(route('deliveries.index'));
    }

    /**
     * Remove the specified Delivery from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $delivery = $this->deliveryRepository->find($id);

        if (empty($delivery)) {
            Flash::error('Delivery not found');

            return redirect(route('deliveries.index'));
        }

        $this->deliveryRepository->delete($id);

        Alert::success('Success', 'Delivery deleted successfully.');

        return redirect(route('deliveries.index'));
    }
}
