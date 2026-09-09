@extends('layouts.master')

@section('content')
<div class="container mt-4">

    <h4 class="mb-3">📊 Shift Summary</h4>

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered">
                <tr>
                    <th>Cashier</th>
                    <td>{{ $shift->user->name }}</td>
                </tr>
                <tr>
                    <th>Opened At</th>
                    <td>{{ $shift->open_time }}</td>
                </tr>
                <tr>
                    <th>Closed At</th>
                    <td>{{ $shift->close_time }}</td>
                </tr>
                <tr>
                    <th>Opening Cash</th>
                    <td>{{ number_format($shift->opening_cash,2) }}</td>
                </tr>
                <tr>
                    <th>Cash Sales</th>
                    <td>{{ number_format($shift->cash_sales,2) }}</td>
                </tr>
                <tr>
                    <th>Card Sales</th>
                    <td>{{ number_format($shift->card_sales,2) }}</td>
                </tr>
                <tr>
                    <th>Total Sales</th>
                    <td>{{ number_format($shift->total_sales,2) }}</td>
                </tr>
                <tr>
                    <th>Expected Cash</th>
                    <td>{{ number_format($shift->opening_cash + $shift->cash_sales,2) }}</td>
                </tr>
                <tr class="{{ $shift->difference < 0 ? 'table-danger' : 'table-success' }}">
                    <th>Difference</th>
                    <td>{{ number_format($shift->difference,2) }}</td>
                </tr>
            </table>

            <a href="{{ route('sales_master.sales') }}" class="btn btn-primary">
                ⬅ Back to POS
            </a>

        </div>
    </div>

</div>
@endsection
