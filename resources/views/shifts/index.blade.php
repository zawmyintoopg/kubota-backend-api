@extends('layouts.master')

@section('title','Shift')

@section('content')
<div class="container-fluid">

    <h4 class="mb-3">⏰ Shift Management</h4>

    <!-- ==========================
        Opening Shift
    ========================== -->
    <div class="card mb-3">
        <div class="card-header fw-bold">🟢 Opening Shift</div>
        <div class="card-body d-flex gap-3 align-items-end">

            <form id="openShiftForm" action="{{ route('shifts.store') }}" method="POST" class="d-flex gap-2">
                @csrf
                <div>
                    <label>Opening Cash Balance</label>
                    <input type="number" name="opening_cash" class="form-control" required {{ $currentShift ? 'readonly' : '' }}>
                </div>
                <div>
                    <label>Remark</label>
                    <input type="text" name="remark" class="form-control" {{ $currentShift ? 'readonly' : '' }}>
                </div>
                <button type="submit" id="startShiftBtn" class="btn btn-success" {{ $currentShift ? 'disabled' : '' }}>
                    <span id="btnText"><i class="fas fa-play"></i> Start Shift</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </form>

        </div>
    </div>

    <!-- ==========================
        Closing Shift
    ========================== -->
    <div class="card mb-3">
        <div class="card-header fw-bold">🔴 Closing Shift</div>
        <div class="card-body d-flex gap-3 align-items-center">
            @if($currentShift)
                <form id="closeShiftForm" action="{{ route('shifts.close', $currentShift->id) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    <div>
                        <label>Cash Sales</label>
                        <input type="number" name="cash_sales" class="form-control" value="{{ $currentShift->cash_sales ?? 0 }}" readonly>
                    </div>
                    <div>
                        <label>Card Sales</label>
                        <input type="number" name="card_sales" class="form-control" value="{{ $currentShift->card_sales ?? 0 }}" readonly>
                    </div>
                    <div>
                        <label>Total Sales</label>
                        <input type="number" name="total_sales" class="form-control" value="{{ $currentShift->total_sales ?? 0 }}" readonly>
                    </div>
                    <div>
                        <label>Closing Cash</label>
                        <input type="number" name="closing_cash" class="form-control" required>
                    </div>
                    <div>
                        <label>Remark</label>
                        <input type="text" name="remark" class="form-control">
                    </div>
                    <button type="submit" id="closeShiftBtn" class="btn btn-danger">
                        <span id="closeText"><i class="fas fa-stop"></i> Close Shift</span>
                        <span id="closeSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </form>
                <p class="mt-2">Shift started at: {{ $currentShift->open_time }} | User: {{ $currentShift->user->name }}</p>
            @else
                <p>No open shift</p>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // --------------------
    // Loading Spinner on Start Shift
    // --------------------
    const openForm = document.getElementById('openShiftForm');
    const startBtn = document.getElementById('startShiftBtn');
    const btnText = document.getElementById('btnText');
    const btnSpinner = document.getElementById('btnSpinner');

    openForm.addEventListener('submit', function(){
        startBtn.setAttribute('disabled', true);
        btnText.classList.add('d-none');
        btnSpinner.classList.remove('d-none');
    });

    // --------------------
    // Loading Spinner on Close Shift
    // --------------------
    const closeForm = document.getElementById('closeShiftForm');
    const closeBtn = document.getElementById('closeShiftBtn');
    const closeText = document.getElementById('closeText');
    const closeSpinner = document.getElementById('closeSpinner');

    if(closeForm){
        closeForm.addEventListener('submit', function(){
            closeBtn.setAttribute('disabled', true);
            closeText.classList.add('d-none');
            closeSpinner.classList.remove('d-none');
        });
    }

    // --------------------
    // Auto-refresh totals for open shift (optional)
    // --------------------
    @if($currentShift)
    setInterval(function(){
        fetch('{{ route("shifts.index") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.currentShift){
                document.querySelector('input[name="cash_sales"]').value = data.currentShift.cash_sales ?? 0;
                document.querySelector('input[name="card_sales"]').value = data.currentShift.card_sales ?? 0;
                document.querySelector('input[name="total_sales"]').value = data.currentShift.total_sales ?? 0;
            }
        });
    }, 5000);
    @endif
</script>
@endpush
