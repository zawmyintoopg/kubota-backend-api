<?php
namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::latest()
        ->paginate(5);        
        return view('product_master.color', compact('colors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'addcolorName' => 'required|string|max:100|unique:colors,color_name',            
        ]);

        Color::create([
            'color_name'  => trim($request->addcolorName),           
            'status' => $request->AddStatus ?? 'Inactive',
        ]);

        return redirect()->route('product_master.color')
            ->with('success', 'Color created');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'addcolorName' => 'required|string|max:100|unique:colors,color_name,' . $id,
        ]);

        Color::findOrFail($id)->update([
            'color_name'   => trim($request->addcolorName),           
            'status' => $request->AddStatus ?? 'Inactive',
        ]);

        return redirect()->route('product_master.color')
            ->with('success', 'Color Updated');
    }



    public function destroy($id)
    {
        Color::findOrFail($id)->delete();

        return redirect()
            ->route('product_master.color')
            ->with('success', 'Color deleted successfully');
    }

    public function updateStatus(Request $request, Color $color)
    {
        $color->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'status'  => $color->status
        ]);
    }
}

