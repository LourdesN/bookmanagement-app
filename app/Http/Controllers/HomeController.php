<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Sale;
use App\Models\Inventory;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
{
    $this->middleware('auth');

    $totalbooks = Book::count();
    $totalsales = Sale::sum('total');
    $totalinventory = Inventory::sum('quantity');
    
    $totalusers = User::count();

    $formattedTotal = $this->formatNumber($totalsales);

    return view('home', compact('totalbooks', 'totalsales', 'formattedTotal', 'totalinventory', 'totalusers'));
}

private function formatNumber($number)
{
    if ($number >= 1000000000) {
        return round($number / 1000000000, 1) . 'B';
    } elseif ($number >= 1000000) {
        return round($number / 1000000, 1) . 'M';
    } elseif ($number >= 1000) {
        return round($number / 1000, 1) . 'k';
    }

    return $number;
}
}