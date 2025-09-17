<?php

namespace App\Http\Controllers;

use App\DataTables\InventoryDataTable;
use App\Http\Requests\CreateInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Repositories\InventoryRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\ReorderLevelAlert;
use App\Models\Book;
use App\Models\Inventory;
use SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use RealRashid\SweetAlert\Facades\Alert as FacadesAlert;

class InventoryController extends AppBaseController
{
    private $inventoryRepository;

    public function __construct(InventoryRepository $inventoryRepo)
    {
        $this->inventoryRepository = $inventoryRepo;
    }

    public function index(InventoryDataTable $inventoryDataTable)
    {
        return $inventoryDataTable->render('inventories.index');
    }

    public function downloadPDF()
    {
        $data = ['inventories' => Inventory::with('book')->get()];
        return Pdf::loadView('pdf.inventory', $data)
                 ->setPaper('a4', 'landscape')
                 ->download('inventory.pdf');
    }

    public function create()
    {
        $books = Book::pluck('title', 'id');
        return view('inventories.create', compact('books'));
    }

    public function store(CreateInventoryRequest $request)
    {
        $input = $request->all();
        $inventory = $this->inventoryRepository->create($input);
        FacadesAlert::success('Success', 'Inventory saved successfully.');
        return redirect(route('inventories.index'));
    }

    public function show($id)
    {
        $inventory = $this->inventoryRepository->find($id);
        if (empty($inventory)) {
            FacadesAlert::error('Inventory not found');
            return redirect(route('inventories.index'));
        }
        $books = Book::pluck('title', 'id');
        return view('inventories.show', compact('inventory', 'books'));
    }

    public function edit($id)
    {
        $inventory = $this->inventoryRepository->find($id);
        if (empty($inventory)) {
            FacadesAlert::error('Inventory not found');
            return redirect(route('inventories.index'));
        }
        $books = Book::pluck('title', 'id');
        return view('inventories.edit', compact('inventory', 'books'));
    }

    public function update($id, UpdateInventoryRequest $request)
    {
        $inventory = $this->inventoryRepository->find($id);
        if (empty($inventory)) {
            FacadesAlert::error('Inventory not found');
            return redirect(route('inventories.index'));
        }
        $inventory = $this->inventoryRepository->update($request->all(), $id);
        FacadesAlert::success('Inventory updated successfully.');
        return redirect(route('inventories.index'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $inventory = DB::selectOne('SELECT * FROM inventories WHERE id = ? FOR UPDATE NOWAIT', [$id]);
            if (!$inventory) {
                FacadesAlert::error('Inventory not found');
                return redirect(route('inventories.index'));
            }
            DB::delete('DELETE FROM inventories WHERE id = ?', [$id]);
            DB::commit();
            FacadesAlert::success('Success', 'Inventory deleted successfully.');
            return redirect(route('inventories.index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error deleting inventory: ' . $e->getMessage(), ['id' => $id]);
            FacadesAlert::error('Error: Unable to delete inventory.');
            return redirect(route('inventories.index'));
        }
    }

    public function updateInventoryFromDelivery($book_id, $quantity)
    {
        DB::beginTransaction();
        try {
            $book = DB::selectOne('SELECT * FROM books WHERE id = ? FOR UPDATE NOWAIT', [$book_id]);
            if (!$book) {
                throw new \Exception('Book not found');
            }
            $inventory = Inventory::firstOrCreate(['book_id' => $book_id]);
            $inventory->increment('quantity', $quantity);
            if ($inventory->quantity <= $book->reorder_level) {
                Log::info('📨 Sending reorder alert');
                FacadesNotification::route('mail', 'lourdeswairimu@gmail.com')
                    ->notify(new ReorderLevelAlert($inventory));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error updating inventory from delivery: ' . $e->getMessage(), ['book_id' => $book_id]);
            throw $e;
        }
    }
    public function decrementInventory($book_id, $quantity)
    {
        Log::info('🟢 InventoryController@decrementInventory', ['book_id' => $book_id, 'quantity' => $quantity]);

        DB::beginTransaction();
        try {
            // Diagnostic query
            Log::info('🔍 Checking transaction state');
            DB::select('SELECT 1');
            Log::info('✅ Transaction state OK');

            // Lock book
            Log::info('🔒 Locking book', ['book_id' => $book_id]);
            $book = Book::where('id', $book_id)->lockForUpdate()->first();
            if (!$book) {
                Log::warning('❌ Book not found', ['book_id' => $book_id]);
                throw new \Exception('Book does not exist.');
            }
            Log::info('✅ Book locked', ['book_id' => $book_id]);

            // Lock inventory
            Log::info('🔒 Locking inventory', ['book_id' => $book_id]);
            $inventory = Inventory::where('book_id', $book_id)->lockForUpdate()->first();
            if (!$inventory) {
                Log::warning('❌ Inventory not found', ['book_id' => $book_id]);
                throw new \Exception('No inventory for this book.');
            }
            Log::info('✅ Inventory locked', ['id' => $inventory->id]);

            if ($inventory->quantity < (int) $quantity) {
                Log::warning('❌ Insufficient inventory', ['available' => $inventory->quantity, 'requested' => $quantity]);
                throw new \Exception('Insufficient inventory.');
            }

            $newQuantity = $inventory->quantity - (int) $quantity;
            Log::info('📦 Updating inventory', ['id' => $inventory->id, 'new_quantity' => $newQuantity]);
            $affected = $inventory->update([
                'quantity' => $newQuantity,
                'updated_at' => now(),
            ]);
            if (!$affected) {
                throw new \Exception('Inventory update failed');
            }
            Log::info('✅ Inventory updated', ['id' => $inventory->id, 'new_quantity' => $newQuantity]);

            if ($newQuantity <= $book->reorder_level) {
                Log::info('📨 Sending reorder alert');
                FacadesNotification::route('mail', 'lourdeswairimu@gmail.com')
                    ->notify(new ReorderLevelAlert($inventory));
            }

            DB::commit();
            Log::info('✅ Inventory decrement successful');
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error decrementing inventory: ' . $e->getMessage(), ['sql' => DB::getQueryLog()]);
            throw $e;
        } finally {
            DB::disableQueryLog();
        }
    }
}