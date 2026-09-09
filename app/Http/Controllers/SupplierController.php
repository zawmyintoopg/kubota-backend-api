<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display supplier listing
     */
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('purchase_master.supplier', compact('suppliers'));
    }

    /**
     * Store new supplier
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_name'  => 'required|string|max:150|unique:suppliers,supplier_name',
            'phone'          => 'nullable|string|max:30',
            'address'        => 'nullable|string',
            'contact_person' => 'nullable|string|max:100',
            'status'         => 'required|in:Active,Inactive',
        ]);

        // 🔢 Generate supplier code (8 digits)
        $lastCode = Supplier::max('supplier_code');
        $nextCode = $lastCode ? $lastCode + 1 : 10000001;

        Supplier::create([
            'supplier_code'  => $nextCode,
            'supplier_name'  => trim($request->supplier_name),
            'phone'          => $request->phone,
            'address'        => $request->address,
            'contact_person' => $request->contact_person,
            'status'         => $request->status,
        ]);

        return redirect()
            ->route('purchase_master.supplier')
            ->with('success', 'Supplier created successfully');
    }

    /**
     * Update supplier
     */
    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $request->validate([
            'supplier_name'  => 'required|string|max:150|unique:suppliers,supplier_name,' . $supplier->id,
            'phone'          => 'nullable|string|max:30',
            'address'        => 'nullable|string',
            'contact_person' => 'nullable|string|max:100',
            'status'         => 'required|in:Active,Inactive',
        ]);

        $supplier->update([
            'supplier_name'  => trim($request->supplier_name),
            'phone'          => $request->phone,
            'address'        => $request->address,
            'contact_person' => $request->contact_person,
            'status'         => $request->status,
        ]);

        return redirect()
            ->route('purchase_master.supplier')
            ->with('success', 'Supplier updated successfully');
    }

    /**
     * Delete supplier
     */
    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();

        return redirect()
            ->route('purchase_master.supplier')
            ->with('success', 'Supplier deleted successfully');
    }
}
