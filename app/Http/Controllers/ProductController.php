<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;


class ProductController extends Controller
{
    /* ================= INDEX ================= */
    public function index(Request $request)
{
    $q = $request->q ?? '';

    $query = DB::table('products as p')
        ->leftJoin('categories as c', 'c.id', '=', 'p.category_id')
        ->leftJoin('brands as b', 'b.id', '=', 'p.brand_id')
        ->leftJoin('product_variants as v', 'v.product_id', '=', 'p.id')
        ->leftJoin('units as u', 'u.id', '=', 'v.unit_id')
        ->select(
            'p.*',
            'v.variant_name',
            'v.sell_price',
            'v.sku',
            'c.name as category_name',
            'b.brand_name as brand_name',
            'u.unit_name'
        )
        ->orderBy('p.id', 'desc');

    // Live search filter
    if($q){
        $query->where('p.item_name', 'like', "%{$q}%");
    }

    $products = $query->paginate(5);

    // AJAX response for live search & pagination
    if($request->ajax()){
        $rows = view('product_master.partials.product_rows', compact('products'))->render();
        $pagination = view('pagination::bootstrap-5', ['paginator'=>$products])->render();
        return response()->json(['rows'=>$rows, 'pagination'=>$pagination]);
    }

    $categories = DB::table('categories')->get();
    $brands     = DB::table('brands')->get();
    $units      = DB::table('units')->get();

    return view('product_master.list', compact(
        'products',
        'categories',
        'brands',
        'units'
    ));
}

// Autocomplete search
public function search(Request $request)
{
    $q = $request->q ?? '';
    if(!$q) return response()->json([]);

    $products = DB::table('products')
        ->where('item_name','like',"%{$q}%")
        ->select('id','item_name')
        ->limit(10)
        ->get();

    return response()->json($products);
}
    public function show(){
        
    }
    public function productcreate(){
        $categories = Category::get();
        $brands = Brand::get();
        $units = Unit::get();
        return view('product_master.create',compact('categories','brands','units'));
    }
    //product edit
    public function productedit(Request $request){
        $variants_row = DB::table('products as p')
                    ->leftJoin('product_variants as pv' , 'p.id', '=' ,'pv.product_id')
                    ->leftJoin('units as u','pv.unit_id','=','u.id');
        $brands = Brand::get();
        $units = Unit::get();
        return view('product_master.product_create',compact('categories','brands','units'));
    }    
    public function quickCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = Category::create(['name' => $request->name]);
        return response()->json($category);
    }

    public function quickBrand(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $brand = Brand::create(['name' => $request->name]);
        return response()->json($brand);
    }

    public function quickUnit(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $unit = Unit::create(['name' => $request->name]);
        return response()->json($unit);
    }
    /* ================= STORE ================= */
    private function generateProductCode()
    {
        $last = Product::orderBy('id','desc')->first();
        return $last ? str_pad(((int)$last->product_code) + 1, 10, '0', STR_PAD_LEFT)
                     : str_pad(1, 10, '0', STR_PAD_LEFT);
    }

    public function store(Request $request)
    {
    
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Prevent duplicate name for same category
                Rule::unique('products')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id);
                }),
            ],
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            //'base_unit_id' => 'nullable|exists:units,id',
            'description' => 'testing',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ],[
            'name.unique' => 'Product with this name already exists in the selected category.'
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = time().'_'.$request->image->getClientOriginalName();
            $request->image->move(public_path('images'), $imageName);
        }

        Product::create([
        // 'product_code' => $this->generateProductCode(),
            'name'         => $request->name,
            'category_id'  => $request->category_id,
            'brand_id'     => $request->brand_id,
        // 'base_unit_id' => $request->base_unit_id,
            'description'  => $request->description,
            'image'        => $imageName,
            'status'       => 'Active'//$request->status ?? 'Inactive',
        ]);
        $data = $request->validate([
                        'unit_id' => [
                        'required',
                        'exists:units,id',
            ],
            'variant_name' => [
                'required',
                // Ensure combination of product_id + unit_id + variant_name is unique
                Rule::unique('product_variants')->where(function ($query) use ($product, $request) {
                    return $query->where('product_id', $product->id)
                                ->where('unit_id', $request->unit_id)
                                ->where('variant_name', $request->variant_name);
                }),
            ],
            'last_purchase_price' => 'required',
            'last_purchase_date' => 'nullable|date',
            'sell_price' => 'required|numeric|min:0',
        ]);

        // Create the variant
        $product->variants()->create([
            'unit_id' => $data['unit_id'],
            'sku' => $this->generatesku(),
            'variant_name' => $data['variant_name'],
            'last_purchase_price' => $data['last_purchase_price'],
            'last_purchase_date' => $data['last_purchase_date'] ?? null,
            'sell_price' => $data['sell_price'],
            'status' => 'active',
        ]);
        return redirect()->route('product_master.product')->with('success','Product created successfully');
    }

    /* ================= UPDATE ================= */


    public function edit($id)
    {
        // Fetch product with category, brand, and unit using left joins
        $product = \DB::table('items as p')
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


    // ----------------- UPDATE PRODUCT -----------------
    public function update(Request $request, $id)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'unit_qty' => 'required|numeric|min:1',
            'on_hand_qty' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($id);

        // Update basic fields
        $product->item_name = $request->item_name;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id ?? null;
        $product->unit_id = $request->unit_id;
        $product->unit_qty = $request->unit_qty;
        $product->on_hand_qty = $request->on_hand_qty;
        $product->unit_price = $request->unit_price;
        $product->cost = $request->cost;
        $product->pos_color = $request->pos_color_value ?? $product->pos_color;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::exists('public/'.$product->image)) {
                Storage::delete('public/'.$product->image);
            }

            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }

        $product->save();

        return redirect()->route('product_master.product')->with('success','Product updated successfully!');
    }

    /* ================= DESTROY ================= */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return redirect()->route('product_master.product')->with('success','Product deleted successfully');
    }

    /* ================= PRICE HISTORY ================= */
    public function priceHistoryIndex(Request $request)
    {
        $query = DB::table('product_price_histories as h')
            ->join('products as p', 'p.id', '=', 'h.product_id')
            ->join('product_variants as pv', 'pv.id', '=', 'h.product_variant_id')
            ->join('units as u', 'u.id', '=', 'pv.unit_id')
            ->leftJoin('users as us', 'us.id', '=', 'h.changed_by')
            ->select(
                'h.id',
                'p.product_code',
                'p.name as product_name',
                'pv.id as variant_id',
                'u.unit_name',
                'h.old_price',
                'h.new_price',
                'h.action',
                'h.changed_at',
                'us.name as changed_by'
            );

        if ($request->from) $query->whereDate('h.changed_at','>=',$request->from);
        if ($request->to)   $query->whereDate('h.changed_at','<=',$request->to);

        $histories = $query->orderByDesc('h.changed_at')->paginate(20)->withQueryString();

        return view('product_master.price_history', compact('histories'));
    }
}
