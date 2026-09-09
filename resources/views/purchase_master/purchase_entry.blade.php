<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>POS Menu</title>

<!-- MDB + FontAwesome + SweetAlert -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* GENERAL */
.qty-input{width:50px;text-align:center;}
.fly-img{position:absolute;z-index:9999;transition:all .8s cubic-bezier(.68,-0.55,.27,1.55);}
.payment-box{border:2px solid #dee2e6;padding:15px;font-size:18px;border-radius:10px;margin-bottom:10px;position:relative;}
.payment-box.active{border-color:#28a745;background:#eafaf1;}
.payment-box i{position:absolute;right:15px;top:50%;transform:translateY(-50%);display:none;color:#28a745;}
.payment-box.active i{display:block;}
#summary-panel{position:fixed;top:0;right:-100%;width:100%;height:100%;background:#fff;z-index:2000;transition:right .4s ease;padding:15px;overflow-y:auto;}
#summary-panel.show{right:0;}
.table-responsive{max-height:60vh;overflow-y:auto;}

/* NAVBAR */
.nav-desktop{display:flex;justify-content:space-between;align-items:center;padding:10px;background: teal;;color:#fff;}
.nav-mobile{display:none;justify-content:space-between;align-items:center;padding:10px;background:#0d6efd;color:#fff;position:fixed;width:100%;top:0;z-index:3000;}
@media(max-width:768px){
    .nav-desktop{display:none;}
    .nav-mobile{display:flex;}
    body{padding-bottom:60px;}
}

/* MOBILE CART PANEL */
#mobile-cart-panel{position:fixed;top:0;left:100%;width:100%;height:100%;background:#fff;z-index:4000;transition:left .4s ease;padding:15px;overflow-y:auto;}
#mobile-cart-panel table td{vertical-align:middle;}
.customer-item.active{background:#0d6efd;color:#fff;}
</style>
</head>
<body>

<!-- DESKTOP NAVBAR -->
<div class="nav-desktop">
    <a class="btn text-white btn-sm btn-outline-white" href="{{ route('purchasePage') }}">
      <i class="fas fa-arrow-left"></i> Back
    </a>
    <div><h5>Create Purchase</h5></div>
    <div>
       
        
        
        <!-- <button id="desktop-customer-btn" class="btn btn-light"><i class="fas fa-user"></i> <span id="selected-customer">Select Customer</span></button> -->
    </div>
</div>

<!-- MOBILE NAVBAR -->
<div class="nav-mobile">
    <div><h5>POS</h5></div>
    <div class="d-flex gap-2">
          <select name="supplier_id" id="supplier_id" class="form-select">
            @foreach($suppliers as $supplier)
              <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
            @endforeach
        </select>
        <!-- <button id="mobile-customer-btn" class="btn btn-light"><i class="fas fa-user"></i> <span id="selected-customer-mobile">Select Customer</span></button>
        <button id="mobile-cart-btn" class="btn btn-light"><i class="fas fa-shopping-cart"></i> <span id="mobile-cart-count" class="badge bg-danger">0</span></button> -->
    </div>
</div>

<!-- MOBILE PRODUCT SEARCH + CHARGE -->
<div class="d-md-none mt-3 px-2 d-flex gap-2">
    <input type="text" id="mobile-search-input" class="form-control" placeholder="Search products...">
    <button id="mobile-charge-btn" class="btn btn-success flex-shrink-0">
        CHARGE
    </button>
</div>

<div class="container-fluid mt-3">
<div class="row">

<!-- CART SIDEBAR DESKTOP -->
<div class="col-md-4 d-none d-md-block">
<div class="card sticky-top">
<div class="card-body text-center">
<button class="btn btn-secondary btn-lg w-100" id="save-sale">
<span class="btn-text">CHARGE</span><br>
<strong>Ks <span id="cart-subtotal">0</span></strong>
<span class="spinner-border spinner-border-sm ms-2 d-none"></span>
</button>
</div>
<div class="card-body" id="cart-items">
<div class="text-center text-muted">No items yet</div>
</div>
</div>
</div>

<!-- PRODUCTS -->
<div class="col-md-8">
<div class="d-flex mb-2 gap-2">
    <select id="search-type" class="form-select w-25">
        <option value="all">All</option>
        <option value="brand">Brand</option>
        <option value="category">Category</option>
        <option value="name">Item</option>
    </select>
    <input type="text" id="search-input" class="form-control" placeholder="Search...">
</div>
<div class="table-responsive">
<table class="table table-sm table-bordered">
<thead class="table-primary">
<tr><th>Srno</th><th>Item Name</th><th>Unit Name</th><th>Price</th></tr>
</thead>
<tbody id="product-table">
@foreach($products as $p)
<tr class="product-row add-to-cart"
    data-id="{{ $p->id }}"
    data-code="{{ $p->item_code }}"
    data-name="{{ $p->item_name }}"
    data-brand="{{ $p->brand->name ?? '' }}"
    data-category="{{ $p->category->name ?? '' }}"
    data-price="{{ $p->cost }}">
    <td>{{ $loop->iteration }}</td>
<td>
    {{ $p->item_name }} {{ $p->brand_name }}<span class="badge bg-warning text-dark ms-1">{{ $p->on_hand_qty }}</span>
    @if($p->on_hand_qty <= 5)
        <span class="badge bg-danger text-dark ms-1">LOW</span>
    @endif
    @if($p->on_hand_qty <= 0)
        <span class="badge bg-danger ms-1">OUT</span>
    @endif
</td>
<td>{{ $p->unit_name }} x {{ $p->to_base }}</td>
<td>Ks {{ number_format($p->cost) }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</div>

</div>
</div>

<!-- SUMMARY PANEL -->
<div id="summary-panel">
<div class="d-flex justify-content-between mb-2">
<button class="btn btn-sm btn-outline-primary" id="back-summary"><i class="fas fa-arrow-left"></i> Back</button>
<h5 class="mb-0 fw-bold">Purchase Summary</h5>
<div style="width:32px"></div>
</div>

<form id="sale-form" action="{{ route('purchase.save') }}" method="POST">
@csrf
<input type="hidden" name="supplier_id" id="supplier_id">
<input type="hidden" name="total_amount" id="total_amount">
<input type="hidden" name="grand_amount" id="grand_amount">
<input type="hidden" name="refund_amount" id="refund_amount">
<input type="hidden" name="extra_amount" id="extra_amount">
<input type="hidden" name="tax" id="tax">
<input type="hidden" name="items" id="items-json">
<input type="hidden" name="paid_amount" id="paid_amount_hidden">
<label for="" class='text-muted'>Supplier:</label>
<select name="supplier_id" id="supplier_id" class="form-select" required>
            <option value="">CHOOSE SUPPLIER</option>
            @foreach($suppliers as $supplier)
              <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
            @endforeach
        </select>
<div class="mb-3 text-center">
<div class="fs-2 fw-bold text-primary">Total: <span id="summary-total">0</span> MMK</div>
</div>

<div class="table-responsive">
<table class="table table-bordered mb-2">
<thead>
<tr><th>Item</th><th>Qty</th><th>Total</th></tr>
</thead>
<tbody id="summary-items"></tbody>
</table>
</div>

<div class="row g-2">
<div class="col-6"><div class="d-flex justify-content-between"><span>Sub Total</span><strong><span id="sub-total">0</span></strong></div></div>
<div class="col-6"><div class="d-flex justify-content-between"><span>Discount</span><input type="number" id="discount" class="form-control form-control-sm w-50" value="0"></div></div>
<div class="col-6"><div class="d-flex justify-content-between"><span>Tax (5%)</span><strong><span id="tax-amount">0</span></strong></div></div>
<div class="col-6"><div class="d-flex justify-content-between"><span>Grand Total</span><strong><span id="grand-total" class="fw-bold fs-5">0</span></strong></div></div>
<div class="col-12 mt-2">
<label>Cash Received</label>
<input type="number" id="paid-amount" class="form-control" value="0">
</div>
<div class="col-12 mt-2">
<p>Refund: <strong><span id="refund-amount-display">0</span></strong></p>
</div>
</div>

<div class="mb-3">
<div class="d-flex flex-wrap gap-2">
@foreach($payment_methods as $m)
<button type="button" class="btn btn-outline-secondary payment-box position-relative" data-id="{{ $m->id }}">
{{ $m->payment_short_code }}
<i class="fas fa-check-circle position-absolute top-0 end-0 m-1 d-none"></i>
</button>
@endforeach
</div>
<input type="hidden" name="payment_method_id" id="payment_method_id">
</div>

<button id="save-print" type="submit" class="btn btn-success w-100 mt-3">
    <span class="btn-text">Save & Print</span>
    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
</button>


<span class="spinner-border spinner-border-sm ms-2 d-none"></span>
</button>
</form>
</div>

<!-- CUSTOMER MODAL -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Customers</h5>
<button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="text" id="customer-search" class="form-control mb-2" placeholder="Search customer">
<hr>
<button class="btn btn-primary w-100 mt-2" id="add-customer-btn">Add Customer</button><hr>
<ul class="list-group" id="customer-list">
@foreach($suppliers as $c)
<li class="list-group-item customer-item" data-id="{{ $c->id }}">{{ $c->customer_name }}</li>
@endforeach
</ul>
</div>
</div>
</div>
</div>

<!-- CUSTOMER ADD PANEL -->
<div id="customer-add-panel" style="position:fixed;top:0;left:-100%;width:100%;max-width:500px;height:100%;background:#fff;z-index:4000;transition:left .4s ease;padding:20px;overflow-y:auto;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-outline-primary" id="back-customer-panel"><i class="fas fa-arrow-left"></i> Back</button>
        <h5 class="mb-0">Add Customer</h5>
        <select name="supplier_id" id="" class="form-select">
            @foreach($suppliers as $supplier)
              <option value="{{ $supplier->id }}">{{ $supplier->customer_name }}</option>
            @endforeach
        </select>
        <div style="width:32px"></div>
    </div>
    <form id="customer-add-form" method="POST" action="">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" class="form-control" name="customer_name" required>
        </div>
        <div class="mb-3">
            <label>Phone</label>
            <input type="text" class="form-control" name="phone">
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" class="form-control" name="email">
        </div>
        <button type="submit" class="btn btn-success w-100">Save Customer</button>
    </form>
</div>

<!-- MOBILE CART PANEL -->
<div id="mobile-cart-panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-outline-primary" id="back-mobile-cart"><i class="fas fa-arrow-left"></i> Back</button>
        <h5 class="mb-0 fw-bold">Cart Items</h5>
        <div style="width:100px"></div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-2">
            <thead><tr><th>Item</th><th>Qty</th><th>Total</th></tr></thead>
            <tbody id="mobile-cart-items"></tbody>
        </table>
    </div>
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <strong>Subtotal: <span id="mobile-cart-subtotal">0</span> MMK</strong>
    </div>
    <button id="mobile-charge-btn-panel" class="btn btn-success w-100">CHARGE</button>
</div>

<!-- JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.js"></script>
<script>
let cart=[];

// ---- PRODUCT SEARCH ----
$('#search-input,#mobile-search-input').keyup(function(){
    let v=$(this).val().toLowerCase();
    let t=$('#search-type').val();
    $('#product-table tr').each(function(){
        let match=false;
        if(t==='all') match=$(this).text().toLowerCase().includes(v);
        else match=$(this).data(t).toLowerCase().includes(v);
        $(this).toggle(match);
    });
});
$('.add-to-cart').click(function(){
    let r=$(this);
    let id=r.data('id');
    let f=cart.find(i=>i.id===id);
    if(f) f.qty++; else cart.push({id:id,name:r.data('name'),price:+r.data('price'),qty:1});
    flyToButton(r);
    updateCart();
    updateSummary();
});
function flyToButton(row){
    let img=$('<i class="fas fa-shopping-cart fly-img"></i>').appendTo('body');
    let s=row.offset();
    let target=$('body').width() < 768 ? $('#mobile-cart-btn') : $('#save-sale');
    let e=target.offset();
    img.css({left:s.left,top:s.top});
    img.animate({left:e.left,top:e.top},800,()=>img.remove());
}

// ---- CART UPDATE ----
function updateCart(){
    let total = 0;
    $('#cart-items').empty();

    cart.forEach((item, index) => {
        let rowTotal = item.price * item.qty;
        total += rowTotal;

        $('#cart-items').append(`
            <div class="d-flex justify-content-between align-items-center border p-1 mb-1">
                <div>
                    <strong>${item.name}</strong><br>
                    <small>${item.price} x ${item.qty} = ${rowTotal}</small>
                </div>

                <div class="d-flex align-items-center gap-1">
                    <button class="btn btn-sm btn-secondary qty-minus" data-i="${index}">−</button>

                    <input type="number"
                           min="1"
                           class="form-control form-control-sm qty-input"
                           style="width:60px"
                           data-i="${index}"
                           value="${item.qty}">

                    <button class="btn btn-sm btn-secondary qty-plus" data-i="${index}">+</button>

                    <button class="btn btn-sm btn-danger remove" data-i="${index}">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        `);
    });

    $('#cart-subtotal,#mobile-cart-subtotal').text(total);
    $('#mobile-cart-count').text(cart.length);
    updateMobileCart();
}

$(document).on('click','.remove',function(){cart.splice($(this).data('i'),1);updateCart();updateSummary();});

// ---- MOBILE CART ----
$('#mobile-cart-btn').click(function(){if(cart.length===0){Swal.fire('Cart empty');return;} updateMobileCart(); $('#mobile-cart-panel').css('left','0');});
$('#back-mobile-cart').click(function(){ $('#mobile-cart-panel').css('left','100%'); });
function updateMobileCart(){
    let t=0;
    $('#mobile-cart-items').empty();
    cart.forEach((i,idx)=>{
        t+=i.price*i.qty;
        $('#mobile-cart-items').append(`
<tr>
<td>${i.name}</td>
<td class="d-flex justify-content-center align-items-center gap-1">
<button class="btn btn-sm btn-outline-secondary mobile-decrease" data-i="${idx}">-</button>
<span>${i.qty}</span>
<button class="btn btn-sm btn-outline-secondary mobile-increase" data-i="${idx}">+</button>
</td>
<td>${i.price*i.qty}</td>
</tr>`);
    });
    $('#mobile-cart-subtotal').text(t);
}
$(document).on('click','.mobile-increase',function(){let idx=$(this).data('i');cart[idx].qty++;updateCart();updateSummary();});
$(document).on('click','.mobile-decrease',function(){let idx=$(this).data('i');if(cart[idx].qty>1) cart[idx].qty--; else cart.splice(idx,1);updateCart();updateSummary();});

// ---- SHOW SUMMARY ----
$('#mobile-charge-btn,#mobile-charge-btn-panel,#save-sale').click(function(){if(cart.length===0){Swal.fire('Cart empty');return;} $('#summary-panel').addClass('show');});
$('#back-summary').click(()=>$('#summary-panel').removeClass('show'));

// ---- PAYMENT ----
$('.payment-box').click(function(){$('.payment-box').removeClass('active'); $(this).addClass('active'); $('#payment_method_id').val($(this).data('id'));});
$('.payment-box').first().addClass('active'); $('#payment_method_id').val($('.payment-box').first().data('id'));

// ---- CUSTOMER MODAL ----
$('#desktop-customer-btn,#mobile-customer-btn').click(()=>{const modal = new mdb.Modal($('#customerModal'));modal.show();});
$('#customer-search').keyup(function(){let v=$(this).val().toLowerCase();$('#customer-list li').each(function(){$(this).toggle($(this).text().toLowerCase().includes(v));});});
$(document).on('click','.customer-item',function(){
    $('.customer-item').removeClass('active'); $(this).addClass('active');
    let cid=$(this).data('id'); $('#supplier_id').val(cid);
    $('#selected-customer').text($(this).text()); $('#selected-customer-mobile').text($(this).text());
    Swal.fire({icon:'success',title:'Customer Selected',text:$(this).text(),timer:1000,showConfirmButton:false});
    $('#customerModal').modal('hide');
});
$('#add-customer-btn').click(function(){ $('#customerModal').modal('hide'); $('#customer-add-panel').css('left','0'); });
$('#back-customer-panel').click(function(){ $('#customer-add-panel').css('left','-100%'); });
$('#customer-add-form').submit(function(e){
    e.preventDefault();
    const data=$(this).serializeArray();
    setTimeout(()=>{
        const name=data.find(d=>d.name==='customer_name').value;
        const newId=Math.floor(Math.random()*10000);
        $('#customer-list').append(`<li class="list-group-item customer-item" data-id="${newId}">${name}</li>`);
        $('#customer-add-panel').css('left','-100%');
        $('#supplier_id').val(newId);
        $('#selected-customer').text(name);
        $('#selected-customer-mobile').text(name);
        Swal.fire({icon:'success',title:'Customer Added & Selected',text:name,timer:1000,showConfirmButton:false});
    },500);
});

// ---- SUMMARY CALC ----
function updateSummary(){
    let sub = cart.reduce((a,b)=>a+b.price*b.qty,0);
    let disc = +$('#discount').val()||0;
    let tax = 0;
    let grandTotal = sub - disc + tax;
    let paid = +($('#paid-amount').val()||0);
    let refund = Math.max(0, paid - grandTotal);
    let extra = Math.max(0, grandTotal - paid);

    $('#sub-total').text(sub);
    $('#tax-amount').text(Math.round(tax));
    $('#summary-total').text(Math.round(grandTotal));
    $('#grand-total').text(Math.round(grandTotal));
    $('#refund-amount-display').text(refund);

    $('#total_amount').val(Math.round(sub));
    $('#grand_amount').val(Math.round(grandTotal));
    $('#refund_amount').val(refund);
    $('#extra_amount').val(extra);
    $('#tax').val(Math.round(tax));
    $('#items-json').val(JSON.stringify(cart));
    $('#paid_amount_hidden').val(paid);
}
updateSummary();
$('#discount,#paid-amount').on('input',updateSummary);
$('#sale-form').on('submit', function () {
    const btn = $('#save-print');

    btn.prop('disabled', true);
    btn.find('.btn-text').text('Saving...');
    btn.find('.spinner-border').removeClass('d-none');
});
// ---- SAVE & PRINT ----
$('#sal-form').submit(function (e) {
    e.preventDefault();
    if (!$('#supplier_id').val()) {Swal.fire({ icon:'warning', title:'Please select a customer', timer:1200, showConfirmButton:false }); return;}

    Swal.fire({title:'Saving Sale...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});

    setTimeout(()=>{
        Swal.close();
        Swal.fire({icon:'success',title:'Sale Saved!',showConfirmButton:false,timer:1200,timerProgressBar:true});
        cart=[]; updateCart(); updateSummary(); $('#summary-panel').removeClass('show');
        printToAndroidPrinter();
    },1500);
});

function printToAndroidPrinter(){
    let receipt='===============================\n';
    receipt+='        POS RECEIPT\n===============================\n';
    receipt+='Date: '+new Date().toLocaleString()+'\n';
    receipt+='Customer: '+($('#selected-customer').text()||'Guest')+'\n';
    receipt+='-------------------------------\n';
    cart.forEach(item=>{receipt+=item.name+' x'+item.qty+'   '+(item.price*item.qty).toLocaleString()+'\n';});
    receipt+='-------------------------------\n';
    receipt+='Subtotal: '+$('#sub-total').text()+'\n';
    receipt+='Discount: '+($('#discount').val()||0)+'\n';
    receipt+='Tax: '+($('#tax-amount').text())+'\n';
    receipt+='TOTAL: '+$('#summary-total').text()+'\n';
    receipt+='Paid: '+($('#paid-amount').val()||0)+'\n';
    receipt+='Change: '+($('#refund-amount-display').text())+'\n';
    receipt+='-------------------------------\n';
    receipt+='Thank You!\n\n\n';
    if(window.AndroidPOS && AndroidPOS.printReceipt){AndroidPOS.printReceipt(receipt);} else {alert('Android printer service not available');}
}
</script>
</body>
</html>
