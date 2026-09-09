<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{

    public function index(Request $request)
    {
        $query = $request->q ?? '';

        $products = DB::table('items as i')
            ->leftJoin('categories as c', 'c.id', '=', 'i.category_id')
            ->leftJoin('brands as b', 'b.id', '=', 'i.brand_id')
            ->leftJoin('units as u', 'u.id', '=', 'i.unit_id')
            ->select(
                'i.*',                
                'c.name as category_name',
                'u.unit_name',
                'b.brand_name as brand_name'
            )
            ->when($query, function($q) use ($query) {
                return $q->where('i.item_name', 'like', "%{$query}%");
            })
            ->orderBy('i.id', 'desc')
            ->paginate(5);

        $categories = DB::table('categories')->get();
        $brands     = DB::table('brands')->get();
        $units      = DB::table('units')->get();

        // If AJAX request (for live grid update)
        if ($request->ajax()) {
            $rows = view('product_master.partials.product_rows', compact('products'))->render();
            $pagination = view('pagination::bootstrap-5', ['paginator' => $products])->render();

            return response()->json([
                'rows' => $rows,
                'pagination' => $pagination
            ]);
        }

        return view('product_master.list', compact(
            'products',
            'categories',
            'brands',
            'units'
        ));
    }

    //create item
    public function create(){
        $categories = Category::get();
        $brands = Brand::get();
        $units = Unit::get();
        return view('product_master.product',compact('categories','brands','units'));
    }
    //create item
    public function edit($id)
    {
        // Fetch product with category, brand, and unit using left joins
        $product = DB::table('items as p')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->leftJoin('brands as b', 'p.brand_id', '=', 'b.id')
            ->leftJoin('units as u', 'p.unit_id', '=', 'u.id')
            ->select(
                'p.*',
                'c.name as category_name',
                'b.brand_name',
                'u.unit_name',
                'u.to_base as unit_to_base'
            )
            ->where('p.id', $id)
            ->first();

        if (!$product) {
            abort(404, 'Product not found');
        }

        // Load all categories, brands, units for dropdowns
        $categories = \DB::table('categories')->orderBy('name')->get();
        $brands     = \DB::table('brands')->orderBy('brand_name')->get();
        $units      = \DB::table('units')->orderBy('unit_name')->get();

        return view('product_master.edit', compact('product','categories','brands','units'));
    }

    // Update product
    public function update(Request $request, $id)
    {
      // dd($request->toArray());
        $product = Item::findOrFail($id);
        //dd($product->toArray());
        // Validate request
        
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'cost' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'unit_qty' => 'required|numeric|min:1',
            'unit_id' => 'required|exists:units,id',
            'on_hand_qty' => 'required|numeric|min:0',
            'pos_type' => 'required|in:image,color',
            'pos_color' => 'nullable|string|max:7', // e.g. #ff0000
            'image' => 'nullable|image|max:2048', // optional image upload
        ]);
        //dd($request->toArray());
        // Collect fillable data
        $data = $request->only([
            'item_name', 'category_id', 'brand_id', 'cost',
            'unit_price', 'unit_qty', 'unit_id', 'on_hand_qty',
            'pos_type', 'pos_color','status'
        ]);
         //dd($request->toArray());
        // Handle image upload
        // if($request->hasFile('image')){
        //     // Delete old image if exists
        //     if($product->image && Storage::exists($product->image)){
        //         Storage::delete($product->image);
        //     }
        //     $path = $request->file('image')->store('products', 'public');
        //     $data['image'] = $path;
        // }

        // Update product
        $product->update($data);

        return redirect()->route('items.list')->with('success', 'Product updated successfully!');
    }


    // API for last item code (used in create page JS)
    public function lastCode() {
        $last = Product::latest('id')->first();
        return response()->json([
            'lastCode' => $last ? $last->item_code : 10000000
        ]);
    }
     
    // ItemController.php
    public function search(Request $request)
    {
        $q = $request->q;

        // Only search top 5 results for speed
        $products = DB::table('items')
            ->where('item_name', 'like', "%{$q}%")
            ->orWhere('item_code', 'like', "%{$q}%")
            ->limit(5)
            ->get(['id', 'item_name']);

        return response()->json($products);
    }


    public function store(Request $request)
    {
        $request->validate([
            'item_name'   => 'required|string|max:255',
            'category_id' => 'required',
            'unit_id'     => 'required',
            'unit_qty'    => 'required|numeric|min:1',
            'on_hand_qty' => 'required|numeric|min:0',
            'unit_price'  => 'required|numeric|min:0',
            'cost'        => 'required|numeric|min:0',
        ]);
    //edit item
   
        // Generate 8-digit item code
        $lastItem = Item::orderBy('id','desc')->first();
        $lastCode = $lastItem ? (int)$lastItem->item_code : 10000000;
        $itemCode = str_pad($lastCode + 1, 8, '0', STR_PAD_LEFT);

        $posType = $request->input('pos_color') ? 'color' : 'image';
        $imagePath = null;
        if($request->hasFile('image') && $posType === 'image') {
            $imagePath = $request->file('image')->store('items','public');
        }

        Item::create([
            'item_code'   => $itemCode,
            'item_name'   => $request->item_name,
            'category_id' => $request->category_id,
            'brand_id'    => $request->brand_id,
            'unit_id'     => $request->unit_id,
            'unit_qty'    => $request->unit_qty,
            'on_hand_qty' => $request->on_hand_qty,
            'unit_price'  => $request->unit_price,
            'cost'        => $request->cost,
            'image'       => $imagePath,
            'pos_color'   => $request->pos_color,
        ]);

        // Redirect back with success message and reset flag
        return redirect()->route('items.create')
                        ->with('success', 'Product saved successfully!')
                        ->with('resetForm', true);
    }
    public function deactivate(Item $product)
    {
        // Permission check
        // if (!auth()->user()->can('delete product')) {
        //     abort(403);
        // }

        // Toggle status (disable / enable)
        $product->status = 'disabled';//$product->status === 'active' ? 'disabled' : 'active';
        $product->save();

        $message = $product->status === 'active' 
            ? 'Product enabled successfully' 
            : 'Product disabled successfully';

        return redirect()->back()->with('success', $message);
    }


    // -------------------- Generate next 8-digit code --------------------
    private function generateItemCode()
    {
        $lastCode = Item::max('item_code');
        if(!$lastCode) $lastCode = 10000000;
        return str_pad($lastCode + 1, 8, '0', STR_PAD_LEFT);
    }
}
