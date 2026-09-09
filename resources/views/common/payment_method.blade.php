@extends('layouts.master')
@section('title','Payment Method')

@section('content')

<style>
.modal input,
.modal textarea{
    color:#212529!important;
    background:#fff!important;
}
.req{color:red;font-weight:bold}

th.sortable{cursor:pointer;user-select:none}
th.sortable .arrow{font-size:11px;margin-left:4px;color:#ddd}
th.sortable.active .arrow{color:#fff}

.is-invalid{border-color:#dc3545!important}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">
        <i class="fas fa-credit-card text-primary me-2"></i> Payment Method
    </h5>

    <button class="btn btn-outline-success btn-sm" onclick="openAdd()">
        <i class="fas fa-plus"></i> Create Payment Method
    </button>
</div>

{{-- ================= TABLE ================= --}}
<div class="card shadow-2">
<div class="table-responsive">
<table class="table table-striped align-middle mb-0" id="paymentTable">
<thead class="bg-primary text-white">
<tr>
    <th>#</th>
    <th class="sortable">Short Code <span class="arrow">⇅</span></th>
    <th class="sortable">Description <span class="arrow">⇅</span></th>
    <th>Status</th>
    <th class="text-center">Action</th>
</tr>
</thead>
<tbody>
@foreach($paymentMethods as $p)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $p->payment_short_code }}</td>
    <td>{{ $p->payment_description }}</td>
    <td>
        <span class="badge {{ $p->status=='Active'?'bg-success':'bg-secondary' }}">
            {{ $p->status }}
        </span>
    </td>
    <td class="text-center">
        <button class="btn btn-sm btn-light"
                onclick='openEdit(@json($p))'>
            <i class="fas fa-edit text-warning"></i>
        </button>

        <form id="del-{{ $p->id }}" method="POST"
              action="{{ route('payment_method.destroy',$p->id) }}"
              class="d-inline">
            @csrf @method('DELETE')
            <button type="button" class="btn btn-sm btn-light"
                    onclick="confirmDelete({{ $p->id }})">
                <i class="fas fa-trash text-danger"></i>
            </button>
        </form>
    </td>
</tr>
@endforeach
</tbody>
</table>
</div>


<div class="p-2 d-flex justify-content-center">
        @php
            $currentPage = $paymentMethods->currentPage();
            $lastPage = $paymentMethods->lastPage();
            $startPage = max(1, $currentPage - ($currentPage-1) % 5);
            $endPage = min($lastPage, $startPage + 4);
        @endphp

        <nav>
            <ul class="pagination mb-0">
                {{-- Prev 5 pages --}}
                <li class="page-item {{ $startPage == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paymentMethods->url(max(1, $startPage - 5)) }}">Prev</a>
                </li>

                {{-- Page numbers --}}
                @for($i = $startPage; $i <= $endPage; $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="{{ $paymentMethods->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                {{-- Next 5 pages --}}
                <li class="page-item {{ $endPage >= $lastPage ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $paymentMethods->url(min($lastPage, $endPage + 1)) }}">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

{{-- ================= MODAL ================= --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
<div class="modal-dialog modal-md modal-dialog-centered">
<div class="modal-content">

<form id="paymentForm" method="POST">
@csrf
<input type="hidden" name="_method" id="formMethod">

<div class="modal-header">
    <h6 id="modalTitle">Add Payment Method</h6>
    <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="row g-3">

<div class="col-12">
    <label>Short Code <span class="req">*</span></label>
    <input type="text" name="payment_short_code" id="payment_short_code"
           class="form-control @error('payment_short_code') is-invalid @enderror"
           required>
</div>

<div class="col-12">
    <label>Description</label>
    <textarea name="payment_description" id="payment_description"
              class="form-control" rows="2" required></textarea>
</div>

<div class="col-12">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox"
               name="status" value="Active" checked>
        <label class="form-check-label">Active</label>
    </div>
</div>

</div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light"
            data-mdb-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary" id="saveBtn">
        Save
    </button>
</div>

</form>
</div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const modalEl  = document.getElementById('paymentModal');
const modal    = new mdb.Modal(modalEl);
const form     = document.getElementById('paymentForm');
const saveBtn  = document.getElementById('saveBtn');
const shortInp = document.getElementById('payment_short_code');

/* ========= AUTOFOCUS ========= */
modalEl.addEventListener('shown.mdb.modal', ()=>{
    shortInp.focus();
    shortInp.select();
});

/* ========= ADD ========= */
function openAdd(){
    form.reset();
    modalTitle.innerText = 'Add Payment Method';
    saveBtn.innerText = 'Save';

    form.action = "{{ route('payment_method.store') }}";
    formMethod.value = 'POST';

    shortInp.classList.remove('is-invalid');

    modal.show();
}

/* ========= EDIT ========= */
function openEdit(p){
    openAdd();

    modalTitle.innerText = 'Edit Payment Method';
    saveBtn.innerText = 'Update';

    form.action = `/payment_method/${p.id}`;
    formMethod.value = 'PUT';

    shortInp.value = p.payment_short_code;
    payment_description.value = p.payment_description ?? '';
    document.querySelector('[name="status"]').checked = p.status === 'Active';
}

/* ========= ENTER = SAVE (100% WORKING) ========= */
form.addEventListener('keydown', function(e){
    if(e.key === 'Enter'){
        e.preventDefault();
        form.requestSubmit(); // ✅ REAL submit
    }
});

/* ========= LOADING ========= */
form.addEventListener('submit',()=>{
    saveBtn.disabled = true;
    saveBtn.innerHTML =
        `<span class="spinner-border spinner-border-sm me-1"></span> Processing...`;

    Swal.fire({
        title:'Processing...',
        allowOutsideClick:false,
        didOpen:()=>Swal.showLoading()
    });
});

/* ========= DELETE ========= */
function confirmDelete(id){
    Swal.fire({
        title:'Delete?',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33'
    }).then(r=>{
        if(r.isConfirmed){
            document.getElementById('del-'+id).submit();
        }
    });
}

/* ========= DUPLICATE ERROR SWEET ALERT ========= */
@if ($errors->has('payment_short_code'))
    modal.show();
    shortInp.classList.add('is-invalid');
    shortInp.focus();

    Swal.fire({
        icon: 'error',
        title: 'Duplicate Entry',
        text: '{{ $errors->first('payment_short_code') }}'
    });

    saveBtn.disabled = false;
    saveBtn.innerHTML = 'Save';
@endif

</script>
@endpush
