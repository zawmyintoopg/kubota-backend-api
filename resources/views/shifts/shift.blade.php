@extends('layouts.master')

@section('title','Shifts')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">⏰ Shifts</h4>

    <div class="row g-3 mb-3">
        <!-- Opening Shift Form -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-bold bg-white d-flex justify-content-between align-items-center">
                    Start Shift
                    @if($currentShift && $currentShift->status=='open')
                        <span class="badge bg-warning text-dark">Open</span>
                    @else
                        <span class="badge bg-secondary">Closed</span>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('shifts.store') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="number" name="opening_cash" placeholder="Opening Cash"
                               class="form-control"
                               {{ $currentShift && $currentShift->status=='open' ? 'readonly' : 'required' }}>
                        <button class="btn btn-success" type="submit"
                                {{ $currentShift && $currentShift->status=='open' ? 'disabled' : '' }}>
                            <i class="fas fa-play"></i> Start Shift
                        </button>
                    </form>
                    @if($currentShift)
                        <p class="mt-2">Started at: <strong>{{ $currentShift->open_time }}</strong></p>
                        <p>EDO Status: <strong>{{ ucfirst($currentShift->edo_status) }}</strong></p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Closing Shift Form -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header fw-bold bg-white d-flex justify-content-between align-items-center">
                    Close Shift
                    @if($currentShift && $currentShift->status=='open')
                        <span class="badge bg-warning text-dark">Open</span>
                    @elseif($currentShift && $currentShift->status=='closed')
                        <span class="badge bg-success">Closed</span>
                    @else
                        <span class="badge bg-secondary">N/A</span>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ $currentShift ? route('shift.close', $currentShift->id) : '#' }}"
                          method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="number" name="closing_cash" placeholder="Closing Cash"
                               class="form-control"
                               {{ !$currentShift || $currentShift->status=='closed' ? 'readonly' : 'required' }}>
                        <input type="number" name="cash_sales" placeholder="Cash Sales"
                               class="form-control"
                               {{ !$currentShift || $currentShift->status=='closed' ? 'readonly' : 'required' }}>
                        <input type="number" name="card_sales" placeholder="Card Sales"
                               class="form-control"
                               {{ !$currentShift || $currentShift->status=='closed' ? 'readonly' : 'required' }}>
                        <button class="btn btn-danger" type="submit"
                                {{ !$currentShift || $currentShift->status=='closed' ? 'disabled' : '' }}>
                            <i class="fas fa-stop"></i> Close Shift
                        </button>
                    </form>
                    @if($currentShift)
                        <p class="mt-2">Opened at: <strong>{{ $currentShift->open_time }}</strong></p>
                        <p>Status: <strong>{{ ucfirst($currentShift->status) }}</strong></p>
                        <p>EDO Status: <strong>{{ ucfirst($currentShift->edo_status) }}</strong></p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- All Shifts Table -->
    <div class="card">
        <div class="card-header fw-bold">All Shifts</div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Open Time</th>
                        <th>Close Time</th>
                        <th>Opening Cash</th>
                        <th>Closing Cash</th>
                        <th>Cash Sales</th>
                        <th>Card Sales</th>
                        <th>Total Sales</th>
                        <th>Difference</th>
                        <th>Status</th>
                        <th>EDO Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @forelse($shifts as $shift)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $shift->user->name }}</td>
                            <td>{{ $shift->transdate }}</td>
                            <td>{{ $shift->open_time }}</td>
                            <td>{{ $shift->close_time ?? '-' }}</td>
                            <td>{{ number_format($shift->opening_cash) }}</td>
                            <td>{{ number_format($shift->closing_cash ?? 0) }}</td>
                            <td>{{ number_format($shift->cash_sales ?? 0) }}</td>
                            <td>{{ number_format($shift->card_sales ?? 0) }}</td>
                            <td>{{ number_format($shift->total_sales ?? 0) }}</td>
                            <td>{{ number_format($shift->difference ?? 0) }}</td>
                            <td>
                                @if($shift->status=='open')
                                    <span class="badge bg-warning text-dark">Open</span>
                                @else
                                    <span class="badge bg-success">Closed</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($shift->edo_status ?? '-') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted">No shifts found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
