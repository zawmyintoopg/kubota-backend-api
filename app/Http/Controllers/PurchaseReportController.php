<?php
namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        $purchases = Purchase::all();

        return view('purchase_report.index', compact('purchases'));
    }
}
