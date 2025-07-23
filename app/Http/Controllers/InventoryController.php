<?php

namespace App\Http\Controllers;

use App\DataTables\InventoryDataTable;
use App\Http\Requests\CreateInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\InventoryRepository;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Notifications\ReorderLevelAlert;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class InventoryController extends AppBaseController
{
    /** @var InventoryRepository $inventoryRepository*/
    private $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepo)
    {
        $this->inventoryRepository = $inventoryRepo;
    }

    /**
     * Display a listing of the Inventory.
     */
    public function index(InventoryDataTable $inventoryDataTable)
    {
    return $inventoryDataTable->render('inventories.index');
    }


public function downloadPDF()
{
    $data = [
        'inventories' => \App\Models\Inventory::with('book')->get()
    ];

    return Pdf::loadView('pdf.inventory', $data)
              ->setPaper('a4', 'landscape') // optional: set orientation
              ->download('inventory.pdf');
}


    /**
     * Show the form for creating a new Inventory.
     */
    public function create()
    {
        $books = Book::pluck('title', 'id');
        return view('inventories.create', compact('books'));
    }

    /**
     * Store a newly created Inventory in storage.
     */
    public function store(CreateInventoryRequest $request)
    {
        $input = $request->all();

        $inventory = $this->inventoryRepository->create($input);

        Alert::success('Success', 'Inventory saved successfully.');

        return redirect(route('inventories.index'));
    }

    /**
     * Display the specified Inventory.
     */
    public function show($id)
    {
        $inventory = $this->inventoryRepository->find($id);

        if (empty($inventory)) {
            Alert::error('Inventory not found');

            return redirect(route('inventories.index'));
        }
        $books = Book::pluck('title', 'id');
        return view('inventories.show', compact('inventory', 'books'));
    }

    /**
     * Show the form for editing the specified Inventory.
     */
    public function edit($id)
    {
        $inventory = $this->inventoryRepository->find($id);

        if (empty($inventory)) {
            Alert::error('Inventory not found');

            return redirect(route('inventories.index'));
        }

        $books = Book::pluck('title', 'id');

        return view('inventories.edit', compact('inventory', 'books'));
    }

    /**
     * Update the specified Inventory in storage.
     */
    public function update($id, UpdateInventoryRequest $request)
    {
        $inventory = $this->inventoryRepository->find($id);

        if (empty($inventory)) {
            Alert::error('Inventory not found');

            return redirect(route('inventories.index'));
        }

        $inventory = $this->inventoryRepository->update($request->all(), $id);

        Alert::success('Inventory updated successfully.');

        return redirect(route('inventories.index'));
    }

    /**
     * Remove the specified Inventory from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $inventory = $this->inventoryRepository->find($id);

        if (empty($inventory)) {
            Alert::error('Inventory not found');

            return redirect(route('inventories.index'));
        }

        $this->inventoryRepository->delete($id);

        Alert::success('Success', 'Inventory deleted successfully.');

        return redirect(route('inventories.index'));
    }
    public function updateInventoryFromDelivery($book_id, $quantity)
{
    $inventory = Inventory::firstOrCreate(['book_id' => $book_id]);

    $inventory->increment('quantity', $quantity);

    // Check reorder level
    $book = $inventory->book; // assuming a relationship defined
    if ($inventory->quantity <= $book->reorder_level) {
        FacadesNotification::route('mail', 'lourdeswairimu@gmail.com')
            ->notify(new ReorderLevelAlert($inventory));
    }
}

}
