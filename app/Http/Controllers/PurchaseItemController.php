<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseItemController extends Controller
{
    public function index()
    {
        $purchasesitem = PurchaseItem::with([
                'paymentType',
                'transactionType',
                'supplier'
            ])
            ->latest()
            ->paginate(10);

        $paymentTypes     = PaymentMethod::where('status','Active')->get();
        $transactionTypes = TransactionType::where('status','Active')->get();
        $suppliers        = Supplier::where('status','Active')->get();

        return view('purchase_master.purchase', compact(
            'purchases',
            'paymentTypes',
            'transactionTypes',
            'suppliers'
        ));
    }
}
