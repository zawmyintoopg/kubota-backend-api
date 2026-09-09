<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    /**
     * Show variant page (iframe / popup)
     */
    public function index(Product $product)
    {
        // Load variants with unit relation
        $variants = \DB::table('product_variants')
            ->join('units', 'units.id', '=', 'product_variants.unit_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.product_id', $product->id)
            ->select(
                'product_variants.*',
                'units.id',
                'units.unit_name',
                'units.remarks',
                'units.to_base',
                'products.name as product_name'
            )
            ->orderBy('product_variants.unit_id')
            ->get();

        // Active units
        $units = Unit::where('status', 'Active')->get();

        return view('product_master.variant', compact('product', 'variants', 'units'));
    }

    /**
     * Store new variant (Unit + Sell Price)
     */


        private function generatesku()
        {
            $last = \App\Models\ProductVariant::orderBy('id','desc')->first();

            if (!$last) {
                return str_pad(1, 10, '0', STR_PAD_LEFT);
            }

            return str_pad(((int)$last->sku) + 1, 10, '0', STR_PAD_LEFT);
        }
        public function store(Request $request, Product $product)
        {
            // Validate input
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

            return back()->with('success', 'Variant added successfully.');
        }



    /**
     * Update existing variant (Sell Price only)
     */
    public function update(Request $request, ProductVariant $variant)
    {
        $data = $request->validate([
            'sellprice' => 'required|numeric|min:0',
            'variant_name' => 'required',
            'last_purchase_price' => 'required|numeric|min:0',
            'last_purchase_date' => 'nullable',
            'status' => 'required|boolean',
        ]);

        $variant->update([
            'sell_price' => $data['sellprice'],
            'variant_name' => 'required',
            'last_purchase_price' => 'required|numeric|min:0',
            'last_purchase_date' => 'nullable',
            'status' => $data['status'],
        ]);

        return back()->with('success', 'Variant updated successfully');
    }

    /**
     * Delete variant
     */
    public function destroy(ProductVariant $variant)
    {
        $variant->delete();
        return back()->with('success', 'Variant deleted successfully');
    }

    
}
