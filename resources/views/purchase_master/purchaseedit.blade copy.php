<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Purchase</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body{background:#f4f6f9}
.table td{vertical-align:middle}
.product-row{cursor:pointer}
.product-row:hover{background:#eef5ff}
.summary-card{position:sticky;top:80px}
</style>
</head>
<body>

<form id="purchaseForm" method="POST" action="{{ route('purchase.update',$purchase->id) }}">
@csrf
@method('PUT')

<input type="hidden" name="sub_total" id="sub_total_input">
<input type="hidden" name="discount" id="discount_input">
<input type="hidden" name="tax" id="tax_input">
<input type="hidden" name="grand_total" id="grand_total_input">
<input type="hidden" name="paid_amount" id="paid_input">
<input type="hidden" name="balance_amount" id="balance_input">

<!-- HEADER -->
<div class="container-fluid bg-warning py-2 sticky-top">
<div class="row g-2 align-items-end">
<div class="col-md-3">
<label class="small">Purchase Date</label>
<input type="date" name="purchase_date"
value="{{ $purchase->purchase_date }}"
class="form-control form-control-sm">
</div>
<div class="mb-2">
<label>Supplier</label>
<select name="supplier_id" class="form-select form-select-sm">
@foreach($suppliers as $s)
<option value="{{ $s->id }}" @selected($purchase->supplier_id==$s->id)>
{{ $s->supplier_name }}
</option>
@endforeach
</select>
</div>

<div class="mb-2">
<label>Payment</label>
<select name="payment_type_id" class="form-select form-select-sm">
@foreach($paymentMethods as $p)
<option value="{{ $p->id }}" @selected($purchase->payment_type_id==$p->id)>
{{ $p->payment_short_code }}
</option>
@endforeach
</select>
</div>

<div class="mb-2">
<label>Transaction</label>
<select name="transaction_type_id" class="form-select form-select-sm">
@foreach($transactionTypes as $t)
<option value="{{ $t->id }}" @selected($purchase->transaction_type_id==$t->id)>
{{ $t->transaction_name }}
</option>
@endforeach
</select>
</div>

<div class="col-md-3 ms-auto">
<button type="button" class="btn btn-dark btn-sm w-100"
data-mdb-toggle="modal" data-mdb-target="#productModal">
<i class="fas fa-plus"></i> Add Product
</button>
</div>
</div>
</div>

<!-- MAIN -->
<div class="container-fluid mt-3">
<div class="row g-3">

<!-- ================= CART COLUMN ================= -->
<div class="col-lg-8">
<div class="card">
<div class="card-body p-2">

<div class="d-flex justify-content-between mb-2">
<strong>Cart Items</strong>
<strong>Sub Total : <span id="cartSubTotal">0 Ks</span></strong>
</div>

<table class="table table-bordered table-sm">
<thead class="table-warning text-center">
<tr>
<th>#</th>
<th>Code</th>
<th>Product</th>
<th>Unit</th>
<th width="80">Qty</th>
<th>Buy</th>
<th>Amount</th>
<th>✕</th>
</tr>
</thead>
<tbody id="cartBody"></tbody>
</table>

</div>
</div>
</div>

<!-- ================= SUMMARY COLUMN ================= -->
<div class="col-lg-4">
<div class="card summary-card">
<div class="card-body">

<h6 class="fw-bold mb-3 text-success">Purchase Summary</h6>
<ul class="list-group list-group-sm mb-2">
<li class="list-group-item d-flex justify-content-between">
<span>Sub Total</span><strong id="subTotal">0 Ks</strong>
</li>

<li class="list-group-item">
<label>Discount</label>
<input id="discountField" class="form-control form-control-sm text-end"
value="{{ $purchase->discount ?? 0 }}" oninput="calculateSummary()">
</li>

<li class="list-group-item">
<label>Tax</label>
<input id="taxField" class="form-control form-control-sm text-end"
value="{{ $purchase->tax ?? 0 }}" oninput="calculateSummary()">
</li>

<li class="list-group-item fw-bold d-flex justify-content-between">
<span>Grand Total</span><span id="grandTotal">0 Ks</span>
</li>

<li class="list-group-item">
<label>Paid</label>
<input id="paidField" class="form-control form-control-sm text-end"
value="{{ $purchase->paid_amount ?? 0 }}" oninput="calculateSummary()">
</li>

<li class="list-group-item fw-bold d-flex justify-content-between">
<span>Balance</span><span id="balance">0 Ks</span>
</li>
</ul>

<div id="itemsInputs"></div>

<button type="button" class="btn btn-success w-100 mt-2" onclick="savePurchase()">
<i class="fas fa-save"></i> Update Purchase
</button>

</div>
</div>
</div>

</div>
</div>

<!-- ================= PRODUCT MODAL ================= -->
<div class="modal-body">
<table class="table table-bordered table-sm">
<thead class="table-light text-center">
<tr>
<th>Code</th><th>Name</th><th>Unit</th><th>Buy</th>
</tr>
</thead>
<tbody>
@foreach($products as $p)
@foreach($p->variants as $v)
<tr class="product-row"
data-product-id="{{ $p->id }}"
data-product-code="{{ $p->product_code }}"
data-product-name="{{ $p->name }}"
data-variant-id="{{ $v->id }}"
data-price="{{ $v->buyprice }}">
<td>{{ $p->product_code }}</td>
<td>{{ $p->name }}</td>
<td>{{ $v->unit->unit_name }}</td>
<td class="text-end">{{ number_format($v->buyprice) }}</td>
</tr>
@endforeach
@endforeach
</tbody>
</table>
</div>
</div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

<script>
let cart = @json($items);

/* NORMALIZE */
cart = cart.map(i => ({
product_id:Number(i.product_id),
variant_id:Number(i.product_variant_id),
product_code:i.product_code,
product_name:i.product_name,
qty:Number(i.qty),
buyprice:Number(i.buyprice)
}));

const cartBody=document.getElementById('cartBody');
const fmt=n=>new Intl.NumberFormat().format(n)+' Ks';

/* VARIANTS MAP */
const variantsByProduct={};
@foreach($products as $p)
variantsByProduct[{{ $p->id }}]=[
@foreach($p->variants as $v)
{id:{{ $v->id }},unit:"{{ $v->unit->unit_name }}",price:{{ $v->buyprice }}},
@endforeach
];
@endforeach

document.addEventListener('DOMContentLoaded',()=>{
bindProductRows();
render();
});

/* PRODUCT CLICK */
function bindProductRows(){
document.querySelectorAll('.product-row').forEach(row=>{
row.onclick=()=>{
const pid=+row.dataset.productId;
const vid=+row.dataset.variantId;

const idx=cart.findIndex(c=>c.product_id===pid&&c.variant_id===vid);

if(idx!==-1){
Swal.fire({
title:'Already Exists',
text:'You are already exist, Do you want to add more?',
icon:'question',
showCancelButton:true,
confirmButtonText:'Yes'
}).then(r=>{
if(r.isConfirmed){
cart[idx].qty+=1;
render();
}
});
return;
}

cart.push({
product_id:pid,
variant_id:vid,
product_code:row.dataset.code,
product_name:row.dataset.name,
qty:1,
buyprice:+row.dataset.price
});
render();
};
});
}

/* UNIT CHANGE */
function changeUnit(i,vid){
vid=+vid;
if(cart.find((c,idx)=>idx!==i&&c.product_id===cart[i].product_id&&c.variant_id===vid)){
Swal.fire('Duplicate','Same product & unit','warning');
render();return;
}
const v=variantsByProduct[cart[i].product_id].find(x=>x.id===vid);
cart[i].variant_id=v.id;
cart[i].buyprice=v.price;
render();
}

/* RENDER */
function render(){
cartBody.innerHTML='';
cart.forEach((c,i)=>{
const opts=variantsByProduct[c.product_id]
.map(v=>`<option value="${v.id}" ${v.id===c.variant_id?'selected':''}>${v.unit}</option>`).join('');

cartBody.insertAdjacentHTML('beforeend',`
<tr>
<td>${i+1}</td>
<td>${c.product_code}</td>
<td>${c.product_name}</td>
<td><select class="form-select form-select-sm"
onchange="changeUnit(${i},this.value)">${opts}</select></td>
<td><input type="number" min="1" value="${c.qty}"
class="form-control form-control-sm"
oninput="cart[${i}].qty=this.value||1;calculateSummary()"></td>
<td class="text-end">${fmt(c.buyprice)}</td>
<td class="text-end">${fmt(c.qty*c.buyprice)}</td>
<td><button class="btn btn-danger btn-sm"
onclick="cart.splice(${i},1);render()">✕</button></td>
</tr>
`);
});
calculateSummary();
}

/* SUMMARY */
function calculateSummary(){
const sub=cart.reduce((s,c)=>s+(c.qty*c.buyprice),0);
const discount=+discountField.value||0;
const tax=+taxField.value||0;
const paid=+paidField.value||0;
const grand=sub-discount+tax;
const bal=grand-paid;

cartSubTotal.innerText=subTotal.innerText=fmt(sub);
grandTotal.innerText=fmt(grand);
balance.innerText=fmt(bal);

sub_total_input.value=sub;
discount_input.value=discount;
tax_input.value=tax;
grand_total_input.value=grand;
paid_input.value=paid;
balance_input.value=bal;
}

/* SAVE */
function savePurchase(){
if(cart.some(c=>!c.variant_id)){
Swal.fire('Error','Unit missing in cart','error');return;
}
itemsInputs.innerHTML='';
cart.forEach((c,i)=>{
itemsInputs.insertAdjacentHTML('beforeend',`
<input type="hidden" name="items[${i}][product_id]" value="${c.product_id}">
<input type="hidden" name="items[${i}][product_variant_id]" value="${c.variant_id}">
<input type="hidden" name="items[${i}][qty]" value="${c.qty}">
<input type="hidden" name="items[${i}][buyprice]" value="${c.buyprice}">
`);
});
purchaseForm.submit();
}
</script>

</body>
</html>
