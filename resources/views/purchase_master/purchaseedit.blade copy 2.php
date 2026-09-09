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
.summary-card{position:sticky;top:80px}
.qty-input{max-width:70px;text-align:center}
</style>
</head>
<body>

<form id="purchaseForm" method="POST" action="{{ route('purchase.update',$purchase->id) }}">
@csrf
@method('PUT')

<input type="hidden" id="sub_total_input" name="sub_total" value="{{ $purchase->sub_total }}">
<input type="hidden" id="discount_input" name="discount" value="{{ $purchase->discount_amount }}">
<input type="hidden" id="tax_input" name="tax" value="{{ $purchase->tax_amount }}">
<input type="hidden" id="grand_total_input" name="grand_total" value="{{ $purchase->grand_total }}">
<input type="hidden" id="paid_input" name="paid_amount" value="{{ $purchase->paid_amount }}">
<input type="hidden" id="balance_input" name="balance_amount" value="{{ $purchase->balance_amount }}">

<div class="container-fluid bg-warning py-2 sticky-top">
<div class="row g-2">

<div class="col-md-3">
<label>Purchase Date</label>
<input type="date" class="form-control form-control-sm" name="purchase_date" value="{{ $purchase->purchase_date->format('Y-m-d') }}">
</div>

<div class="col-md-3">
<label>Supplier</label>
<select name="supplier_id" class="form-select form-select-sm">
@foreach($suppliers as $s)
<option value="{{ $s->id }}" @selected($purchase->supplier_id==$s->id)>{{ $s->supplier_name }}</option>
@endforeach
</select>
</div>

<div class="col-md-3">
<label>Payment</label>
<select name="payment_type_id" class="form-select form-select-sm">
@foreach($paymentMethods as $p)
<option value="{{ $p->id }}" @selected($purchase->payment_type_id==$p->id)>{{ $p->payment_short_code }}</option>
@endforeach
</select>
</div>

<div class="col-md-3">
<label>Transaction</label>
<select name="transaction_type_id" class="form-select form-select-sm">
@foreach($transactionTypes as $t)
<option value="{{ $t->id }}" @selected($purchase->transaction_type_id==$t->id)>{{ $t->transaction_name }}</option>
@endforeach
</select>
</div>

</div>
</div>

<!-- CART + SUMMARY -->
<div class="container-fluid mt-3">
<div class="row g-3">

<div class="col-lg-8">
<button type="button" class="btn btn-sm btn-primary mb-2" data-mdb-toggle="modal" data-mdb-target="#productSearchModal">
<i class="fas fa-search"></i> Search Product
</button>

<div class="card">
<div class="card-body p-2">
<table class="table table-bordered table-sm">
<thead class="table-warning text-center">
<tr>
<th>#</th>
<th>Code</th>
<th>Product</th>
<th width="100">Unit</th>
<th width="150">Qty</th>
<th width="110">Buy</th>
<th width="100">Discount</th>
<th width="110">Amount</th>
<th>✕</th>
</tr>
</thead>
<tbody id="cartBody"></tbody>
</table>
</div>
</div>
</div>

<div class="col-lg-4">
<div class="card summary-card">
<div class="card-body">
<ul class="list-group mb-2">
<li class="list-group-item d-flex justify-content-between">
<span>Sub Total</span><strong id="subTotal">{{ number_format($purchase->sub_total) }} Ks</strong>
</li>
<li class="list-group-item">
<label>Discount</label>
<input id="discountField" class="form-control form-control-sm text-end" value="{{ $purchase->discount_amount }}" oninput="calculate()">
</li>
<li class="list-group-item">
<label>Tax</label>
<input id="taxField" class="form-control form-control-sm text-end" value="{{ $purchase->tax_amount }}" oninput="calculate()">
</li>
<li class="list-group-item fw-bold d-flex justify-content-between">
<span>Grand Total</span><span id="grandTotal">{{ number_format($purchase->grand_total) }} Ks</span>
</li>
<li class="list-group-item">
<label>Paid</label>
<input id="paidField" class="form-control form-control-sm text-end" value="{{ $purchase->paid_amount }}" oninput="calculate()">
</li>
<li class="list-group-item fw-bold d-flex justify-content-between">
<span>Balance</span><span id="balance">{{ number_format($purchase->balance_amount) }} Ks</span>
</li>
</ul>
<button type="button" id="saveBtn" class="btn btn-success w-100">
<span id="saveText"><i class="fas fa-save"></i> Update Purchase</span>
<span id="saveSpinner" class="d-none"><span class="spinner-border spinner-border-sm me-2"></span>Saving...</span>
</button>
</div>
</div>
</div>

