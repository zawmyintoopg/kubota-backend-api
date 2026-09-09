@extends('layouts.master')

@section('title','Edit Sale - POS')

@section('content')
<div class="container mt-3">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('salePage') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <h4>Edit Sale - #{{ $sale->voucher_no }}</h4>
        <div></div>
    </div>

    {{-- Buttons --}}
    <div class="mb-3 d-flex gap-2">
        <button id="btn-refund" class="btn btn-warning">Refund Selected</button>
        <button id="btn-delete" class="btn btn-danger">Delete Selected</button>
    </div>

    {{-- Sale form --}}
    <form action="{{ route('sales.update', $sale->id) }}" method="POST" id="sale-form">
        @csrf
        <input type="hidden" name="items" id="items-json">
        <input type="hidden" name="total_amount" id="total_amount">
        <input type="hidden" name="grand_amount" id="grand_amount">
        <input type="hidden" name="paid_amount" id="paid-amount-hidden">

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="cart-table">
                <thead class="table-light">
                    <tr>
                        <th class="checkbox-column d-none"><input type="checkbox" id="check-all"></th>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Discount</th>
                        <th>Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @foreach($saleDetails as $index => $item)
                        @php
                            $amount = $item->qty * $item->price - ($item->discount ?? 0);
                            $grandTotal += $amount;
                        @endphp
                        <tr data-id="{{ $item->sale_detail_id }}">
                            <td class="checkbox-column d-none"><input type="checkbox" class="item-check"></td>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->unit_name }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>{{ number_format($item->price,2) }}</td>
                            <td>{{ number_format($item->discount ?? 0,2) }}</td>
                            <td class="amount">{{ number_format($amount,2) }}</td>
                            <td>
                                {{-- Single delete --}}
                                <form action="{{ route('delete.item', $item->sale_detail_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end"><strong>Grand Total:</strong></td>
                        <td id="grand-total-display"><strong>{{ number_format($grandTotal,2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Totals --}}
        <div class="row mt-3">
            <div class="col-md-4">
                <label>Discount</label>
                <input type="number" id="discount" class="form-control" value="{{ $sale->discount_amount }}">
            </div>
            <div class="col-md-4">
                <label>Tax</label>
                <input type="number" id="tax" class="form-control" value="{{ $sale->tax_amount }}">
            </div>
            <div class="col-md-4">
                <label>Paid Amount</label>
                <input type="number" id="paid-amount" class="form-control" value="{{ $sale->paid_amount }}">
            </div>
            <div class="col-md-12 mt-2">
                <label>Balance</label>
                <input type="text" id="balance" class="form-control" readonly value="{{ $sale->balance_amount }}">
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-3">Update Sale</button>
    </form>
</div>

{{-- JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
let deleteMode = false;
let refundMode = false;

// toggle checkboxes
$('#btn-delete').click(function(){
    deleteMode = !deleteMode;
    refundMode = false;
    toggleCheckboxes(deleteMode || refundMode);
});

$('#btn-refund').click(function(){
    refundMode = !refundMode;
    deleteMode = false;
    toggleCheckboxes(deleteMode || refundMode);
});

function toggleCheckboxes(show){
    if(show){
        $('.checkbox-column').removeClass('d-none');
    } else {
        $('.checkbox-column').addClass('d-none');
        $('.item-check').prop('checked',false);
    }
}

// check all
$('#check-all').change(function(){
    $('.item-check').prop('checked', $(this).prop('checked'));
});

// update totals
function updateTotals(){
    let total = 0;
    $('#cart-table tbody tr').each(function(){
        let row = $(this);
        let amount = parseFloat(row.find('.amount').text().replace(/,/g,'')) || 0;
        total += amount;
    });
    let discount = +$('#discount').val() || 0;
    let tax = +$('#tax').val() || 0;
    let grand = total - discount + tax;
    $('#grand-total-display').text(grand.toFixed(2));
    let paid = +$('#paid-amount').val() || 0;
    $('#balance').val((paid - grand).toFixed(2));
    $('#total_amount').val(total);
    $('#grand_amount').val(grand);
    $('#paid-amount-hidden').val(paid);
}

$('#discount,#tax,#paid-amount').on('input',updateTotals);
updateTotals();

// handle form submit
$('#sale-form').submit(function(e){
    if(deleteMode || refundMode){
        e.preventDefault();
        let mode = deleteMode ? 'delete' : 'refund';
        $('.item-check:checked').each(function(){
            $(this).closest('tr').remove();
        });
        toggleCheckboxes(false);
        updateTotals();
        alert('Selected items marked for '+mode+'. Now click Update Sale.');
    }
});
</script>
@endsection
