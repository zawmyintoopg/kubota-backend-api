@extends('layouts.master')

@section('title','Edit Product')

@section('content')
<div class="container mt-4">
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Edit Product & Variant</h4>
        <a href="{{ route('product_master.product') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
    
    <form action="{{ route('product_master.update', $product->variant_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Product Info -->
        <div class="card mb-3">
            <div class="card-header">Product Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="product_name" class="form-label">Product Name</label>
                    <input type="text" name="product_name" id="product_name" class="form-control" value="{{ $product->product_name }}">
                </div>

                <div class="mb-3">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select name="brand_id" id="brand_id" class="form-select">
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                {{ $brand->brand_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control">{{ $product->description }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Product Image</label>
                    <input type="file" name="image" id="image" class="form-control">
                    @if($product->image)
                        <img src="{{ asset('storage/'.$product->image) }}" alt="Product Image" class="mt-2" width="100">
                    @endif
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Product Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="Active" {{ $product->status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ $product->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Variant Info -->
        <div class="card mb-3">
            <div class="card-header">Variant Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="variant_name" class="form-label">Variant Name</label>
                    <input type="text" name="variant_name" id="variant_name" class="form-control" value="{{ $product->variant_name }}">
                </div>

                <div class="mb-3">
                    <label for="unit_id" class="form-label">Unit</label>
                    <select name="unit_id" id="unit_id" class="form-select">
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ $product->unit_id == $unit->id ? 'selected' : '' }}>
                                {{ $unit->unit_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="last_purchase_date" class="form-label">Last Purchase Date</label>
                    <input type="date" name="last_purchase_date" id="last_purchase_date" class="form-control" value="{{ $product->last_purchase_date }}">
                </div>

                <div class="mb-3">
                    <label for="last_purchase_price" class="form-label">Last Purchase Price</label>
                    <input type="number" step="0.01" name="last_purchase_price" id="last_purchase_price" class="form-control" value="{{ $product->last_purchase_price }}">
                </div>

                <div class="mb-3">
                    <label for="sell_price" class="form-label">Sell Price</label>
                    <input type="number" step="0.01" name="sell_price" id="sell_price" class="form-control" value="{{ $product->sell_price }}">
                </div>

                <div class="mb-3">
                    <label for="sku" class="form-label">SKU</label>
                    <input type="text" name="sku" id="sku" class="form-control" value="{{ $product->sku }}">
                </div>

                <div class="mb-3">
                    <label for="variant_status" class="form-label">Variant Status</label>
                    <select name="variant_status" id="variant_status" class="form-select">
                        <option value="Active" {{ $product->status == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ $product->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>
</div>
@endsection
