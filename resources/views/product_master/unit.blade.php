@extends('layouts.master')
@section('title','Unit Management')

@section('content')

<div class="d-flex justify-content-between mb-3">
    <h5 class="fw-bold">
        <i class="fas fa-ruler-combined text-primary me-2"></i> Units
    </h5>
    <button class="btn btn-outline-success btn-sm" onclick="openAdd()">
        <i class="fas fa-plus"></i> Add Unit
    </button>
</div>

{{-- ================= TABLE ================= --}}
<div class="card shadow-2">
    <div class="table-responsive">
        <table class="table table-sm table-striped align-middle mb-0" id="unitTable">
            <thead class="bg-primary text-white">
                <tr>
                    <th>#</th>
                    <th>Unit Name</th>
                    <th>To Base</th>
                    <th>Remarks</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($units as $u)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $u->unit_name }}</td>
                    <td>{{ $u->to_base }}</td>
                    <td>{{ $u->remarks ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $u->status=='Active'?'bg-success':'bg-secondary' }}">
                            {{ $u->status }}
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light" 
                                onclick="openEdit({{ $u->id }}, '{{ addslashes($u->unit_name) }}', {{ $u->to_base }}, '{{ addslashes($u->remarks) }}', '{{ $u->status }}')">
                            <i class="fas fa-edit text-warning"></i>
                        </button>

                        <form class="d-inline" method="POST" action="{{ route('unit.destroy',$u->id) }}">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-light" onclick="confirmDelete(this)">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="p-2 d-flex justify-content-center">
        @php
            $currentPage = $units->currentPage();
            $lastPage = $units->lastPage();
            $startPage = max(1, $currentPage - ($currentPage-1) % 5);
            $endPage = min($lastPage, $startPage + 4);
        @endphp

        <nav>
            <ul class="pagination mb-0">
                {{-- Prev 5 pages --}}
                <li class="page-item {{ $startPage == 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $units->url(max(1, $startPage - 5)) }}">Prev</a>
                </li>

                {{-- Page numbers --}}
                @for($i = $startPage; $i <= $endPage; $i++)
                    <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                        <a class="page-link" href="{{ $units->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                {{-- Next 5 pages --}}
                <li class="page-item {{ $endPage >= $lastPage ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $units->url(min($lastPage, $endPage + 1)) }}">Next</a>
                </li>
            </ul>
        </nav>
    </div>

{{-- ================= ADD / EDIT MODAL ================= --}}
<div class="modal fade" id="unitModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="unitForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod">

                <div class="modal-header">
                    <h6 class="mb-0" id="modalTitle">Add Unit</h6>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Unit Name</label>
                        <input type="text" name="unit_name" id="unit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>To Base (1 × ?)</label>
                        <input type="number" name="to_base" id="to_base" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Remarks</label>
                        <input type="text" name="remarks" id="remarks" class="form-control">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="statusToggle" checked>
                        <label class="form-check-label" id="statusLabel">Active</label>
                    </div>
                    <input type="hidden" name="status" id="status" value="Active">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-mdb-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="saveBtn" type="submit">
                        <i class="fas fa-save me-1"></i> Save
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
const unitModal = new mdb.Modal(document.getElementById('unitModal'));
const unitForm = document.getElementById('unitForm');
const formMethod = document.getElementById('formMethod');
const unit_name = document.getElementById('unit_name');
const to_base = document.getElementById('to_base');
const remarks = document.getElementById('remarks');
const statusToggle = document.getElementById('statusToggle');
const statusLabel = document.getElementById('statusLabel');
const status = document.getElementById('status');
const saveBtn = document.getElementById('saveBtn');

let isSubmitting = false;

function openAdd() {
    unitForm.reset();
    formMethod.value = 'POST';
    unitForm.action = "{{ route('unit.store') }}";
    statusToggle.checked = true;
    status.value = 'Active';
    statusLabel.innerText = 'Active';
    unitModal.show();
}

function openEdit(id, name, base, rem, stat) {
    openAdd();
    formMethod.value = 'PUT';
    unitForm.action = `/unit/${id}`;
    unit_name.value = name;
    to_base.value = base;
    remarks.value = rem;
    statusToggle.checked = stat == 'Active';
    status.value = stat;
    statusLabel.innerText = stat;
}

statusToggle.onchange = () => {
    status.value = statusToggle.checked ? 'Active' : 'Disable';
    statusLabel.innerText = status.value;
}

unitForm.addEventListener('submit', e => {
    if(isSubmitting){ e.preventDefault(); return; }
    isSubmitting = true;
    saveBtn.disabled = true;
    Swal.fire({
        title:'Saving...',
        allowOutsideClick:false,
        showConfirmButton:false,
        didOpen:()=>Swal.showLoading()
    });
    setTimeout(()=>unitForm.submit(), 150);
});

document.querySelectorAll('#unitTable button[onclick*="confirmDelete"]').forEach(btn=>{
    btn.addEventListener('click',()=>confirmDelete(btn));
});

function confirmDelete(btn){
    Swal.fire({
        title:'Delete?',
        icon:'warning',
        showCancelButton:true
    }).then(r=>{
        if(r.isConfirmed) btn.closest('form').submit();
    });
}

@if(session('success'))
Swal.fire({
    icon:'success',
    title:'Success',
    text:@json(session('success')),
    timer:2000,
    showConfirmButton:false
});
@endif
</script>
@endpush
