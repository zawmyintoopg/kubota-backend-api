<?php
namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index()
    {
        $sizes = Size::latest()
        ->paginate(5);     
        //dd($sizes->toArray());   
        return view('product_master.size', compact('sizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'addsizeName' => 'required|string|max:100|unique:sizes,size_name',
            'addDescription' => 'required',
        ]);

        Size::create([
            'size_name'   => trim($request->addsizeName),
            'description'   => trim($request->addDescription),
            'status' => $request->AddStatus ?? 'Inactive',
        ]);

        return redirect()->route('product_master.size')
            ->with('success', 'Size created');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'addsizeName' => 'required|string|max:100|unique:sizes,size_name,' . $id,
        ]);

        Size::findOrFail($id)->update([
            'size_name'   => trim($request->addsizeName),
            'description'   => trim($request->addDescription),
            'status' => $request->AddStatus ?? 'Inactive',
        ]);

        return redirect()->route('product_master.size')
            ->with('success', 'Size Updated');
    }



    public function destroy($id)
    {
        Size::findOrFail($id)->delete();

        return redirect()
            ->route('product_master.size')
            ->with('success', 'Size deleted successfully');
    }

    public function updateStatus(Request $request, Size $size)
    {
        $size->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'status'  => $size->status
        ]);
    }
}

