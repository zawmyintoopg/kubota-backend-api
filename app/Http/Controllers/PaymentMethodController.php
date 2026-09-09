<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMethod;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods = PaymentMethod::orderBy('id','desc')
            ->paginate(10);

        return view('common.payment_method', compact('paymentMethods'));
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        $request->validate([
            'payment_short_code'   => 'required|string|max:50|unique:payment_methods,payment_short_code',
            'payment_description'  => 'required|string|max:255',
        ]);

        PaymentMethod::create([
            'payment_short_code'  => strtoupper(trim($request->payment_short_code)),
            'payment_description' => trim($request->payment_description),
            'status'              => $request->status ?? 'Inactive',
        ]);

        return redirect()
            ->route('common.payment_method')
            ->with('success','Payment Method created successfully');
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, $id)
    {
        $request->validate([
            'payment_short_code'  => 'required|string|max:50|unique:payment_methods,payment_short_code,' . $id,
            'payment_description' => 'required|string|max:255',
        ]);

        $pm = PaymentMethod::findOrFail($id);

        $pm->update([
            'payment_short_code'  => strtoupper(trim($request->payment_short_code)),
            'payment_description' => trim($request->payment_description),
            'status'              => $request->status ?? 'Inactive',
        ]);

        return redirect()
            ->route('common.payment_method')
            ->with('success','Payment Method updated successfully');
    }

    /* ================= DELETE ================= */
    public function destroy($id)
    {
        PaymentMethod::findOrFail($id)->delete();

        return redirect()
            ->route('common.payment_method')
            ->with('success','Payment Method deleted successfully');
    }
}
