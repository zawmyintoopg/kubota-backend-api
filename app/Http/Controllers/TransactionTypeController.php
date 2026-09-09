<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionType;
use Illuminate\Validation\Rule;

class TransactionTypeController extends Controller
{
    public function index()
    {
        $transactionTypes = TransactionType::latest()->paginate(10);
        return view('common.transaction_type', compact('transactionTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_code' => 'required|unique:transaction_types,transaction_code',
            'transaction_name' => 'required'
        ]);

        TransactionType::create([
            'transaction_code' => strtoupper(trim($request->transaction_code)),
            'transaction_name' => trim($request->transaction_name),
            'status'           => $request->status ?? 'Active'
        ]);

        return redirect()->back()->with('success','Transaction Type created');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'transaction_code' => [
                'required',
                Rule::unique('transaction_types','transaction_code')->ignore($id)
            ],
            'transaction_name' => 'required'
        ]);

        TransactionType::where('id',$id)->update([
            'transaction_code' => strtoupper(trim($request->transaction_code)),
            'transaction_name' => trim($request->transaction_name),
            'status'           => $request->status ?? 'Active'
        ]);

        return redirect()->back()->with('success','Transaction Type updated');
    }

    public function destroy($id)
    {
        TransactionType::findOrFail($id)->delete();
        return redirect()->back()->with('success','Transaction Type deleted');
    }
}
