<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = customer::latest()->paginate(10);
        return view('sale_master.customer', compact('customers'));
    }

    /**
     * Store new customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:150|unique:customers,customer_name',
            'phone'          => 'nullable|string|max:30',
            'address'        => 'nullable|string',
            'contact_person' => 'nullable|string|max:100',
            'status'         => 'required|in:Active,Inactive',
        ]);

        // 🔢 Generate customer code (8 digits)
        $lastCode = customer::max('customer_code');
        $nextCode = $lastCode ? $lastCode + 1 : 10000001;

        customer::create([
            'customer_code'  => $nextCode,
            'customer_name'  => trim($request->customer_name),
            'phone'          => $request->phone,
            'address'        => $request->address,
            'contact_person' => $request->contact_person,
            'status'         => $request->status,
        ]);

        return redirect()
            ->route('sale_master.customer')
            ->with('success', 'customer created successfully');
    }

    /**
     * Update customer
     */
    public function update(Request $request, $id)
    {
        $customer = customer::findOrFail($id);

        $request->validate([
            'customer_name'  => 'required|string|max:150|unique:customers,customer_name,' . $customer->id,
            'phone'          => 'nullable|string|max:30',
            'address'        => 'nullable|string',
            'contact_person' => 'nullable|string|max:100',
            'status'         => 'required|in:Active,Inactive',
        ]);

        $customer->update([
            'customer_name'  => trim($request->customer_name),
            'phone'          => $request->phone,
            'address'        => $request->address,
            'contact_person' => $request->contact_person,
            'status'         => $request->status,
        ]);

        return redirect()
            ->route('sale_master.customer')
            ->with('success', 'customer updated successfully');
    }

    /**
     * Delete customer
     */
    public function destroy($id)
    {
        customer::findOrFail($id)->delete();

        return redirect()
            ->route('sale_master.customer')
            ->with('success', 'customer deleted successfully');
    }
}