</div>
</div>

<div id="cartInputs"></div>

<!-- PRODUCT MODAL -->
<div class="modal fade" id="productSearchModal">
<div class="modal-dialog modal-lg modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header bg-primary text-white">
<h6 class="modal-title"><i class="fas fa-box"></i> Product Search</h6>
<button class="btn-close btn-close-white" data-mdb-dismiss="modal"></button>
</div>
<div class="modal-body p-2">
<input id="productSearchInput" class="form-control form-control-sm mb-2" placeholder="Search product name or code">
<table class="table table-sm table-hover">
<thead>
<tr>
<th>Code</th>
<th>Name</th>
<th>Unit</th>
<th class="text-end">Buy</th>
</tr>
</thead>
<tbody id="productSearchBody"></tbody>
</table>
</div>
</div>
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

<script>
const products = @json($products);
let cart = @json($items ?? []).map(item=>{
    return {...item, buyprice:Number(item.buyprice||item.last_purchase_price||0), qty:Number(item.quantity||item.qty)};
});
let isSubmitting=false;

const cartBody=document.getElementById('cartBody');
const cartInputs=document.getElementById('cartInputs');
const productSearchInput=document.getElementById('productSearchInput');
const productSearchBody=document.getElementById('productSearchBody');
const discountField=document.getElementById('discountField');
const taxField=document.getElementById('taxField');
const paidField=document.getElementById('paidField');
const subTotal=document.getElementById('subTotal');
const grandTotal=document.getElementById('grandTotal');
const balance=document.getElementById('balance');
const saveBtn=document.getElementById('saveBtn');
const saveText=document.getElementById('saveText');
const saveSpinner=document.getElementById('saveSpinner');
const fmt=n=>new Intl.NumberFormat().format(n)+' Ks';

/* ================= RENDER CART ================= */
function render(){
    cartBody.innerHTML='';
    cartInputs.innerHTML='';
    cart.forEach((c,i)=>{
        const product = products.find(p=>p.id===c.product_id);
        let variant = product?.variants.find(v=>v.id===c.product_variant_id) || { unit:{unit_name:''}, last_purchase_price:0 };
        c.buyprice = Number(c.buyprice || variant.last_purchase_price || 0);
        const total = (c.qty*c.buyprice)-(c.discount||0);

        cartBody.insertAdjacentHTML('beforeend',`
        <tr id="row-${i}">
            <td>${i+1}</td>
            <td>${c.product_code}</td>
            <td>${c.product_name}</td>
            <td>
            <select class="form-select form-select-sm" onchange="changeUnit(${i},this.value)">
                ${product.variants.map(v=>`<option value="${v.id}" ${v.id===c.product_variant_id?'selected':''}>${v.unit?.unit_name||''}</option>`).join('')}
            </select>
            </td>
            <td>
            <div class="input-group input-group-sm">
                <button type="button" class="btn btn-outline-secondary" onclick="changeQty(${i},-1)">−</button>
                <input type="number" min="1" class="form-control qty-input" value="${c.qty}" oninput="changeQtyInput(${i},this.value)">
                <button type="button" class="btn btn-outline-secondary" onclick="changeQty(${i},1)">+</button>
            </div>
            </td>
            <td>
            <input class="form-control form-control-sm text-end" value="${c.buyprice}" oninput="cart[${i}].buyprice=this.value||0;updateRow(${i})">
            </td>
            <td>
            <input class="form-control form-control-sm text-end" value="${c.discount||0}" oninput="cart[${i}].discount=this.value||0;updateRow(${i})">
            </td>
            <td class="text-end row-amount">${fmt(total)}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="cart.splice(${i},1);render()">✕</button></td>
        </tr>
        `);

        cartInputs.insertAdjacentHTML('beforeend',`
            <input type="hidden" name="items[${i}][product_id]" value="${c.product_id}">
            <input type="hidden" name="items[${i}][variant_id]" value="${c.product_variant_id}">
            <input type="hidden" name="items[${i}][qty]" value="${c.qty}">
            <input type="hidden" name="items[${i}][buyprice]" value="${c.buyprice}">
            <input type="hidden" name="items[${i}][discount]" value="${c.discount||0}">
            <input type="hidden" name="items[${i}][total_amount]" value="${total}">
        `);
    });
    calculate();
}

