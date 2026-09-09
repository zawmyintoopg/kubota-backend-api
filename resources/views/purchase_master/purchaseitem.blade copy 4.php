<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Purchase - POS</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<!-- Materialize CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

<style>
body { font-family: 'Roboto', sans-serif; background:#f5f5f5; padding-bottom:50px; }

/* TABLET WRAPPER */
.tablet-container { width:1024px; min-height:768px; margin:20px auto; border:2px solid #1976d2; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.3); padding:15px; background:#fff; }

/* HEADER BAR */
.header-bar { display:flex; justify-content:space-between; align-items:center; background:#1976d2; color:#fff; padding:10px 15px; border-radius:8px; margin-bottom:15px; }
.header-bar .back-btn, .header-bar .user-info { display:flex; align-items:center; gap:5px; cursor:pointer; font-weight:500; }
.header-bar .back-btn i { margin-right:5px; }

/* NAV HEADER */
#purchaseNav { display: flex; flex-wrap: wrap; gap: 10px; padding: 10px 0; flex:1; }
#purchaseNav .input-field { margin: 0; flex: 1 1 200px; }
#purchaseNav label { color: #1976d2 !important; font-weight:500; }

/* TABLES */
.table-container { overflow-x:auto; }
.cart-table { width:100%; border-collapse:collapse; min-width:900px; }
.cart-table th, .cart-table td { padding:6px 8px; text-align:center; border-bottom:1px solid #ddd; cursor:pointer; }
.cart-table th.sticky, .cart-table td.sticky { position: sticky; left:0; background:#f5f5f5; z-index:3; box-shadow:2px 0 5px -2px rgba(0,0,0,0.3);}
.cart-table th.sticky2, .cart-table td.sticky2 { position: sticky; left:50px; background:#f5f5f5; z-index:2; box-shadow:2px 0 5px -2px rgba(0,0,0,0.2);}

/* CARDS */
.card.summary-card { border-radius:10px; }

/* BUTTONS */
.btn { border-radius:8px; }

/* MODAL */
.modal { max-height:80%; }

/* TOAST */
.toast, .tablet-toast { background-color:#43a047 !important; }

/* AUTOCOMPLETE */
.autocomplete-suggestions {
    border:1px solid #ddd;
    max-height:200px;
    overflow-y:auto;
    position:absolute;
    background:#fff;
    z-index:999;
    width:100%;
}
.autocomplete-suggestion {
    padding:6px 10px;
    cursor:pointer;
}
.autocomplete-suggestion:hover {
    background:#eee;
}

/* RESPONSIVE */
@media only screen and (max-width:1024px){
    .tablet-container { width:768px; min-height:1024px; margin:10px auto; }
    #purchaseNav { flex-direction: column; gap:8px; }
    #purchaseNav .input-field { flex:1 1 100%; margin-bottom:10px; }
    .row > .col.s12.m8, .row > .col.s12.m4 { flex:1 1 100%; max-width:100%; }
}
</style>
</head>
<body>

<div id="tabletWrapper" class="tablet-container">

    <!-- HEADER BAR -->
    <div class="header-bar">
        <div class="back-btn" onclick="window.history.back()">
            <i class="fas fa-arrow-left"></i> Back to List
        </div>
        <div class="user-info">
            <i class="fas fa-user-circle"></i> Administrator
        </div>
    </div>

    <!-- NAV HEADER -->
    <div id="purchaseNav" class="row valign-wrapper">
        <div class="input-field col s6 m3">
            <input type="date" id="purchaseDate" value="{{ date('Y-m-d') }}">
            <label for="purchaseDate">Date</label>
        </div>
        <div class="input-field col s6 m3">
            <select id="supplierSelect">
                <option value="" disabled selected>Choose Supplier</option>
                <option value="1">Supplier 1</option>
                <option value="2">Supplier 2</option>
            </select>
            <label>Supplier</label>
        </div>
        <div class="input-field col s6 m3">
            <select id="paymentSelect">
                <option value="" disabled selected>Choose Payment</option>
                <option value="1">Cash</option>
                <option value="2">Card</option>
            </select>
            <label>Payment</label>
        </div>
        <div class="input-field col s6 m3">
            <select id="transactionSelect">
                <option value="" disabled selected>Choose Transaction</option>
                <option value="1">Purchase</option>
                <option value="2">Return</option>
            </select>
            <label>Transaction</label>
        </div>
    </div>

    <!-- PURCHASE FORM -->
    <form id="purchaseForm">
        <input type="hidden" id="sub_total_input" name="sub_total">
        <input type="hidden" id="discount_input" name="discount">
        <input type="hidden" id="tax_input" name="tax">
        <input type="hidden" id="grand_total_input" name="grand_total">
        <input type="hidden" id="paid_input" name="paid_amount">
        <input type="hidden" id="balance_input" name="balance_amount">

        <div class="row">
            <!-- CART TABLE -->
            <div class="col s12 m8">
                <div class="row" style="position:relative;">
                    <div class="input-field col s8" style="position:relative;">
                        <input type="text" id="productCodeInput" placeholder="Enter Product Code" autocomplete="off" autofocus>
                        <label for="productCodeInput">Product Code</label>
                        <div id="autocompleteList" class="autocomplete-suggestions"></div>
                    </div>
                    <div class="col s4">
                        <a class="btn modal-trigger" href="#productSearchModal">
                            <i class="fas fa-search"></i> Search
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content table-container">
                        <table class="cart-table highlight">
                            <thead>
                                <tr>
                                    <th class="sticky" style="width:50px">#</th>
                                    <th class="sticky2" style="width:100px">Code</th>
                                    <th>Product</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <th>Buy</th>
                                    <th>Discount</th>
                                    <th>Amount</th>
                                    <th>✕</th>
                                </tr>
                            </thead>
                            <tbody id="cartBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SUMMARY -->
            <div class="col s12 m4">
                <div class="card summary-card">
                    <div class="card-content">
                        <span class="card-title">Summary</span>
                        <div class="row">
                            <div class="col s12"><p>Sub Total: <span id="subTotal">0 Ks</span></p></div>
                            <div class="col s12">
                                <div class="input-field">
                                    <input type="number" id="discountField" value="0">
                                    <label for="discountField">Discount</label>
                                </div>
                            </div>
                            <div class="col s12">
                                <div class="input-field">
                                    <input type="number" id="taxField" value="0">
                                    <label for="taxField">Tax</label>
                                </div>
                            </div>
                            <div class="col s12"><p>Grand Total: <span id="grandTotal">0 Ks</span></p></div>
                            <div class="col s12">
                                <div class="input-field">
                                    <input type="number" id="paidField" value="0">
                                    <label for="paidField">Paid</label>
                                </div>
                            </div>
                            <div class="col s12"><p>Balance: <span id="balance">0 Ks</span></p></div>
                            <div class="col s12">
                                <a class="btn green" id="saveBtn"><i class="fas fa-save"></i> Save</a>
                                <a class="btn red" id="cancelBtn"><i class="fas fa-times"></i> Cancel</a>
                            </div>

                            <!-- PRINT OPTIONS -->
                            <div class="col s12" style="margin-top:15px;">
                                <select id="printSizeSelect">
                                    <option value="A4" selected>A4</option>
                                    <option value="80mm">80mm Thermal</option>
                                    <option value="45mm">45mm Thermal</option>
                                </select>
                                <label>Print Paper Size</label>
                                <a class="btn blue" id="printBtn"><i class="fas fa-print"></i> Print</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="cartInputs"></div>
    </form>

</div>

<!-- PRODUCT SEARCH MODAL -->
<div id="productSearchModal" class="modal">
    <div class="modal-content">
        <h5><i class="fas fa-box"></i> Product Search</h5>
        <div class="input-field">
            <input type="text" id="productSearchInput" placeholder="Search product name or code" autofocus>
            <label for="productSearchInput">Search Product</label>
        </div>
        <div class="table-container">
            <table class="highlight">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Unit</th>
                        <th>Buy</th>
                        <th>Qty</th>
                        <th>Add</th>
                    </tr>
                </thead>
                <tbody id="productSearchBody"></tbody>
            </table>
        </div>
    </div>
    <div class="modal-footer">
        <a class="modal-close btn grey">Close</a>
    </div>
</div>

<!-- EDIT MODAL -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <h5>Edit Item</h5>
    <div id="modalUnitDiv" style="margin-bottom:10px;">
        <label>Unit:</label>
        <select id="modalUnitSelect"></select>
    </div>
    <div id="modalQtyDiv" style="display:flex; align-items:center; gap:10px;">
        <label>Qty:</label>
        <a class="btn red" id="modalQtyMinus">-</a>
        <input type="number" id="modalQtyInput" min="1" style="width:80px; text-align:center;">
        <a class="btn green" id="modalQtyPlus">+</a>
    </div>
    <div style="margin-top:10px; font-size:1.2rem; color:green;">
      Price: <span id="modalPriceDisplay">0</span> Ks
    </div>
  </div>
  <div class="modal-footer">
    <a class="btn green modal-close" id="modalSaveBtn">Save</a>
    <a class="btn red modal-close">Cancel</a>
  </div>
</div>

<a id="tabletModeBtn" class="btn blue" style="position:fixed; top:10px; right:10px; z-index:999;">Exit Tablet Mode</a>

<!-- PRINT AREA (HIDDEN) -->
<div id="printArea" style="display:none;"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ===== INIT =====
document.addEventListener('DOMContentLoaded', ()=>{
    M.FormSelect.init(document.querySelectorAll('select'));
    M.Modal.init(document.querySelectorAll('.modal'));
    document.getElementById('productCodeInput').focus();
});

// ===== MOCK PRODUCTS =====
const products=[
  {id:1,product_code:'P001',name:'Product 1',variants:[
    {id:101,unit:{unit_name:'pcs'},last_purchase_price:100},
    {id:102,unit:{unit_name:'box'},last_purchase_price:950},
    {id:103,unit:{unit_name:'dozen'},last_purchase_price:1100},
  ]},
  {id:2,product_code:'P002',name:'Product 2',variants:[
    {id:201,unit:{unit_name:'pcs'},last_purchase_price:200},
    {id:202,unit:{unit_name:'box'},last_purchase_price:1800},
  ]}
];

let cart=[], isSubmitting=false;
const fmt=n=>new Intl.NumberFormat().format(n)+' Ks';

// ===== RENDER CART, CALCULATE TOTALS, AUTOCOMPLETE, MODAL LOGIC =====
// ... (Keep all your existing cart/product/modal JS from original code) ...

// ===== PRINT FUNCTION =====
document.getElementById('printBtn').addEventListener('click', () => {
    const size = document.getElementById('printSizeSelect').value;
    const printDiv = document.getElementById('printArea');
    printDiv.innerHTML = '';

    const supplier = document.getElementById('supplierSelect').selectedOptions[0]?.text || '';
    const date = document.getElementById('purchaseDate').value;
    const payment = document.getElementById('paymentSelect').selectedOptions[0]?.text || '';
    const transaction = document.getElementById('transactionSelect').selectedOptions[0]?.text || '';

    // Set styles based on paper size
    let width, fontSize, padding;
    if(size === 'A4'){ width = '210mm'; fontSize='12px'; padding='10px'; }
    else if(size==='80mm'){ width='80mm'; fontSize='10px'; padding='5px'; }
    else { width='45mm'; fontSize='8px'; padding='3px'; }

    let html = `<div style="font-family:Arial,sans-serif; width:${width}; padding:${padding}; font-size:${fontSize};">
        <h3 style="text-align:center; margin:0; font-size: ${fontSize};">MaxPOS Voucher</h3>
        <p style="margin:2px 0;">Date: ${date}</p>
        <p style="margin:2px 0;">Supplier: ${supplier}</p>
        <p style="margin:2px 0;">Payment: ${payment} | Transaction: ${transaction}</p>
        <hr style="border:1px dashed #000;">
        <table style="width:100%; border-collapse:collapse; font-size:${fontSize};">
            <thead>
                <tr>
                    <th>#</th><th>Code</th><th>Name</th><th>Unit</th><th>Qty</th><th>Price</th><th>Amt</th>
                </tr>
            </thead><tbody>`;

    cart.forEach((c,i)=>{
        const total=c.qty*c.buyprice-(c.discount||0);
        html+=`<tr>
            <td>${i+1}</td>
            <td>${c.product_code}</td>
            <td>${c.product_name}</td>
            <td>${c.unit}</td>
            <td style="text-align:right;">${c.qty}</td>
            <td style="text-align:right;">${c.buyprice}</td>
            <td style="text-align:right;">${total}</td>
        </tr>`;
    });

    html+=`</tbody></table>
        <hr style="border:1px dashed #000;">
        <p>Sub Total: ${document.getElementById('subTotal').innerText}</p>
        <p>Discount: ${document.getElementById('discountField').value} Ks</p>
        <p>Tax: ${document.getElementById('taxField').value} Ks</p>
        <p style="font-weight:bold;">Grand Total: ${document.getElementById('grandTotal').innerText}</p>
        <p>Paid: ${document.getElementById('paidField').value} Ks</p>
        <p>Balance: ${document.getElementById('balance').innerText}</p>
        <hr style="border:1px dashed #000;">
        <p style="text-align:center; margin:2px 0;">Thank you for your purchase!</p>
    </div>`;

    printDiv.innerHTML = html;

    const printWindow = window.open('', '', `width=600,height=600`);
    printWindow.document.write('<html><head><title>Voucher</title></head><body>');
    printWindow.document.write(html);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
});
</script>

</body>
</html>
