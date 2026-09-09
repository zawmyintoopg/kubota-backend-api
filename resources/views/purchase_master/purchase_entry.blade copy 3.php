@extends('layouts.master')
@section('title','Purchase POS')

@section('content')
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{ background:#f3f4f6; font-family:sans-serif; margin:0; padding:0; }
.pos-header{ height:56px; background:#111827; color:#fff; display:flex; align-items:center; padding:0 12px; position:sticky; top:0; z-index:50; }
.pos-header strong{ font-size:1.1rem; }

.charge-row{ position:sticky; top:56px; z-index:49; background:#fff; display:flex; justify-content:center; align-items:center; padding:12px; border-bottom:1px solid #ddd; }
.charge-row button{ font-size:1.2rem; padding:12px 30px; min-width:200px; }

.product-list{ margin-top:4px; max-height:60vh; overflow-y:auto; }
.product-item{ display:flex; justify-content:space-between; align-items:center; padding:10px; background:#fff; border-bottom:1px solid #e5e7eb; cursor:pointer; }
.product-item:active{ background:#e5e7eb; }
.product-left{ display:flex; align-items:center; gap:10px; }
.product-thumb{ width:42px; height:42px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-weight:600; color:#fff; font-size:18px; flex-shrink:0; }

.fly-item{ position:fixed; width:36px; height:36px; z-index:9999; pointer-events:none; transition:.6s cubic-bezier(.4,0,.2,1); }

.ticket, .payment-panel{ position:fixed; inset:0; background:#fff; z-index:10000; display:flex; flex-direction:column; transform:translateX(100%); transition:.3s ease; }
.ticket.show, .payment-panel.show{ transform:translateX(0); }

.ticket-header, .payment-header{ background:#1f2937; color:#fff; padding:12px; display:flex; justify-content:space-between; align-items:center; }
.ticket-body, .payment-body{ flex:1; overflow:auto; padding:10px; }

.ticket-item{ border-bottom:1px solid #ddd; padding:10px; border-radius:6px; margin-bottom:6px; background:#f8f8f8; display:flex; justify-content:space-between; align-items:center; }
.ticket-item strong{ font-size:1rem; }
.qty-btn{ width:30px; height:30px; border:none; background:#e5e7eb; border-radius:6px; font-size:16px; }
.item-input{ width:60px; }
.remove-btn{ background:red; color:#fff; border:none; border-radius:4px; padding:2px 6px; cursor:pointer; }

.back-btn{ font-size:1rem; padding:6px 12px; background:#e5e7eb; border:none; border-radius:6px; cursor:pointer; }
.payment-buttons button{ margin-right:6px; margin-bottom:6px; }

#itemSearchWrapper{ position:relative; }
#itemSearchWrapper input{ padding-left:30px; }
#itemSearchWrapper span{ position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#888; }
</style>

<!-- HEADER -->
<div class="pos-header d-flex align-items-center justify-content-between">
    <strong>Purchase POS</strong>
    <div class="d-flex gap-2">
        <button id="supplierBtn" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal">Select Supplier</button>
        <button id="ticketBtn" class="btn btn-dark btn-sm position-relative" disabled>🧾 
            <span id="ticketCount" class="badge bg-danger position-absolute top-0 start-100 translate-middle">0</span>
        </button>
    </div>
</div>

<!-- CHARGE ROW -->
<div class="charge-row">
    <button id="chargeBtn" class="btn btn-success" disabled>CHARGE 0 Ks</button>
</div>

<!-- SEARCH PRODUCT -->
<div class="p-2 bg-light sticky-top" id="itemSearchWrapper">
    <span>🔍</span>
    <input id="searchInput" class="form-control" placeholder="Search product">
</div>

<!-- PRODUCT LIST -->
<div class="product-list">
@foreach($products as $product)
    @foreach($product->variants as $variant)
    <div class="product-item"
         data-name="{{ strtolower($product->name) }}"
         onclick="addToCart(this,{
            variant_id:{{ $variant->id }},
            name:'{{ $product->name }}',
            unit:'{{ $variant->unit->unit_name }}',
            buy:{{ $variant->last_purchase_price }},
            qty:1,
            discount:0,
            tax:0
         })">
        <div class="product-left">
            <div class="product-thumb" style="background: {{ sprintf('#%06X', crc32($product->name) & 0xFFFFFF) }}">
                {{ strtoupper(substr($product->name,0,1)) }}
            </div>
            <div>
                <strong>{{ $product->name }}</strong><br>
                <small>{{ $variant->unit->unit_name }}</small>
            </div>
        </div>
        <strong>{{ number_format($variant->last_purchase_price,2) }} Ks</strong>
    </div>
    @endforeach
@endforeach
</div>

<!-- TICKET -->
<div id="ticket" class="ticket">
    <div class="ticket-header">
        <button class="back-btn" onclick="closeTicket()">← Back</button>
        <strong>Cart (<span id="ticketCartCount">0</span>)</strong>
    </div>
    <div id="ticketBody" class="ticket-body"></div>
</div>

<!-- PAYMENT PANEL -->
<div id="paymentPanel" class="payment-panel">
    <div class="payment-header">
        <button class="back-btn" onclick="closePaymentPanel()">← Back</button>
        <strong>Payment</strong>
    </div>
    <div class="payment-body">
        <p><strong>Sub Total:</strong> <span id="paymentSubTotal">0 Ks</span></p>
        <p><strong>Total Discount:</strong> <span id="paymentDiscount">0 Ks</span></p>
        <p><strong>Total Tax:</strong> <span id="paymentTax">0 Ks</span></p>
        <p><strong>Grand Total:</strong> <span id="paymentTotal">0 Ks</span></p>

        <div class="mb-3">
            <label>Paid Amount</label>
            <input type="number" id="paidAmount" class="form-control" value="0">
        </div>

        <div class="mb-3 payment-buttons">
            <button class="btn btn-outline-primary" onclick="setPaymentType(1)">Cash</button>
            <button class="btn btn-outline-primary" onclick="setPaymentType(2)">Card</button>
            <button class="btn btn-outline-primary" onclick="setPaymentType(3)">KPay</button>
        </div>
        <button class="btn btn-primary w-100" onclick="savePurchase()">Save Purchase</button>
    </div>
</div>

<!-- SUPPLIER MODAL -->
<div class="modal fade" id="supplierModal">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <strong>Select Supplier</strong>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Quick Add Supplier -->
                <div class="mb-3 d-flex gap-2">
                    <input type="text" id="newSupplierName" class="form-control" placeholder="Add New Supplier">
                    <button class="btn btn-primary" onclick="quickAddSupplier()">Add</button>
                </div>

                <!-- Supplier Search -->
                <div class="mb-3 position-relative">
                    <input type="text" id="supplierSearch" class="form-control ps-5" placeholder="Search supplier">
                    <span style="position:absolute; left:12px; top:50%; transform:translateY(-50%);">🔍</span>
                </div>

                <!-- Supplier Table -->
                <div class="table-responsive" style="max-height:300px; overflow:auto;">
                    <table class="table table-hover table-bordered" id="supplierTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Supplier Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $s)
                            <tr>
                                <td>{{ $s->id }}</td>
                                <td>{{ $s->supplier_name }}</td>
                                <td><button class="btn btn-sm btn-success" onclick="selectSupplier('{{ $s->supplier_name }}',{{ $s->id }})">Select</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let cart=[], supplier=null, supplierId=null, paymentTypeSelected='';

const ticket=document.getElementById('ticket');
const ticketBody=document.getElementById('ticketBody');
const ticketCount=document.getElementById('ticketCount');
const ticketCartCount=document.getElementById('ticketCartCount');
const chargeBtn=document.getElementById('chargeBtn');
const ticketBtn=document.getElementById('ticketBtn');
const supplierBtn=document.getElementById('supplierBtn');
const searchInput=document.getElementById('searchInput');
const paymentPanel=document.getElementById('paymentPanel');
const paymentTotal=document.getElementById('paymentTotal');
const paymentSubTotal=document.getElementById('paymentSubTotal');
const paymentDiscount=document.getElementById('paymentDiscount');
const paymentTax=document.getElementById('paymentTax');
const paidAmount=document.getElementById('paidAmount');
const supplierSearch=document.getElementById('supplierSearch');
const supplierTable=document.getElementById('supplierTable').getElementsByTagName('tbody')[0];
const newSupplierName=document.getElementById('newSupplierName');

function updateButtonsState(){
    const disabled = cart.length===0;
    ticketBtn.disabled = disabled;
    chargeBtn.disabled = disabled;
    const totals = calculateTotals();
    chargeBtn.innerText = `CHARGE ${totals.grand_total} Ks`;
    ticketCartCount.innerText = cart.length;
}

function selectSupplier(name,id){
    supplier=name; supplierId=id;
    supplierBtn.innerText=name;
    const modalEl=document.getElementById('supplierModal');
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modalInstance.hide();
    ticketBtn.disabled = cart.length===0;
}

supplierSearch.oninput = () => {
    const val = supplierSearch.value.toLowerCase();
    Array.from(supplierTable.rows).forEach(row=>{
        row.style.display = row.cells[1].innerText.toLowerCase().includes(val) ? '' : 'none';
    });
}

function quickAddSupplier(){
    const name = newSupplierName.value.trim();
    if(!name){ alert('Enter supplier name'); return; }
    const newId = Math.floor(Math.random()*99999);
    const row = supplierTable.insertRow(0);
    row.innerHTML = `<td>${newId}</td><td>${name}</td><td><button class="btn btn-sm btn-success" onclick="selectSupplier('${name}',${newId})">Select</button></td>`;
    newSupplierName.value='';
    alert('Supplier added');
}

// CART FUNCTIONS
function addToCart(el,p){
    let f=cart.find(i=>i.variant_id===p.variant_id);
    if(f){ f.qty++; } else { cart.push(p); }
    cart.forEach(i=>i.amount=(i.qty*i.buy - i.discount + i.tax).toFixed(2));
    updateButtonsState(); renderTicket(); fly(el);
}

function fly(el){
    const img = el.querySelector('.product-thumb'); if(!img) return;
    const f=img.cloneNode(true);
    const r=img.getBoundingClientRect();
    const t=ticketBtn.getBoundingClientRect();
    f.className='fly-item';
    document.body.appendChild(f);
    f.style.left=r.left+'px'; f.style.top=r.top+'px';
    setTimeout(()=>{ f.style.left=t.left+'px'; f.style.top=t.top+'px'; f.style.opacity=0; f.style.transform='scale(.2)'; },10);
    setTimeout(()=>f.remove(),700);
}

function renderTicket(){
    ticketBody.innerHTML='';
    cart.forEach((i,x)=>{
        ticketBody.innerHTML+=`
        <div class="ticket-item">
            <div>
                <strong>${i.name}</strong>
                <div class="d-flex justify-content-between"><small>${i.unit}</small><strong>${i.buy} Ks</strong></div>
                <div class="d-flex gap-1 mt-1">
                    <button class="qty-btn" onclick="updateQty(${x},-1)">−</button>
                    <strong>${i.qty}</strong>
                    <button class="qty-btn" onclick="updateQty(${x},1)">+</button>
                    <input type="number" class="item-input" value="${i.discount}" placeholder="Disc" onchange="updateDiscount(${x},this.value)">
                    <input type="number" class="item-input" value="${i.tax}" placeholder="Tax" onchange="updateTax(${x},this.value)">
                </div>
                <div class="mt-1">Subtotal: ${i.amount} Ks</div>
            </div>
            <button class="remove-btn" onclick="removeItem(${x})">×</button>
        </div>`;
    });
    updateButtonsState();
    updatePaymentPanel();
}

function updateQty(i,v){ cart[i].qty+=v; if(cart[i].qty<1) cart[i].qty=1; cart[i].amount=(cart[i].qty*cart[i].buy - cart[i].discount + cart[i].tax).toFixed(2); renderTicket(); }
function updateDiscount(i,v){ cart[i].discount=parseFloat(v)||0; cart[i].amount=(cart[i].qty*cart[i].buy - cart[i].discount + cart[i].tax).toFixed(2); renderTicket(); }
function updateTax(i,v){ cart[i].tax=parseFloat(v)||0; cart[i].amount=(cart[i].qty*cart[i].buy - cart[i].discount + cart[i].tax).toFixed(2); renderTicket(); }
function removeItem(i){ cart.splice(i,1); renderTicket(); }

function calculateTotals(){
    let sub_total=0, discount=0, tax=0;
    cart.forEach(i=>{
        sub_total += i.buy*i.qty;
        discount += i.discount;
        tax += i.tax;
    });
    let grand_total = (sub_total - discount + tax).toFixed(2);
    return {sub_total: sub_total.toFixed(2), discount: discount.toFixed(2), tax: tax.toFixed(2), grand_total};
}

function updatePaymentPanel(){
    const totals = calculateTotals();
    paymentSubTotal.innerText = totals.sub_total + ' Ks';
    paymentDiscount.innerText = totals.discount + ' Ks';
    paymentTax.innerText = totals.tax + ' Ks';
    paymentTotal.innerText = totals.grand_total + ' Ks';
}

// Payment type
function setPaymentType(id){
    paymentTypeSelected=id;
    document.querySelectorAll('.payment-buttons button').forEach(b=>b.classList.remove('btn-primary'));
    document.querySelector(`.payment-buttons button[onclick="setPaymentType(${id})"]`).classList.add('btn-primary');
}

// Open/Close panels
ticketBtn.onclick=()=>{ if(cart.length===0){ alert('Cart empty'); return; } ticket.classList.add('show'); renderTicket(); }
function closeTicket(){ ticket.classList.remove('show'); }

chargeBtn.onclick=()=>{ 
    if(cart.length===0){ alert('Cart empty'); return; } 
    if(!supplier){ alert('Select supplier'); return; } 
    updatePaymentPanel(); paidAmount.value = calculateTotals().grand_total;
    paymentTypeSelected=''; document.querySelectorAll('.payment-buttons button').forEach(b=>b.classList.remove('btn-primary'));
    paymentPanel.classList.add('show'); 
};
function closePaymentPanel(){ paymentPanel.classList.remove('show'); }

// SAVE PURCHASE
function savePurchase(){
    if(!supplier){ alert('Select supplier'); return; }
    if(cart.length===0){ alert('Cart empty'); return; }
    if(!paymentTypeSelected){ alert('Select payment type'); return; }
    const paid=parseFloat(paidAmount.value); if(isNaN(paid)||paid<=0){ alert('Enter paid amount'); return; }

    const totals = calculateTotals();
    let data = new FormData();
    data.append('supplier_id', supplierId);
    data.append('payment_type_id', paymentTypeSelected);
    data.append('paid_amount', paid);
    data.append('cart_items', JSON.stringify(cart));
    data.append('sub_total', totals.sub_total);
    data.append('discount', totals.discount);
    data.append('tax', totals.tax);
    data.append('grand_total', totals.grand_total);
    data.append('balance_amount', (totals.grand_total - paid).toFixed(2));
    data.append('purchase_date', new Date().toISOString().slice(0,10));
    data.append('transaction_type_id', 1);

    fetch("{{ route('purchase_master.save') }}",{
        method:'POST',
        headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body:data
    })
    .then(res=>res.json())
    .then(res=>{
        if(res.status==='success'){
            alert('Purchase saved!');
            
            window.open(`/maxpos/purchase/print/${res.purchase_id}`,'_blank');
            cart=[]; supplier=null; supplierId=null; paymentTypeSelected='';
            ticketCount.innerText=0; ticketCartCount.innerText=0; supplierBtn.innerText='Select Supplier';
            updateButtonsState(); closeTicket(); closePaymentPanel(); renderTicket();
        } else alert('Failed to save');
    })
    .catch(err=>{ console.error(err); alert('Error saving'); });
}

// SEARCH
searchInput.oninput=()=>{ 
    const v=searchInput.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(p=>{ p.style.display=p.dataset.name.includes(v)?'':'none'; });
};
</script>
@endsection
