@extends('layouts.master')
@section('title','Supplier')

@section('content')

<style>
.modal input,
.modal textarea{
    color:#212529!important;
    background:#fff!important;
}
.req{color:red;font-weight:bold}

/* ===== SORTING ===== */
th.sortable{cursor:pointer;user-select:none}
th.sortable .arrow{
    font-size:11px;
    margin-left:4px;
    color:#ddd;
}
th.sortable.active .arrow{
    color:#fff;
}

/* ===== ERROR ===== */
.is-invalid{border-color:#dc3545!important}
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">
        <i class="fas fa-truck text-primary me-2"></i> Supplier
    </h5>

    <button class="btn btn-outline-success btn-sm" onclick="openAdd()">
        <i class="fas fa-plus"></i> Create Supplier
    </button>
</div>

<!-- ================= TABLE ================= -->
<div class="card shadow-2">
<div class="table-responsive">
<table class="table table-striped align-middle mb-0" id="supplierTable">
<thead class="bg-primary text-white">
<tr>
    <th>#</th>
    <th class="sortable">Code <span class="arrow">⇅</span></th>
    <th class="sortable">Supplier Name <span class="arrow">⇅</span></th>
    <th class="sortable">Phone <span class="arrow">⇅</span></th>
    <th>Address</th>
    <th>Contact Person</th>
    <th>Status</th>
    <th class="text-center">Action</th>
</tr>
</thead>

<tbody>
@foreach($suppliers as $s)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $s->supplier_code }}</td>
    <td>{{ $s->supplier_name }}</td>
    <td>{{ $s->phone }}</td>
    <td>{{ $s->address }}</td>
    <td>{{ $s->contact_person }}</td>
    <td>
        <span class="badge {{ $s->status=='Active'?'bg-success':'bg-secondary' }}">
            {{ $s->status }}
        </span>
    </td>
    <td class="text-center">
        <button class="btn btn-sm btn-light"
            onclick='openEdit(@json($s))'>
            <i class="fas fa-edit text-warning"></i>
        </button>

        <form id="del-{{ $s->id }}" method="POST"
              action="{{ route('supplier.destroy',$s->id) }}"
              class="d-inline">
            @csrf @method('DELETE')
            <button type="button" class="btn btn-sm btn-light"
                    onclick="confirmDelete({{ $s->id }})">
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
            $currentPage = $suppliers->currentPage();
            $lastPage = $suppliers->lastPage();
            $startPage = max(1, $currentPage - ($currentPage-1) % 5);
            $endPage = min($lastPage, $startPage + 4);
        @endphp

        <nav>
            <ul class="pagination mb-0">
                {{-- Prev 5 pages --}}
                <li class="page-item {{ $startPage == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $suppliers->url(max(1, $startPage - 5)) }}">Prev</a>
                </li>

                {{-- Page numbers --}}
                @for($i = $startPage; $i <= $endPage; $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="{{ $suppliers->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                {{-- Next 5 pages --}}
                <li class="page-item {{ $endPage >= $lastPage ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $suppliers->url(min($lastPage, $endPage + 1)) }}">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="supplierModal" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<form id="supplierForm" method="POST">
@csrf
<input type="hidden" name="_method" id="formMethod">
<input type="hidden" name="edit_id" id="edit_id" value="{{ old('edit_id') }}">

<div class="modal-header">
    <h6 id="modalTitle">Add Supplier</h6>
    <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="row g-3">

<div class="col-md-6">
    <label>Supplier Name <span class="req">*</span></label>
    <input type="text"
           name="supplier_name"
           id="supplier_name"
           class="form-control {{ $errors->has('supplier_name')?'is-invalid':'' }}"
           value="{{ old('supplier_name') }}"
           required>
</div>

<div class="col-md-6">
    <label>Phone</label>
    <input type="text" name="phone" id="phone"
           class="form-control" value="{{ old('phone') }}">
</div>

<div class="col-12">
    <label>Address</label>
    <textarea name="address" id="address"
              class="form-control">{{ old('address') }}</textarea>
</div>

<div class="col-md-6">
    <label>Contact Person</label>
    <input type="text" name="contact_person"
           id="contact_person" class="form-control"
           value="{{ old('contact_person') }}">
</div>

<div class="col-12">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox"
               name="status" value="Active"
               {{ old('status','Active')=='Active'?'checked':'' }}>
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
const supplierModal = new mdb.Modal(document.getElementById('supplierModal'));
const form = document.getElementById('supplierForm');
const saveBtn = document.getElementById('saveBtn');

/* ===== ADD ===== */
function openAdd(){
    form.reset();
    edit_id.value='';
    modalTitle.innerText='Add Supplier';
    saveBtn.innerText='Save';
    saveBtn.disabled=false;
    saveBtn.innerHTML='Save';

    document.querySelectorAll('.is-invalid')
        .forEach(el=>el.classList.remove('is-invalid'));

    form.action="{{ route('supplier.store') }}";
    formMethod.value='POST';

    supplierModal.show();
}

/* ===== EDIT ===== */
function openEdit(s){
    openAdd();

    edit_id.value=s.id;
    modalTitle.innerText='Edit Supplier';
    saveBtn.innerText='Update';

    form.action=`/suppliers/${s.id}`;
    formMethod.value='PUT';

    supplier_name.value=s.supplier_name;
    phone.value=s.phone ?? '';
    address.value=s.address ?? '';
    contact_person.value=s.contact_person ?? '';
    document.querySelector('[name="status"]').checked=s.status==='Active';
}

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
            document.getElementById('del-'+id).submit();
        }
    });
}

/* ===== AUTOFUCUS ===== */
document.getElementById('supplierModal')
.addEventListener('shown.mdb.modal',()=>supplier_name.focus());

/* ===== LOADING ===== */
form.addEventListener('submit',()=>{
    saveBtn.disabled=true;
    saveBtn.innerHTML=`<span class="spinner-border spinner-border-sm me-1"></span> Processing...`;
    Swal.fire({title:'Processing...',allowOutsideClick:false,didOpen:()=>Swal.showLoading()});
});

/* ===== SORTING WITH ARROWS ===== */
document.querySelectorAll('#supplierTable th.sortable')
.forEach((th,idx)=>{
    let asc=true;
    th.addEventListener('click',()=>{
        const tbody=document.querySelector('#supplierTable tbody');
        const rows=[...tbody.querySelectorAll('tr')];

        document.querySelectorAll('th.sortable')
            .forEach(h=>h.classList.remove('active'));

        th.classList.add('active');
        th.querySelector('.arrow').textContent = asc ? '▲' : '▼';

        rows.sort((a,b)=>{
            const A=a.children[idx].innerText.toLowerCase();
            const B=b.children[idx].innerText.toLowerCase();
            return asc ? A.localeCompare(B) : B.localeCompare(A);
        });

        rows.forEach(r=>tbody.appendChild(r));
        asc=!asc;
    });
});

/* ===== GLOBAL SEARCH (MASTER INPUT) ===== */
document.addEventListener('DOMContentLoaded',()=>{
    const gs=document.getElementById('globalSearch');
    if(!gs) return;

    gs.addEventListener('keyup',()=>{
        const v=gs.value.toLowerCase();
        document.querySelectorAll('#supplierTable tbody tr')
        .forEach(tr=>{
            tr.style.display=tr.innerText.toLowerCase().includes(v)?'':'none';
        });
    });
});

/* ===== ERROR ===== */
@if($errors->any())
document.addEventListener('DOMContentLoaded',()=>{
    supplierModal.show();
    Swal.fire({icon:'error',title:'Duplicate / Validation Error'});
});
@endif

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
