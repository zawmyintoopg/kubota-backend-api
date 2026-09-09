<?php
namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->paginate(5);
        return view('product_master.unit', compact('units'));
    }

    public function store(Request $request)
    {
       // dd('Ok');
        try {
            $request->validate([
                'unit_name' => 'required|string|max:100|unique:units,unit_name',
                'to_base'   => 'required|numeric|min:1',
                'status'    => 'nullable',
                'remarks'   => 'nullable',
            ]);

            $unit = Unit::create([
                'unit_name' => trim($request->unit_name),
                'to_base'   => $request->to_base,
                'status'    => $request->status ?? 'inactive',
                'remarks'   => $request->remarks,
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'unit_id' => $unit->id,
                ]);
            }

            return redirect()
                ->route('product_master.unit')
                ->with('success', 'Unit created successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 500);
            }
            throw $e;
        }
    }



    public function update(Request $request, $id)
    {
        $request->validate([
            'unit_name' => 'required|string|max:100|unique:units,unit_name,' . $id,
            'to_base'   => 'required|numeric|min:1',
            'status'    => 'nullable',
            'remarks'   => 'nullable',
            
        ]);

        Unit::findOrFail($id)->update([
            'unit_name' => trim($request->unit_name),
            'to_base'   => $request->to_base,
            'status'    => $request->status ?? 'inactive',
            'remarks'   => $request->remarks,
        ]);

        return redirect()
            ->route('product_master.unit')
            ->with('success', 'Unit updated successfully');
    }


    public function destroy($id)
    {
        Unit::findOrFail($id)->delete();

        return redirect()->route('product_master.unit')
            ->with('success', 'Unit deleted successfully');
    }
}
