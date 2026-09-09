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
                                <div class="col s12" style="display:flex; gap:5px; flex-wrap:wrap;">
                                    <a class="btn green" id="saveBtn"><i class="fas fa-save"></i> Save</a>
                                    <a class="btn red" id="cancelBtn"><i class="fas fa-times"></i> Cancel</a>
                                    <a id="printBtn" class="btn orange"><i class="fas fa-print"></i> Print</a>
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

    // ===== RENDER CART =====
    function render(){
        const cartBody=document.getElementById('cartBody');
        const cartInputs=document.getElementById('cartInputs');
        cartBody.innerHTML=''; cartInputs.innerHTML='';
        cart.forEach((c,i)=>{
            const total=(c.qty*c.buyprice-(c.discount||0));
            cartBody.insertAdjacentHTML('beforeend',`
                <tr>
                    <td class="sticky">${i+1}</td>
                    <td class="sticky2">${c.product_code}</td>
                    <td>${c.product_name}</td>
                    <td onclick="openEditModal(${i}, 'unit')">${c.unit}</td>
                    <td onclick="openEditModal(${i}, 'qty')">${c.qty}</td>
                    <td>${c.buyprice}</td>
                    <td>${c.discount||0}</td>
                    <td>${fmt(total)}</td>
                    <td><a class="btn-small red" onclick="removeFromCart(${i}); event.stopPropagation();">✕</a></td>
                </tr>
            `);
            cartInputs.insertAdjacentHTML('beforeend',`
                <input type="hidden" name="items[${i}][product_id]" value="${c.product_id}">
                <input type="hidden" name="items[${i}][variant_id]" value="${c.variant_id}">
                <input type="hidden" name="items[${i}][qty]" value="${c.qty}">
                <input type="hidden" name="items[${i}][buyprice]" value="${c.buyprice}">
                <input type="hidden" name="items[${i}][discount]" value="${c.discount||0}">
                <input type="hidden" name="items[${i}][total_amount]" value="${total}">
            `);
        });
        calculateTotals();
    }

    // ===== CALCULATE TOTALS =====
    function calculateTotals(){
        const sub=cart.reduce((s,c)=>s+(c.qty*c.buyprice-(c.discount||0)),0);
        const d=Number(document.getElementById('discountField').value||0);
        const t=Number(document.getElementById('taxField').value||0);
        const p=Number(document.getElementById('paidField').value||0);
        const grand=sub-d+t;
        document.getElementById('subTotal').innerText=fmt(sub);
        document.getElementById('grandTotal').innerText=fmt(grand);
        document.getElementById('balance').innerText=fmt(grand-p);
        document.getElementById('sub_total_input').value=sub;
        document.getElementById('discount_input').value=d;
        document.getElementById('tax_input').value=t;
        document.getElementById('grand_total_input').value=grand;
        document.getElementById('paid_input').value=p;
        document.getElementById('balance_input').value=grand-p;
    }

    ['discountField','taxField','paidField'].forEach(id=>document.getElementById(id).addEventListener('input',calculateTotals));

    function showToast(message,color='green'){ 
        M.toast({html:`<span>${message}</span>`, classes:`${color} darken-1 tablet-toast`, displayLength:2500}); 
    }
    function removeFromCart(i){ cart.splice(i,1); render(); }

    // ===== ADD PRODUCT =====
    function addToCart(pid,vid){
        const p=products.find(x=>x.id===pid);
        const v=p.variants.find(x=>x.id===vid);
        const existing = cart.find(x=>x.product_id===pid && x.variant_id===vid);
        if(existing){
            existing.qty += 1;
            render();
            showToast('Added 1 more');
            return;
        }
        cart.push({product_id:pid,variant_id:vid,product_code:p.product_code,product_name:p.name,unit:v.unit.unit_name,qty:1,buyprice:v.last_purchase_price,discount:0});
        render();
        showToast('Product added','green');
    }

    // ===== PRODUCT SEARCH MODAL =====
    // Live search and table render
    const productSearchInput = document.getElementById('productSearchInput');
    const productSearchBody = document.getElementById('productSearchBody');

    function renderProductSearchTable(filter='') {
        productSearchBody.innerHTML = '';
        const f = filter.trim().toLowerCase();
        products.forEach(p => {
            p.variants.forEach(v => {
                const text = (p.product_code + ' ' + p.name + ' ' + v.unit.unit_name).toLowerCase();
                if(text.includes(f)) {
                    const tr = document.createElement('tr');
                    tr.addEventListener('click', () => {
                        addToCart(p.id, v.id);
                        const modal = M.Modal.getInstance(document.getElementById('productSearchModal'));
                        modal.close();
                    });
                    tr.innerHTML = `
                        <td>${p.product_code}</td>
                        <td>${p.name}</td>
                        <td>${v.unit.unit_name}</td>
                        <td>${v.last_purchase_price}</td>
                        <td><a class="btn-small blue" onclick="addToCart(${p.id},${v.id}); event.stopPropagation(); showToast('Added','green')">+1</a></td>
                        <td><a class="btn-small green" onclick="addToCart(${p.id},${v.id}); event.stopPropagation(); showToast('Added','green')">Add</a></td>
                    `;
                    productSearchBody.appendChild(tr);
                }
            });
        });
    }
    document.addEventListener('DOMContentLoaded', () => { renderProductSearchTable(); });
    productSearchInput.addEventListener('input', () => { renderProductSearchTable(productSearchInput.value); });

    // ===== AUTOCOMPLETE SEARCH =====
    const productCodeInput = document.getElementById('productCodeInput');
    const autocompleteList = document.getElementById('autocompleteList');
    productCodeInput.addEventListener('input', function(){
        const val = this.value.trim().toLowerCase();
        autocompleteList.innerHTML = '';
        if(val==='') return;
        products.forEach(p=>{
            p.variants.forEach(v=>{
                const text = (p.product_code + ' ' + p.name + ' ' + v.unit.unit_name).toLowerCase();
                if(text.includes(val)){
                    const div = document.createElement('div');
                    div.classList.add('autocomplete-suggestion');
                    div.innerText = `${p.product_code} | ${p.name} | ${v.unit.unit_name}`;
                    div.addEventListener('click', ()=>{
                        addToCart(p.id, v.id);
                        productCodeInput.value = '';
                        autocompleteList.innerHTML = '';
                    });
                    autocompleteList.appendChild(div);
                }
            });
        });
    });
    document.addEventListener('click', function(e){
        if(!productCodeInput.contains(e.target) && !autocompleteList.contains(e.target)){
            autocompleteList.innerHTML = '';
        }
    });

    // ===== QUICK ADD BY ENTER =====
    productCodeInput.addEventListener('keypress', e=>{
        if(e.key==='Enter'){ 
            e.preventDefault();
            const code=e.target.value.trim().toLowerCase();
            const p=products.find(x=>x.product_code.toLowerCase()===code);
            if(p){ addToCart(p.id,p.variants[0].id); e.target.value=''; }
            else Swal.fire('Not Found','Product code not found','error');
        }
    });

    // ===== SAVE & CANCEL =====
    document.getElementById('saveBtn').addEventListener('click',()=>{
        if(isSubmitting) return;
        if(Number(document.getElementById('paidField').value) < Number(document.getElementById('grand_total_input').value)){
            Swal.fire('Invalid Paid','Check paid amount','error'); return;
        }
        isSubmitting=true;
        showToast('Purchase saved!','green');
    });
    document.getElementById('cancelBtn').addEventListener('click',()=>{ cart=[]; render(); showToast('Purchase cancelled','red'); });

    // ===== TABLET MODE =====
    let tabletMode=true;
    const wrapper=document.getElementById('tabletWrapper');
    const btn=document.getElementById('tabletModeBtn');
    function setTabletLandscape(){
        wrapper.classList.add('tablet-container');
        wrapper.style.width='1024px';
        wrapper.style.minHeight='768px';
        btn.innerText='Exit Tablet Mode';
    }
    document.addEventListener('DOMContentLoaded', setTabletLandscape);
    btn.addEventListener('click', ()=>{
        tabletMode=!tabletMode;
        if(tabletMode){
            setTabletLandscape();
        } else {
            wrapper.classList.remove('tablet-container');
            wrapper.style.width='';
            wrapper.style.minHeight='';
            btn.innerText='Toggle Tablet Mode';
        }
    });

    // ===== EDIT MODAL =====
    let editingIndex = null;
    let editField = null;

    function openEditModal(i, field){
        editingIndex = i;
        editField = field;
        const item = cart[i];
        const product = products.find(p => p.id === item.product_id);

        const unitDiv = document.getElementById('modalUnitDiv');
        const qtyDiv = document.getElementById('modalQtyDiv');

        if(field === 'unit'){
            unitDiv.style.display='block';
            qtyDiv.style.display='none';
            const unitSelect = document.getElementById('modalUnitSelect');
            unitSelect.innerHTML='';
            product.variants.forEach(v=>{
                const option=document.createElement('option');
                option.value=v.id;
                option.text=v.unit.unit_name;
                if(v.id===item.variant_id) option.selected=true;
                unitSelect.appendChild(option);
            });
            M.FormSelect.init(unitSelect);

            unitSelect.addEventListener('change', ()=>{
                const variantId = Number(unitSelect.value);
                const variant = product.variants.find(v=>v.id===variantId);
                document.getElementById('modalPriceDisplay').innerText=variant.last_purchase_price;
            });

        } else if(field==='qty'){
            unitDiv.style.display='none';
            qtyDiv.style.display='flex';
            document.getElementById('modalQtyInput').value=item.qty;
        }

        const variant = product.variants.find(v=>v.id===item.variant_id);
        document.getElementById('modalPriceDisplay').innerText=variant.last_purchase_price;

        M.Modal.getInstance(document.getElementById('editModal')).open();
    }

    // ===== QTY -/+ =====
    document.getElementById('modalQtyPlus').addEventListener('click', ()=>{
        let val=Number(document.getElementById('modalQtyInput').value)||1;
        document.getElementById('modalQtyInput').value=val+1;
    });
    document.getElementById('modalQtyMinus').addEventListener('click', ()=>{
        let val=Number(document.getElementById('modalQtyInput').value)||1;
        if(val>1) document.getElementById('modalQtyInput').value=val-1;
    });

    // ===== SAVE EDIT =====
    document.getElementById('modalSaveBtn').addEventListener('click', ()=>{
        const item = cart[editingIndex];
        const product = products.find(p => p.id === item.product_id);

        if(editField==='unit'){
            const variantId = Number(document.getElementById('modalUnitSelect').value);
            const variant = product.variants.find(v=>v.id===variantId);
            item.variant_id = variant.id;
            item.unit = variant.unit.unit_name;
            item.buyprice = variant.last_purchase_price; 
        } else if(editField==='qty'){
            item.qty = Number(document.getElementById('modalQtyInput').value);
        }
        render();
    });

    // ===== PRINT RECEIPT =====
    document.getElementById('printBtn').addEventListener('click', ()=>{
        if(cart.length===0){ Swal.fire('Empty Cart','Add items before printing','error'); return; }

        Swal.fire({
            title: 'Select Paper Size',
            input: 'select',
            inputOptions: { '80mm':'80mm', '45mm':'45mm' },
            inputPlaceholder: 'Select size',
            showCancelButton: true,
        }).then(result=>{
            if(result.isConfirmed){
                const paperSize = result.value;
                let style = `<style>
                    body{ font-family: monospace; font-size:12px; }
                    .receipt{ width:${paperSize}; margin:0 auto; }
                    .header{text-align:center;font-weight:bold;font-size:14px;margin-bottom:5px;}
                    .line{border-bottom:1px dashed #000;margin:2px 0;}
                    table{width:100%;border-collapse:collapse;}
                    td{padding:2px 0;}
                    .right{text-align:right;}
                    .center{text-align:center;}
                </style>`;

                let content = `<div class="receipt">
                    <div class="header">Max POS System</div>
                    <div class="center">Purchase Receipt</div>
                    <div>Date: ${document.getElementById('purchaseDate').value}</div>
                    <div>Supplier: ${document.getElementById('supplierSelect').selectedOptions[0].text}</div>
                    <div class="line"></div>
                    <table>
                        <thead>
                            <tr>
                                <td>Item</td><td class="right">Qty</td><td class="right">Price</td><td class="right">Total</td>
                            </tr>
                        </thead>
                        <tbody>`;

                cart.forEach(c=>{
                    let total=(c.qty*c.buyprice-(c.discount||0));
                    content += `<tr>
                        <td>${c.product_name}</td>
                        <td class="right">${c.qty}</td>
                        <td class="right">${c.buyprice}</td>
                        <td class="right">${total}</td>
                    </tr>`;
                });

                content += `</tbody></table>
                    <div class="line"></div>
                    <table>
                        <tr><td>Sub Total:</td><td class="right">${document.getElementById('sub_total_input').value} Ks</td></tr>
                        <tr><td>Discount:</td><td class="right">${document.getElementById('discount_input').value} Ks</td></tr>
                        <tr><td>Tax:</td><td class="right">${document.getElementById('tax_input').value} Ks</td></tr>
                        <tr><td><b>Grand Total:</b></td><td class="right"><b>${document.getElementById('grand_total_input').value} Ks</b></td></tr>
                        <tr><td>Paid:</td><td class="right">${document.getElementById('paid_input').value} Ks</td></tr>
                        <tr><td>Balance:</td><td class="right">${document.getElementById('balance_input').value} Ks</td></tr>
                    </table>
                    <div class="center" style="margin-top:5px;">Thank you for your purchase!</div>
                </div>`;

                const printWindow = window.open('', '_blank');
                printWindow.document.write(style + content);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }
        });
    });
    </script>

    </body>
    </html>