/* ================= CART FUNCTIONS ================= */
function updateRow(i){
    const c = cart[i];
    const total = (Number(c.qty)*Number(c.buyprice))-(Number(c.discount)||0);
    document.querySelector(`#row-${i} .row-amount`).innerText = fmt(total);
    document.querySelector(`[name="items[${i}][qty]"]`).value=c.qty;
    document.querySelector(`[name="items[${i}][total_amount]"]`).value=total;
    calculate();
}

function changeQty(i,d){ cart[i].qty=Math.max(1,Number(cart[i].qty)+d); updateRow(i); }
function changeQtyInput(i,val){ val=parseFloat(val); if(isNaN(val)||val<1) val=1; cart[i].qty=val; updateRow(i); }

function changeUnit(i,newVid){
    const oldVid = cart[i].product_variant_id;
    const pid = cart[i].product_id;
    const dupIndex = cart.findIndex((x,idx)=> idx!==i && x.product_id===pid && x.product_variant_id==newVid);
    if(dupIndex===-1){
        cart[i].product_variant_id=Number(newVid);
        const product = products.find(p=>p.id===pid);
        const variant = product.variants.find(v=>v.id==newVid);
        cart[i].buyprice = Number(variant.last_purchase_price || 0);
        render(); return;
    }
    Swal.fire({icon:'warning',title:'Duplicate Unit',text:'This unit already exists.'});
}

/* ================= PRODUCT MODAL ================= */
function addToCart(pid,vid){
    if(cart.some(x=>x.product_id===pid && x.product_variant_id===vid)){ Swal.fire('Already Exist','Product already in cart','warning'); return; }
    const p=products.find(x=>x.id===pid);
    const v=p.variants.find(x=>x.id===vid);
    cart.push({
        product_id:pid,
        product_variant_id:vid,
        product_code:p.product_code,
        product_name:p.name,
        qty:1,
        buyprice:Number(v.last_purchase_price||0),
        discount:0
    });
    render();
}

function renderProductSearch(q=''){
    productSearchBody.innerHTML='';
    q=q.toLowerCase();
    products.forEach(p=>{
        if(!q||p.name.toLowerCase().includes(q)||p.product_code.toLowerCase().includes(q)){
            p.variants.forEach(v=>{
                productSearchBody.insertAdjacentHTML('beforeend',`
                    <tr style="cursor:pointer" onclick="addToCart(${p.id},${v.id})">
                    <td>${p.product_code}</td>
                    <td>${p.name}</td>
                    <td>${v.unit?.unit_name||''}</td>
                    <td class="text-end">${v.last_purchase_price || 0}</td>
                    </tr>`);
            });
        }
    });
}

productSearchInput.addEventListener('input',e=>renderProductSearch(e.target.value));
document.addEventListener('DOMContentLoaded',()=>{ renderProductSearch(); render(); });

/* ================= TOTAL ================= */
function calculate(){
    const sub=cart.reduce((s,c)=>s+(c.qty*c.buyprice-(c.discount||0)),0);
    const d=+discountField.value||0;
    const t=+taxField.value||0;
    const p=+paidField.value||0;
    const g=sub-d+t;
    subTotal.innerText=fmt(sub); grandTotal.innerText=fmt(g); balance.innerText=fmt(g-p);
    sub_total_input.value=sub; discount_input.value=d; tax_input.value=t; grand_total_input.value=g;
    paid_input.value=p; balance_input.value=g-p;
}

/* ================= SAVE ================= */
saveBtn.addEventListener('click',()=>{
    if(isSubmitting) return;
    if(+paidField.value<+grand_total_input.value){ Swal.fire('Invalid Paid','Check paid amount','error'); return; }
    isSubmitting=true; saveBtn.disabled=true;
    saveText.classList.add('d-none'); saveSpinner.classList.remove('d-none');
    setTimeout(()=>document.getElementById('purchaseForm').submit(),120);
});

/* ================= SESSION ALERT ================= */
@if(session('swal'))
Swal.fire({
    icon: '{{ session('swal.icon') }}',
    title: '{{ session('swal.title') }}',
    text: '{{ session('swal.text') }}',
    confirmButtonText: 'OK',
    allowOutsideClick: false,
    allowEscapeKey: false
});
@endif

</script>

</body>
</html>
