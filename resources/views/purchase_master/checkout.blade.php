@extends('layouts.master')
@section('title','Purchase POS - Checkout')

@section('content')
@php
    $cart = session('purchase_cart', []);
    $supplier_id = session('purchase_supplier', null);
@endphp

<h5>Checkout - Supplier: {{ optional($suppliers->find($supplier_id))->supplier_name }}</h5>

<form method="POST" action="{{ route('purchase_master.save_final') }}">
@csrf
<input type="hidden" name="supplier_id" value="{{ $supplier_id }}">
<input type="hidden" name="cart_items" value='@json($cart)'>

<div class="cart-items mb-2">
    @foreach($cart as $index => $item)
    <div class="cart-item d-flex justify-content-between align-items-center p-2 mb-1 border rounded">
        <div>
            <strong>{{ $item['name'] }}</strong> ({{ $item['unit'] }})
        </div>
        <div class="d-flex gap-1 align-items-center">
            <input type="number" name="qty[{{ $index }}]" class="form-control form-control-sm" value="{{ $item['qty'] }}" min="1">
            <span>{{ number_format($item['buy'],2) }} Ks</span>
            <span>{{ number_format($item['buy'] * $item['qty'],2) }} Ks</span>
        </div>
    </div>
    @endforeach
</div>

<div class="summary border p-2 rounded">
    <p>Sub Total: <span id="subTotal">0 Ks</span></p>
    <input type="number" id="discountField" class="form-control mb-1" placeholder="Discount" value="0" name="discount">
    <input type="number" id="taxField" class="form-control mb-1" placeholder="Tax" value="0" name="tax">
    <p class="fw-bold">Grand Total: <span id="grandTotal">0 Ks</span></p>
    <input type="number" id="paidField" class="form-control mb-1" placeholder="Paid" value="0" name="paid_amount">
    <p>Balance: <span id="balance">0 Ks</span></p>
    <button type="submit" class="btn btn-success w-100 mt-2"><i class="fas fa-print"></i> Save & Print</button>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const qtyInputs = document.querySelectorAll('input[name^="qty"]');
    const discountEl = document.getElementById('discountField');
    const taxEl = document.getElementById('taxField');
    const paidEl = document.getElementById('paidField');
    const subTotalEl = document.getElementById('subTotal');
    const grandTotalEl = document.getElementById('grandTotal');
    const balanceEl = document.getElementById('balance');

    function updateSummary(){
        let subtotal=0;
        qtyInputs.forEach((input,i)=>{
            const buy = parseFloat({!! json_encode(array_column($cart,'buy')) !!}[i]);
            const qty = parseFloat(input.value) || 1;
            subtotal += buy*qty;
        });
        const discount = parseFloat(discountEl.value)||0;
        const tax = parseFloat(taxEl.value)||0;
        const grand = subtotal-discount+tax;
        const paid = parseFloat(paidEl.value)||0;
        const balance = grand-paid;
        subTotalEl.innerText = subtotal.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})+' Ks';
        grandTotalEl.innerText = grand.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})+' Ks';
        balanceEl.innerText = balance.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2})+' Ks';
    }

    qtyInputs.forEach(input=>input.addEventListener('input',updateSummary));
    discountEl.addEventListener('input',updateSummary);
    taxEl.addEventListener('input',updateSummary);
    paidEl.addEventListener('input',updateSummary);

    updateSummary();
});
</script>
@endsection
