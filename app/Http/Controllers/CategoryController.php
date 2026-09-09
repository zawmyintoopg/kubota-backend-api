<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()
        ->paginate(10);        
        return view('product_master.category', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'addName' => 'required|string|max:100|unique:categories,name',
        ]);

        Category::create([
            'name'   => trim($request->addName),
            'status' => $request->addStatus ?? 'Inactive',
        ],[
        'addName.unique' => 'Category name already exists!'
        ]);

        return redirect()->route('product_master.category')
            ->with('success', 'Category created');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'addName' => 'required|string|max:100|unique:Categories,name,' . $id,
        ],[
        'addName.unique' => 'Category name already exists!'
        ]);

        Category::findOrFail($id)->update([
            'name'   => trim($request->addName),
            'status' => $request->addStatus ?? 'Inactive',
        ]);

        return redirect()->route('product_master.Category')
            ->with('success', 'Category Updated');
    }



    public function destroy($id)
    {
        Category::findOrFail($id)->delete();

        return redirect()
            ->route('product_master.category')
            ->with('success', 'Category deleted successfully');
    }

}

