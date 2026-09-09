@extends('layouts.master')
@section('title','POS - Purchase')

@section('content')
<form id="purchaseForm" method="POST" action="{{ route('purchase_master.save') }}">
  @csrf

  <!-- Hidden Inputs for controller -->
  <input type="hidden" id="cart_items_input" name="cart_items">
  <input type="hidden" id="sub_total_input" name="sub_total">
  <input type="hidden" id="discount_input" name="discount">
  <input type="hidden" id="tax_input" name="tax">
  <input type="hidden" id="grand_total_input" name="grand_total">
  <input type="hidden" id="paid_input" name="paid_amount">
  <input type="hidden" id="balance_input" name="balance_amount">

  <div class="row">
    <!-- LEFT COLUMN -->
    <div class="col-md-8">
      <h3 class="text-muted">Purchase Transaction</h3>
      <div class="row mb-3">
        <div class="col-md-3">
          <label class="form-label">Date:</label>
          <input type="date" id="purchaseDate" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-3">
          <label class="form-label">Supplier:</label>
          <select class="form-select" name="supplier_id" required>
            <option disabled selected>Choose Supplier</option>
            @foreach($suppliers as $s)
              <option value="{{ $s->id }}">{{ $s->supplier_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Payment Method:</label>
          <select class="form-select" name="payment_type_id" required>
            <option disabled selected>Choose Payment</option>
            @foreach($paymentMethods as $p)
              <option value="{{ $p->id }}">{{ $p->payment_description }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Transaction Type:</label>
          <select class="form-select" name="transaction_type_id" required>
            <option disabled selected>Choose Transaction</option>
            @foreach($transactionTypes as $t)
              <option value="{{ $t->id }}">{{ $t->transaction_name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <!-- PRODUCT ENTRY -->
      <div class="row mb-3">
        <div class="col-8">
          <input type="text" id="productCodeInput" class="form-control" placeholder="Enter Product Code">
        </div>
        <div class="col-4 d-flex align-items-end">
          <button type="button" class="btn btn-primary w-100" data-mdb-toggle="modal" data-mdb-target="#productSearchModal">
            <i class="fas fa-search"></i> Search
          </button>
        </div>
      </div>

      <!-- CART TABLE -->
      <div class="card">
        <div class="card-body table-container">
          <table class="table table-hover cart-table">
            <thead>
              <tr>
                <th>#</th><th>Code</th><th>Product</th><th>Unit</th>
                <th>Qty</th><th>Buy</th><th>Discount</th><th>Amount</th><th>✕</th>
              </tr>
            </thead>
            <tbody id="cartBody"></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-md-4">
      <div class="card summary-card">
        <div class="card-body">
          <h5>Summary</h5>
          <p>Sub Total: <span id="subTotal">0 Ks</span></p>
          <input type="number" id="discountField" class="form-control mb-2" value="0" placeholder="Discount">
          <input type="number" id="taxField" class="form-control mb-2" value="0" placeholder="Tax">
          <p>Grand Total: <span id="grandTotal">0 Ks</span></p>
          <input type="number" id="paidField" class="form-control mb-2" value="0" placeholder="Paid">
          <p>Balance: <span id="balance">0 Ks</span></p>
          <div class="d-flex gap-2">
        <button type="submit" id="savePrintBtn" class="btn btn-success">
        <i class="fas fa-print"></i> Save & Print
    </button>
        <button type="button" class="btn btn-danger" id="cancelBtn"><i class="fas fa-times"></i> Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<!-- PRODUCT SEARCH MODAL -->
<div class="modal fade" id="productSearchModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Product Search</h5>
        <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="productSearchInput" class="form-control mb-2" placeholder="Search product name or code">
        <table class="table table-hover table-bordered">
          <thead>
            <tr><th>Code</th><th>Name</th><th>Unit</th><th>Buy Price</th><th>Action</th></tr>
          </thead>
          <tbody id="productSearchBody">
            @forelse($products as $product)
              @foreach($product->variants as $variant)
                <tr class="product-row"
                    data-product-id="{{ $product->id }}"
                    data-variant-id="{{ $variant->id }}"
                    data-code="{{ $product->product_code }}"
                    data-name="{{ $product->name }}"
                    data-unit="{{ $variant->unit->unit_name }}"
                    data-buy="{{ $variant->last_purchase_price }}">
                  <td>{{ $product->product_code }}</td>
                  <td>{{ $product->name }}</td>
                  <td>{{ $variant->unit->unit_name }}</td>
                  <td class="text-end">{{ number_format($variant->last_purchase_price,2) }}</td>
                  <td class="text-center text-muted">Click Row to Add</td>
                </tr>
              @endforeach
            @empty
              <tr><td colspan="5" class="text-center text-muted">No products found</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- SweetAlert -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let cart = [];

    const fmt = n => Number(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const savePrintBtn = document.getElementById('savePrintBtn');
    const purchaseForm = document.getElementById('purchaseForm');

    // =================== CART FUNCTIONS ===================
    function addToCart(product){
        const existing = cart.find(i => i.variant_id === product.variant_id);
        if(existing){
            Swal.fire({
                title: `"${product.name}" already added`,
                text: 'Add 1 more?',
                icon: 'question',
                showCancelButton: true
            }).then(res => {
                if(res.isConfirmed){
                    existing.qty += 1;
                    existing.amount = ((existing.qty * existing.buy) - existing.discount).toFixed(2);
                    renderCart();
                    updateSummary();
                }
            });
        } else {
            product.qty = 1;
            product.discount = 0;
            product.amount = product.buy.toFixed(2);
            cart.push(product);
            renderCart();
            updateSummary();
        }
    }

    function renderCart(){
        const tbody = document.getElementById('cartBody');
        tbody.innerHTML = '';
        cart.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.code}</td>
                <td>${item.name}</td>
                <td>${item.unit}</td>
                <td>
                    <input type="number" class="form-control form-control-sm qty-input" data-index="${index}" value="${item.qty}" min="1">
                </td>
                <td>${fmt(item.buy)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm discount-input" data-index="${index}" value="${item.discount}" min="0">
                </td>
                <td>${fmt(item.amount)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger remove-btn" data-index="${index}">✕</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        document.querySelectorAll('.qty-input').forEach(i => i.addEventListener('change', updateQty));
        document.querySelectorAll('.discount-input').forEach(i => i.addEventListener('change', updateDiscount));
        document.querySelectorAll('.remove-btn').forEach(b => b.addEventListener('click', removeItem));
    }

    function updateQty(e){
        const i = e.target.dataset.index;
        let qty = parseFloat(e.target.value);
        if(qty < 1){ qty = 1; e.target.value = 1; }
        cart[i].qty = qty;
        cart[i].amount = ((cart[i].buy * qty) - cart[i].discount).toFixed(2);
        renderCart();
        updateSummary();
    }

    function updateDiscount(e){
        const i = e.target.dataset.index;
        const d = parseFloat(e.target.value) || 0;
        cart[i].discount = d;
        cart[i].amount = ((cart[i].buy * cart[i].qty) - d).toFixed(2);
        renderCart();
        updateSummary();
    }

    function removeItem(e){
        cart.splice(e.target.dataset.index, 1);
        renderCart();
        updateSummary();
    }

    // =================== SUMMARY ===================
    function updateSummary(){
        let sub = cart.reduce((s,i) => s + (i.buy * i.qty), 0);
        let disc = cart.reduce((s,i) => s + i.discount, 0);
        let tax = parseFloat(taxField.value) || 0;
        let grand = sub - disc + tax;
        let paid = parseFloat(paidField.value) || 0;

        subTotal.innerText = fmt(sub) + ' Ks';
        grandTotal.innerText = fmt(grand) + ' Ks';
        balance.innerText = fmt(grand - paid) + ' Ks';

        sub_total_input.value = sub;
        discount_input.value = disc;
        tax_input.value = tax;
        grand_total_input.value = grand;
        paid_input.value = paid;
        balance_input.value = grand - paid;
    }

    taxField.addEventListener('input', updateSummary);
    paidField.addEventListener('input', updateSummary);

    // =================== PRODUCT SEARCH ===================
    productSearchInput.addEventListener('input', function(){
        const f = this.value.toLowerCase();
        document.querySelectorAll('#productSearchBody .product-row').forEach(r => {
            r.style.display =
                r.dataset.code.toLowerCase().includes(f) ||
                r.dataset.name.toLowerCase().includes(f) ? '' : 'none';
        });
    });

    productSearchBody.addEventListener('click', function(e){
        const row = e.target.closest('.product-row');
        if(!row) return;
        addToCart({
            id: parseInt(row.dataset.productId),
            variant_id: parseInt(row.dataset.variantId),
            code: row.dataset.code,
            name: row.dataset.name,
            unit: row.dataset.unit,
            buy: parseFloat(row.dataset.buy)
        });
        const modal = mdb.Modal.getInstance(productSearchModal);
        modal.hide();
    });

    productCodeInput.addEventListener('keypress', function(e){
        if(e.key === 'Enter'){
            e.preventDefault();
            const code = this.value.trim();
            const row = [...document.querySelectorAll('.product-row')]
                .find(r => r.dataset.code === code);

            if(row){
                addToCart({
                    id: parseInt(row.dataset.productId),
                    variant_id: parseInt(row.dataset.variantId),
                    code: row.dataset.code,
                    name: row.dataset.name,
                    unit: row.dataset.unit,
                    buy: parseFloat(row.dataset.buy)
                });
                this.value = '';
            } else {
                Swal.fire('Product not found','','warning');
            }
        }
    });

    // =================== SAVE & PRINT ===================
    purchaseForm.addEventListener('submit', function(e){
        e.preventDefault();

        if(cart.length === 0){
            Swal.fire('Cart is empty','','warning');
            return;
        }

        // Disable button & show spinner
        savePrintBtn.disabled = true;
        const originalHTML = savePrintBtn.innerHTML;
        savePrintBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...`;

        // SweetAlert loading
        Swal.fire({
            title: 'Saving...',
            html: 'Please wait while the purchase is being saved.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        // Save cart to hidden input
        cart_items_input.value = JSON.stringify(cart);

        // AJAX POST request
        fetch(purchaseForm.action, {
            method: 'POST',
            body: new FormData(purchaseForm),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            Swal.close();
            savePrintBtn.disabled = false;
            savePrintBtn.innerHTML = originalHTML;

            // Ask user to print
            Swal.fire({
                title: 'Purchase Saved!',
                text: 'Do you want to print the invoice?',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Yes, Print',
                cancelButtonText: 'No'
            }).then(result => {
                if(result.isConfirmed){
                    window.open(`/purchase/print/${data.purchase_id}`, '_blank');
                }
            });

            // Reset cart and form
            cart = [];
            renderCart();
            updateSummary();
            purchaseForm.reset();
        })
        .catch(err => {
            Swal.fire('Error','Failed to save purchase.','error');
            savePrintBtn.disabled = false;
            savePrintBtn.innerHTML = originalHTML;
            console.error(err);
        });
    });

    // =================== CANCEL ===================
    cancelBtn.addEventListener('click', function(){
        Swal.fire({
            title: 'Cancel Purchase?',
            icon: 'warning',
            showCancelButton: true
        }).then(r => {
            if(r.isConfirmed){
                cart = [];
                renderCart();
                updateSummary();
                purchaseForm.reset();
            }
        });
    });

});
</script>


@endsection
