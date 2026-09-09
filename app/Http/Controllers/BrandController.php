<?php
namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()
        ->paginate(10);        
        return view('product_master.brand', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'addName' => 'required|string|max:100|unique:brands,brand_name',
        ]);

        Brand::create([
            'brand_name'   => trim($request->addName),
            'status' => $request->addStatus ?? 'Inactive',
        ],[
        'addName.unique' => 'Brand name already exists!'
        ]);

        return redirect()->route('product_master.brand')
            ->with('success', 'Brand created');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'addName' => 'required|string|max:100|unique:brands,brand_name,' . $id,
        ],[
        'addName.unique' => 'Brand name already exists!'
        ]);

        Brand::findOrFail($id)->update([
            'brand_name'   => trim($request->addName),
            'status' => $request->addStatus ?? 'Inactive',
        ]);

        return redirect()->route('product_master.brand')
            ->with('success', 'Brand Updated');
    }



    public function destroy($id)
    {
        Brand::findOrFail($id)->delete();

        return redirect()
            ->route('product_master.brand')
            ->with('success', 'Brand deleted successfully');
    }

}

