<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purchase Entry</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- MDB -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"/>

<style>
body{ background:#f4f6f9 }
.table td{ vertical-align: middle }
.product-row{ cursor:pointer }
.sticky-summary{ position:sticky; top:20px }

.page-loader{
    position:fixed;
    inset:0;
    background:rgba(255,255,255,.7);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:9999;
}


</style>
</head>

<body>

<div class="container-fluid my-3">

<form id="purchaseForm" method="POST" action="{{ route('purchase.store') }}">
@csrf

<!-- ================= HEADER ================= -->
<div class="card shadow-3 mb-3">
<div class="card-header bg-primary text-white fw-bold">
    <i class="fas fa-cart-plus"></i> Purchase Entry
</div>

<div class="card-body">
<div class="row g-3">

<div class="col-md-2">
    <div class="form-outline">
        <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
        <label class="form-label">Purchase Date</label>
    </div>
</div>

<div class="col-md-2">
    <div class="form-outline">
        <input type="text" class="form-control" value="AUTO" disabled>
        <label class="form-label">Voucher No</label>
    </div>
</div>

<div class="col-md-3">
    <select class="form-select" name="supplier_id" required>
        <option value="">Select Supplier</option>
        @foreach($suppliers as $s)
        <option value="{{ $s->id }}">{{ $s->name }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-2">
    <select class="form-select" name="payment_type_id" required>
        <option value="">Payment</option>
        @foreach($paymentMethods as $p)
        <option value="{{ $p->id }}">{{ $p->name }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-2">
    <select class="form-select" name="transaction_type_id" required>
        <option value="">Transaction</option>
        @foreach($transactionTypes as $t)
        <option value="{{ $t->id }}">{{ $t->name }}</option>
        @endforeach
    </select>
</div>

<div class="col-md-1">
    <div class="form-outline">
        <input type="text" class="form-control" value="Completed" readonly>
        <label class="form-label">Status</label>
    </div>
</div>

</div>
</div>
</div>

<!-- ================= BODY ================= -->
<div class="row">

<!-- LEFT -->
<div class="col-md-8">

<div class="form-outline mb-2">
    <input type="text" id="productSearch" class="form-control">
    <label class="form-label">Search Product</label>
</div>

<div class="card shadow-2">
<div class="table-responsive">
<table class="table table-bordered table-sm mb-0">
<thead class="table-primary text-center">
<tr>
    <th>#</th>
    <th>Product</th>
    <th width="130">Unit</th>
    <th width="80">Qty</th>
    <th width="90">Buy</th>
    <th width="80">Disc</th>
    <th width="120">Amount</th>
    <th width="40">✕</th>
</tr>
</thead>
<tbody id="cartBody"></tbody>
</table>
</div>
</div>

</div>

<!-- RIGHT -->
<div class="col-md-4">

<div class="card shadow-3 sticky-summary">
<div class="card-header bg-dark text-white text-center fw-bold">
    Summary
</div>

<div class="card-body">

<div class="d-flex justify-content-between mb-2">
    <span>Sub Total</span>
    <strong><span id="subTotal">0</span> Ks</strong>
</div>

<div class="form-outline mb-2">
    <input type="number" id="discount" class="form-control text-end" value="0">
    <label class="form-label">Discount</label>
</div>

<div class="form-outline mb-2">
    <input type="number" id="tax" class="form-control text-end" value="0">
    <label class="form-label">Tax</label>
</div>

<hr>

<div class="d-flex justify-content-between fs-4 fw-bold text-success">
    <span>Grand Total</span>
    <span><span id="grandTotal">0</span> Ks</span>
</div>

<div class="form-outline mt-2">
    <input type="number" id="paid" class="form-control text-end" value="0">
    <label class="form-label">Paid</label>
</div>

<div class="d-flex justify-content-between mt-2 text-danger">
    <span>Refund</span>
    <strong><span id="refund">0</span> Ks</strong>
</div>

<div id="itemsInputs"></div>

<button type="button" id="saveBtn" class="btn btn-success btn-lg w-100 mt-3 d-none" onclick="savePurchase()">
    <i class="fas fa-save"></i> Save Purchase
</button>

</div>
</div>

</div>
</div>

</form>

<!-- ================= PRODUCT LIST ================= -->
<div class="card shadow-2 mt-3">
<div class="card-header fw-bold">Products</div>
<div class="table-responsive">
<table class="table table-hover" id="productTable">
<tbody>
@foreach($products as $p)
<tr class="product-row"
    data-product-id="{{ $p->id }}"
    data-product-name="{{ $p->name }}"
    data-variants='@json(
        $p->variants->map(fn($v)=>[
            "id"=>$v->id,
            "unit"=>$v->unit->unit_name,
            "price"=>$v->buyprice
        ])
    )'>
    <td>{{ $p->product_code }}</td>
    <td>{{ $p->name }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

</div>

<!-- MDB -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let cart=[];

/* SEARCH */
productSearch.onkeyup=()=> {
    document.querySelectorAll('#productTable tr').forEach(r=>{
        r.style.display=r.innerText.toLowerCase().includes(productSearch.value.toLowerCase())?'':'none';
    });
};

/* ADD PRODUCT */
document.addEventListener('click',e=>{
    let r=e.target.closest('.product-row');
    if(!r) return;
    let v=JSON.parse(r.dataset.variants)[0];
    let i=cart.findIndex(c=>c.variant_id==v.id);
    i>-1?cart[i].qty++:cart.push({
        product_id:r.dataset.productId,
        product_name:r.dataset.productName,
        variant_id:v.id,
        buyprice:v.price,
        qty:1,
        discount:0,
        variants:JSON.parse(r.dataset.variants)
    });
    render();
});

/* RENDER CART */
function render(){
    cartBody.innerHTML='';
    cart.forEach((c,i)=>{
        let amt=c.qty*c.buyprice-c.discount;
        cartBody.innerHTML+=`
        <tr>
        <td>${i+1}</td>
        <td>${c.product_name}</td>
        <td>
        <select class="form-select form-select-sm"
        onchange="cart[${i}].variant_id=this.value;
        cart[${i}].buyprice=this.selectedOptions[0].dataset.price;render()">
        ${c.variants.map(v=>`<option value="${v.id}" data-price="${v.price}" ${v.id==c.variant_id?'selected':''}>${v.unit}</option>`).join('')}
        </select>
        </td>
        <td><input type="number" class="form-control form-control-sm" value="${c.qty}" min="1" oninput="cart[${i}].qty=this.value;render()"></td>
        <td class="text-end">${c.buyprice}</td>
        <td><input type="number" class="form-control form-control-sm text-end" value="${c.discount}" oninput="cart[${i}].discount=this.value;render()"></td>
        <td class="text-end">${amt}</td>
        <td><button class="btn btn-danger btn-sm" onclick="cart.splice(${i},1);render()">✕</button></td>
        </tr>`;
    });
    saveBtn.classList.toggle('d-none',cart.length==0);
    calc();
}

/* SUMMARY */
function calc(){
    let sub=cart.reduce((s,c)=>s+(c.qty*c.buyprice-c.discount),0);
    subTotal.innerText=sub;
    let grand=sub-(+discount.value||0)+(+tax.value||0);
    grandTotal.innerText=grand;
    refund.innerText=(+paid.value||0)-grand;
}
function showPageLoader(){
    document.getElementById('pageLoader').classList.remove('d-none');
}
/* SAVE */
function savePurchase(){
    itemsInputs.innerHTML='';
    cart.forEach((c,i)=>{
        itemsInputs.innerHTML+=`
        <input type="hidden" name="items[${i}][product_id]" value="${c.product_id}">
        <input type="hidden" name="items[${i}][variant_id]" value="${c.variant_id}">
        <input type="hidden" name="items[${i}][qty]" value="${c.qty}">
        <input type="hidden" name="items[${i}][buyprice]" value="${c.buyprice}">
        <input type="hidden" name="items[${i}][discount]" value="${c.discount}">
        `;
    });
    purchaseForm.submit();
}
</script>

@if(session('success'))
<script>
Swal.fire({icon:'success',title:'Saved',text:"{{ session('success') }}"});
</script>
@endif

</body>
</html>
