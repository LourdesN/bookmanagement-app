<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;


class PaymentController extends Controller
{
    /**
     * Display a listing of the payments for a specific sale.
     *
     * @param int $saleId
     * @return \Illuminate\View\View
     */
public function index()
{
    $payments = Payment::with('sale')->latest()->get();
    return view('payments.index', compact('payments'));
}

public function create($sale_id)
{
    $sale = Sale::with('customer', 'book')->findOrFail($sale_id);
    return view('payments.create', compact('sale'));
}
   public function store(Request $request)
{
    // Retrieve sale first for validation
    $sale = Sale::findOrFail($request->sale_id);
    $remaining = $sale->total - $sale->amount_paid;

    // Validate input including custom rule for amount
    $request->validate([
        'sale_id' => 'required|exists:sales,id',
        'amount' => [
            'required',
            'numeric',
            'min:0.01',
            function ($attribute, $value, $fail) use ($remaining) {
                if ($value > $remaining) {
                    $fail("The amount cannot exceed the remaining balance of Ksh " . number_format($remaining, 2));
                }
            },
        ],
        'payment_date' => 'required|date',
    ]);

    // Create new payment
    Payment::create([
        'sale_id' => $sale->id,
        'amount' => $request->amount,
        'payment_date' => $request->payment_date,
    ]);

    // Update amount_paid and payment_status in sale
    $sale->amount_paid += $request->amount;
    $sale->payment_status = $sale->amount_paid >= $sale->total ? 'Paid' : 'Partially Paid';
    $sale->save();

    return redirect()->route('sales.show', $sale->id)->with('success', 'Payment recorded successfully.');
}


public function downloadPdf()
{
    $payments = Payment::with('sale.customer', 'sale.book')->get();

    $pdf = Pdf::loadView('payments.pdf', compact('payments'));

    return $pdf->download('payments_report_' . date('Ymd_His') . '.pdf');
}


}
