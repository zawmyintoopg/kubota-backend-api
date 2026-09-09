<!DOCTYPE html>
<html lang="my">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>POS Menu</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.1/mdb.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* လိုအပ်သလို style ထည့်ထားနိုင် */
</style>
</head>
<body>

<div class="container mt-3">
    <h4>POS System</h4>
    <div>
        <button id="save-print" class="btn btn-success w-100">Save & Print</button>
    </div>
</div>

<!-- hidden receipt template -->
<div id="receipt-print" style="display:none;">
    <h3>Maxx POS</h3>
    <p>Customer: <span id="r-customer"></span></p>
    <p>Date: <span id="r-date"></span></p>
    <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr><th>Item</th><th>Total</th></tr>
        </thead>
        <tbody id="r-items"></tbody>
    </table>
    <p>Subtotal: <span id="r-subtotal"></span></p>
    <p>Discount: <span id="r-discount"></span></p>
    <p>Tax: <span id="r-tax"></span></p>
    <p>Total: <span id="r-total"></span></p>
    <p>Paid: <span id="r-paid"></span></p>
    <p>Change: <span id="r-change"></span></p>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
let cart = [
    {name:'Item A', qty:2, price:1000},
    {name:'Item B', qty:1, price:500}
];
let customerName = "Guest";
let subtotal = 2500;
let discount = 0;
let tax = 125;
let total = 2625;
let paid = 3000;
let change = 375;

$('#save-print').click(function(){
    // fill receipt template
    $('#r-customer').text(customerName);
    $('#r-date').text(new Date().toLocaleString());
    let itemsHTML = '';
    cart.forEach(i=>{
        itemsHTML += `<tr><td>${i.name} x${i.qty}</td><td>${i.price*i.qty}</td></tr>`;
    });
    $('#r-items').html(itemsHTML);
    $('#r-subtotal').text(subtotal);
    $('#r-discount').text(discount);
    $('#r-tax').text(tax);
    $('#r-total').text(total);
    $('#r-paid').text(paid);
    $('#r-change').text(change);

    // get HTML content
    let receiptHTML = document.getElementById('receipt-print').innerHTML;

    // check if Android interface available
    if(window.AndroidPOS && AndroidPOS.printReceipt){
        AndroidPOS.printReceipt(receiptHTML);
    }else{
        // browser fallback
        const w = window.open('', '', 'width=300,height=600');
        w.document.write(`<html><body onload="window.print();window.close()">${receiptHTML}</body></html>`);
        w.document.close();
    }
});
</script>
</body>
</html>
