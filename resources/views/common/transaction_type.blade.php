@extends('layouts.master')
@section('title','Transaction Type')

@section('content')

<style>
.modal input{
    color:#212529!important;
    background:#fff!important;
}
.req{color:red;font-weight:bold}

/* sorting */
th.sortable{cursor:pointer;user-select:none}
th.sortable .arrow{font-size:11px;margin-left:4px;color:#ddd}
th.sortable.active .arrow{color:#fff}

/* error */
.is-invalid{border-color:#dc3545!important}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">
        <i class="fas fa-exchange-alt text-primary me-2"></i> Transaction Type
    </h5>

    <button class="btn btn-outline-success btn-sm" onclick="openAdd()">
        <i class="fas fa-plus"></i> Create Transaction Type
    </button>
</div>

{{-- ================= TABLE ================= --}}
<div class="card shadow-2">
<div class="table-responsive">
<table class="table table-striped align-middle mb-0" id="ttTable">
<thead class="bg-primary text-white">
<tr>
    <th>#</th>
    <th class="sortable">Code <span class="arrow">⇅</span></th>
    <th class="sortable">Name <span class="arrow">⇅</span></th>
    <th>Status</th>
    <th class="text-center">Action</th>
</tr>
</thead>
<tbody>
@foreach($transactionTypes as $t)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $t->transaction_code }}</td>
    <td>{{ $t->transaction_name }}</td>
    <td>
        <span class="badge {{ $t->status=='Active'?'bg-success':'bg-secondary' }}">
            {{ $t->status }}
        </span>
    </td>
    <td class="text-center">
        <button class="btn btn-sm btn-light"
                onclick='openEdit(@json($t))'>
            <i class="fas fa-edit text-warning"></i>
        </button>

        <form id="del-{{ $t->id }}"
              method="POST"
              action="{{ route('transaction_types.destroy',$t->id) }}"
              class="d-inline">
            @csrf @method('DELETE')
            <button type="button"
                    class="btn btn-sm btn-light"
                    onclick="confirmDelete({{ $t->id }})">
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
            $currentPage = $transactionTypes->currentPage();
            $lastPage = $transactionTypes->lastPage();
            $startPage = max(1, $currentPage - ($currentPage-1) % 5);
            $endPage = min($lastPage, $startPage + 4);
        @endphp

        <nav>
            <ul class="pagination mb-0">
                {{-- Prev 5 pages --}}
                <li class="page-item {{ $startPage == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $transactionTypes->url(max(1, $startPage - 5)) }}">Prev</a>
                </li>

                {{-- Page numbers --}}
                @for($i = $startPage; $i <= $endPage; $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="{{ $transactionTypes->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                {{-- Next 5 pages --}}
                <li class="page-item {{ $endPage >= $lastPage ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $transactionTypes->url(min($lastPage, $endPage + 1)) }}">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

{{-- ================= MODAL ================= --}}
<div class="modal fade" id="ttModal" tabindex="-1">
<div class="modal-dialog modal-md modal-dialog-centered">
<div class="modal-content">

<form id="ttForm" method="POST">
@csrf
<input type="hidden" name="_method" id="formMethod">
<input type="hidden" id="formMode" value="{{ old('form_mode','add') }}">

<div class="modal-header">
    <h6 id="modalTitle">Add Transaction Type</h6>
    <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="row g-3">

<div class="col-12">
    <label>Transaction Code <span class="req">*</span></label>
    <input type="text"
           name="transaction_code"
           id="transaction_code"
           class="form-control @error('transaction_code') is-invalid @enderror"
           value="{{ old('transaction_code') }}"
           required>
</div>

<div class="col-12">
    <label>Transaction Name <span class="req">*</span></label>
    <input type="text"
           name="transaction_name"
           id="transaction_name"
           class="form-control"
           value="{{ old('transaction_name') }}"
           required>
</div>

<div class="col-12">
    <div class="form-check form-switch">
        <input class="form-check-input"
               type="checkbox"
               name="status"
               value="Active"
               {{ old('status','Active')=='Active'?'checked':'' }}>
        <label class="form-check-label">Active</label>
    </div>
</div>

</div>
</div>

<div class="modal-footer">
    <button type="button"
            class="btn btn-light"
            data-mdb-dismiss="modal">Close</button>
    <button type="submit"
            class="btn btn-primary"
            id="saveBtn">Save</button>
</div>

</form>
</div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const modalEl  = document.getElementById('ttModal');
const modal    = new mdb.Modal(modalEl);
const form     = document.getElementById('ttForm');
const saveBtn  = document.getElementById('saveBtn');
const codeInp  = document.getElementById('transaction_code');

/* ===== AUTOFOCUS (ADD ONLY) ===== */
modalEl.addEventListener('shown.mdb.modal',()=>{
    if(modalEl.dataset.mode==='add'){
        codeInp.focus(); codeInp.select();
    }
});

/* ===== ADD ===== */
function openAdd(){
    form.reset();
    modalEl.dataset.mode='add';
    formMode.value='add';

    modalTitle.innerText='Add Transaction Type';
    saveBtn.innerText='Save';
    saveBtn.disabled=false;

    document.querySelectorAll('.is-invalid')
        .forEach(el=>el.classList.remove('is-invalid'));

    form.action="{{ route('transaction_types.store') }}";
    formMethod.value='POST';

    modal.show();
}

/* ===== EDIT ===== */
function openEdit(t){
    modalEl.dataset.mode='edit';
    formMode.value='edit';

    modalTitle.innerText='Edit Transaction Type';
    saveBtn.innerText='Update';

    form.action=`/transaction-types/${t.id}`;
    formMethod.value='PUT';

    transaction_code.value=t.transaction_code;
    transaction_name.value=t.transaction_name;
    form.status.checked=t.status==='Active';

    modal.show();
}

/* ===== ENTER = SAVE ===== */
form.addEventListener('keydown',e=>{
    if(e.key==='Enter'){
        e.preventDefault();
        form.requestSubmit();
    }
});

/* ===== LOADING ===== */
form.addEventListener('submit',()=>{
    saveBtn.disabled=true;
    saveBtn.innerHTML=`<span class="spinner-border spinner-border-sm me-1"></span> Processing...`;

    Swal.fire({
        title:'Processing...',
        allowOutsideClick:false,
        didOpen:()=>Swal.showLoading()
    });
});

/* ===== DELETE ===== */
function confirmDelete(id){
    Swal.fire({
        title:'Delete?',
        text:'This cannot be undone',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33'
    }).then(r=>{
        if(r.isConfirmed){
            Swal.fire({
                title:'Deleting...',
                allowOutsideClick:false,
                didOpen:()=>Swal.showLoading()
            });
            document.getElementById('del-'+id).submit();
        }
    });
}

/* ===== SORTING ===== */
document.querySelectorAll('#ttTable th.sortable')
.forEach((th,idx)=>{
    let asc=true;
    th.addEventListener('click',()=>{
        const tbody=ttTable.tBodies[0];
        const rows=[...tbody.rows];

        document.querySelectorAll('th.sortable')
            .forEach(h=>h.classList.remove('active'));

        th.classList.add('active');
        th.querySelector('.arrow').textContent=asc?'▲':'▼';

        rows.sort((a,b)=>{
            const A=a.cells[idx].innerText.toLowerCase();
            const B=b.cells[idx].innerText.toLowerCase();
            return asc?A.localeCompare(B):B.localeCompare(A);
        });

        rows.forEach(r=>tbody.appendChild(r));
        asc=!asc;
    });
});

/* ===== GLOBAL SEARCH ===== */
document.addEventListener('DOMContentLoaded',()=>{
    const gs=document.getElementById('globalSearch');
    if(!gs) return;

    gs.addEventListener('keyup',()=>{
        const v=gs.value.toLowerCase();
        document.querySelectorAll('#ttTable tbody tr')
        .forEach(tr=>{
            tr.style.display=tr.innerText.toLowerCase().includes(v)?'':'none';
        });
    });
});

/* ===== DUPLICATE ERROR (KEEP OLD DATA) ===== */
@if($errors->any())
modalEl.dataset.mode='{{ old("form_mode","add") }}';
modal.show();
Swal.fire({
    icon:'error',
    title:'Duplicate Entry',
    text:'Transaction code already exists'
});
@endif

/* ===== SUCCESS ===== */
@if(session('success'))
Swal.fire({
    icon:'success',
    title:'Success',
    text:"{{ session('success') }}",
    timer:2000,
    showConfirmButton:false
});
@endif
</script>
@endpush
