@extends('layouts.master')
@section('title','Brand')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">
        <i class="fas fa-tags text-primary me-2"></i> Brand
    </h5>
    <button class="btn btn-outline-success btn-sm" onclick="openAdd()">
        <i class="fas fa-plus"></i> Create Brand
    </button>
</div>

<!-- ================= TABLE ================= -->
<div class="card shadow-2">
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle mb-0" id="brandTable">
            <thead class="bg-primary text-white">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Status</th>
                <th class="text-center">Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($brands as $c)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $c->brand_name }}</td>
                <td>
                    <span class="badge {{ $c->status=='active'?'bg-success':'bg-secondary' }}">
                        {{ $c->status }}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-sm btn-light"
                        onclick="openView('{{ $c->brand_name }}','{{ $c->status }}')">
                        <i class="fas fa-eye text-primary"></i>
                    </button>

                    <button class="btn btn-sm btn-light"
                        onclick="openEdit({{ $c->id }},'{{ $c->brand_name }}','{{ $c->status }}')">
                        <i class="fas fa-edit text-warning"></i>
                    </button>

                    <button class="btn btn-sm btn-light"
                        onclick="confirmDelete({{ $c->id }})">
                        <i class="fas fa-trash text-danger"></i>
                    </button>

                    <form id="del-{{ $c->id }}" method="POST"
                          action="{{ route('brand.destroy',$c->id) }}" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- ================= CUSTOM 5-PAGE PAGINATION ================= -->
    <div class="p-2 d-flex justify-content-center">
        @php
            $currentPage = $brands->currentPage();
            $lastPage = $brands->lastPage();
            $startPage = max(1, $currentPage - ($currentPage-1) % 5);
            $endPage = min($lastPage, $startPage + 4);
        @endphp

        <nav>
            <ul class="pagination mb-0">
                {{-- Prev 5 pages --}}
                <li class="page-item {{ $startPage == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $brands->url(max(1, $startPage - 5)) }}">Prev</a>
                </li>

                {{-- Page numbers --}}
                @for($i = $startPage; $i <= $endPage; $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="{{ $brands->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                {{-- Next 5 pages --}}
                <li class="page-item {{ $endPage >= $lastPage ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $brands->url(min($lastPage, $endPage + 1)) }}">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="BrandModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered modal-md">
<div class="modal-content">

<form id="BrandForm" method="POST">
@csrf
<input type="hidden" name="_method" id="formMethod">
<input type="hidden" name="id" id="editId" value="{{ old('id') }}">

<div class="modal-header">
    <h6 id="modalTitle">Add Brand</h6>
    <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
</div>

<div class="modal-body">

<!-- FORM MODE -->
<div id="formMode">
    <div class="form-outline mb-3">
        <input type="text"
               name="addName"
               id="catName"
               class="form-control @error('addName') is-invalid @enderror"
               value="{{ old('addName') }}"
               required>
        <label class="form-label">Brand Name</label>

        @error('addName')
            <small class="invalid-feedback">{{ $message }}</small>
        @enderror
    </div>

    <div class="form-check form-switch mt-2">
        <input class="form-check-input"
               type="checkbox"
               id="catStatus"
               name="addStatus"
               value="active"
               {{ old('addStatus','active')=='active'?'checked':'' }}>
        <label class="form-check-label">Active</label>
    </div>
</div>

<!-- VIEW MODE -->
<div id="viewMode" class="d-none">
    <table class="table table-bordered mb-0">
        <tr>
            <th width="40%">Brand Name</th>
            <td id="viewName"></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><span id="viewStatus" class="badge"></span></td>
        </tr>
    </table>
</div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light" id="btnClose" data-mdb-dismiss="modal">Close</button>
    <button class="btn btn-primary" id="saveBtn">
        <span class="btn-text">Save</span>
        <span class="spinner-border spinner-border-sm ms-2 d-none"></span>
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
const modalEl = document.getElementById('BrandModal');
const modal   = new mdb.Modal(modalEl);
const form    = document.getElementById('BrandForm');
const nameInp = document.getElementById('catName');
const statInp = document.getElementById('catStatus');
const saveBtn = document.getElementById('saveBtn');
const method  = document.getElementById('formMethod');
const editId  = document.getElementById('editId');

// Focus input on modal show
modalEl.addEventListener('shown.mdb.modal',()=>{
    if(!nameInp.disabled){
        nameInp.focus();
        nameInp.select();
    }
});

// Reset form function
function resetForm(){    
    form.reset();
    nameInp.classList.remove('is-invalid');
    const feedback = nameInp.parentNode.querySelector('.invalid-feedback');
    if(feedback) feedback.remove();
    editId.value='';
    method.value='POST';
}

// Reset button
function resetBtn(txt){
    saveBtn.disabled=false;
    saveBtn.querySelector('.btn-text').innerText=txt;
    saveBtn.querySelector('.spinner-border').classList.add('d-none');
}

// Close button or X resets form
document.getElementById('btnClose').addEventListener('click', resetForm);
modalEl.querySelector('.btn-close').addEventListener('click', resetForm);

// ADD
function openAdd(){
   
    document.getElementById('modalTitle').innerText='Add Brand';
    document.getElementById('formMode').classList.remove('d-none');
    document.getElementById('viewMode').classList.add('d-none');

    saveBtn.style.display='inline-block';
    form.action="{{ route('brand.store') }}";
    method.value='POST';

    @if(old('addName'))
        nameInp.value = "{{ old('addName') }}";
    @else
        nameInp.value='';
    @endif

    @if(old('addStatus'))
        statInp.checked = "{{ old('addStatus') }}" === 'active';
    @else
        statInp.checked=true;
    @endif

    resetBtn('Save');
    modal.show();
}

// EDIT
function openEdit(id,name,status){
    document.getElementById('modalTitle').innerText='Edit Brand';
    document.getElementById('formMode').classList.remove('d-none');
    document.getElementById('viewMode').classList.add('d-none');

    saveBtn.style.display='inline-block';
    form.action=`maxpos/update/${id}`;
    method.value='PUT';

    editId.value=id;
    nameInp.value=name;
    statInp.checked=status==='active';

    resetBtn('Update');
    modal.show();
}

// VIEW
function openView(name,status){
    document.getElementById('modalTitle').innerText='View Brand';
    document.getElementById('formMode').classList.add('d-none');
    document.getElementById('viewMode').classList.remove('d-none');

    document.getElementById('viewName').innerText=name;
    const badge=document.getElementById('viewStatus');
    badge.innerText=status;
    badge.className='badge '+(status==='active'?'bg-success':'bg-secondary');

    saveBtn.style.display='none';
    modal.show();
}

// SUBMIT
form.addEventListener('submit',(e)=>{
    e.preventDefault();
    const txt=method.value==='PUT'?'Updating...':'Saving...';
    saveBtn.disabled=true;
    saveBtn.querySelector('.btn-text').innerText=txt;
    saveBtn.querySelector('.spinner-border').classList.remove('d-none');

    Swal.fire({
        title:txt,
        allowOutsideClick:false,
        showConfirmButton:false,
        didOpen:()=>Swal.showLoading()
    });

    form.submit();
});

// DELETE
function confirmDelete(id){
    Swal.fire({
        title:'Delete?',
        icon:'warning',
        showCancelButton:true
    }).then(r=>{
        if(r.isConfirmed){
            Swal.fire({title:'Deleting...',showConfirmButton:false,didOpen:()=>Swal.showLoading()});
            document.getElementById('del-'+id).submit();
        }
    });
}

// SUCCESS
@if(session('success'))
Swal.fire({
    icon:'success',
    title:"{{ session('success') }}",
    showConfirmButton:false,
    timer:2000
});
@endif

// REOPEN MODAL ON VALIDATION ERROR
@if($errors->any())
document.addEventListener('DOMContentLoaded',()=>{
    @if(old('_method')==='PUT')
        openEdit("{{ old('id') }}","{{ old('addName') }}","{{ old('addStatus') }}");
    @else
        openAdd();
    @endif

    // Show validation error
    @error('addName')
        nameInp.classList.add('is-invalid');
        let feedback=nameInp.parentNode.querySelector('.invalid-feedback');
        if(!feedback){
            feedback=document.createElement('small');
            feedback.classList.add('invalid-feedback');
            nameInp.parentNode.appendChild(feedback);
        }
        feedback.innerText="{{ $message }}";
        nameInp.focus();
    @enderror
});
@endif
</script>
@endpush
