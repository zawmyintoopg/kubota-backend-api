@forelse($products as $product)
<div class="kbz-product-row" onclick="window.location='{{ route('items.edit', $product->id) }}'">
    <div class="kbz-left">
        {{ $product->pos_color  }}
        @if($product->pos_color)
            <div class="kbz-color" style="background-color:{{ $product->pos_color }};"></div>
        @elseif($product->image)
            <img src="{{ asset('storage/'.$product->image) }}" class="kbz-img">
        @else
            <div class="kbz-color" style="background:#adb5bd;">ITEM</div>
        @endif
    </div>
    <div class="kbz-middle">
        <div class="kbz-name">{{ $product->item_name }}</div>
        <div class="kbz-brand">{{ $product->brand_name ?? '-' }}</div>
        <div class="kbz-stock">Stock: {{ $product->on_hand_qty ?? 0 }}</div>
              
        </div>
    </div>
    <div class="kbz-right">
        {{ number_format($product->unit_price ?? $product->unit_price ?? 0,2) }} MMK
    </div>
</div>
@empty
<div class="text-center py-3 text-muted">No products found.</div>
@endforelse
