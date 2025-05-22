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
use App\Models\Supplier;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;


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

public function store(CreateDeliveryRequest $request)
{
    $input = $request->all();

    DB::transaction(function () use ($input) {
        // Save delivery through repository
        $delivery = $this->deliveryRepository->create($input);

        // Try to find inventory for this book and location
        $inventory = Inventory::where('book_id', $input['book_id'])
            ->where('location', $input['location']) // Ensure this field is in the form
            ->first();

        if ($inventory) {
            // Update existing inventory
            $inventory->quantity += $input['quantity'];
            $inventory->delivery_date = $input['delivery_date'];
            $inventory->save();
        } else {
            // Create new inventory entry
            Inventory::create([
                'book_id' => $input['book_id'],
                'quantity' => $input['quantity'],
                'location' => $input['location'],
                'delivery_date' => $input['delivery_date'],
            ]);
        }
    });

    Flash::success('Delivery saved and inventory updated successfully.');

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

        Flash::success('Delivery deleted successfully.');

        return redirect(route('deliveries.index'));
    }
}
