<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Receipt</title>

<style>
@page {
    size: 80mm auto;
    margin: 0;
}
body {
    font-family: monospace;
    font-size: 12px;
    margin: 5px;
}
.center { text-align: center; }
.right { text-align: right; }
hr {
    border: none;
    border-top: 1px dashed #000;
}
table {
    width: 100%;
    border-collapse: collapse;
}
td {
    padding: 2px 0;
}
.total td {
    font-weight: bold;
}
</style>
</head>

<body onload="window.print(); setTimeout(()=>window.close(),500);">

<div class="center">
    <strong>Fashion Shop</strong><br>
    Yangon, Myanmar<br>
    Tel: 09xxxxxxxx
</div>

<hr>

Date: {{ $sale->sale_date }}<br>
Invoice: #{{ $sale->id }}

<hr>

<table>
@foreach($sale->items as $item)
<tr>
    <td colspan="2">
    
    </td>
</tr>
<tr>
    <td></td>
    <td class="right"></td>
</tr>
@endforeach
</table>

<hr>

<table>
<tr>
    <td>Sub Total</td>
    <td class="right"></td>
</tr>
<tr>
    <td>Discount</td>
    <td class="right"></td>
</tr>
<tr>
    <td>Tax</td>
    <td class="right"></td>
</tr>
<tr class="total">
    <td>Grand Total</td>
    <td class="right"></td>
</tr>
<tr>
    <td>Paid</td>
    <td class="right"></td>
</tr>
<tr>
    <td>Balance</td>
    <td class="right"></td>
</tr>
</table>

<hr>

<div class="center">
    Thank You 🙏<br>
    Please Come Again
</div>

</body>
</html>
