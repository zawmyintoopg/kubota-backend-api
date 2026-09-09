@extends('layouts.master')
@section('title','Unit')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">
        <i class="fas fa-tags text-primary me-2"></i> Unit
    </h5>
    <button class="btn btn-outline-success btn-sm" onclick="openAdd()">
        <i class="fas fa-plus"></i> Create Unit
    </button>
</div>

<!-- ================= TABLE ================= -->
<div class="card shadow-2">
    <div class="table-responsive">
        <table class="table align-middle table-striped table-sm mb-0" id="categoryTable">
            <thead class="bg-primary text-white">
                <tr>
                    <th>#</th>
                    <th onclick="sortTable(1)" style="cursor:pointer">Name <i class="fas fa-sort ms-1"></i></th>
                    <th onclick="sortTable(2)" style="cursor:pointer">Status <i class="fas fa-sort ms-1"></i></th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($categories as $c)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $c->name }}</td>
                    <td>
                        <span class="badge {{ $c->status=='Active'?'bg-success':'bg-secondary' }}">{{ $c->status }}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-light" onclick="openView('{{ $c->name }}','{{ $c->status }}')">
                            <i class="fas fa-eye text-primary"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="openEdit({{ $c->id }},'{{ $c->name }}','{{ $c->status }}')">
                            <i class="fas fa-edit text-warning"></i>
                        </button>
                        <button class="btn btn-sm btn-light" onclick="confirmDelete({{ $c->id }})">
                            <i class="fas fa-trash text-danger"></i>
                        </button>

                        <form id="del-{{ $c->id }}" method="POST"
                              action="{{ route('categories.destroy',$c->id) }}" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="p-2">
        {{ $categories->appends(request()->query())->links() }}
    </div>
</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="categoryForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="{{ old('_method', 'POST') }}">
                <input type="hidden" name="category_id" value="{{ old('category_id', '') }}">

                <div class="modal-header">
                    <h6 id="modalTitle">{{ old('_method')=='PUT' ? 'Edit Category' : 'Add Category' }}</h6>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- FORM MODE -->
                    <div id="formMode">
                        <div class="form-outline mb-4">
                            <input type="text"
                                   id="catName"
                                   name="addName"
                                   class="form-control @error('addName') is-invalid @enderror"
                                   value="{{ old('addName', '') }}"
                                   required autofocus>
                            <label class="form-label">Category Name</label>
                            <small class="invalid-feedback" id="nameError">
                                @error('addName') {{ $message }} @enderror
                            </small>
                        </div>

                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="catStatus"
                                   name="addStatus"
                                   value="Active"
                                   {{ old('addStatus', 'Active') == 'Active' ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <!-- VIEW MODE -->
                    <div id="viewMode" class="d-none">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th width="40%">Name</th>
                                <td id="viewName"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span id="viewStatus" class="badge"></span>
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-mdb-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="saveBtn">
                        <span class="btn-text">Save</span>
                        <span class="spinner-border spinner-border-sm d-none ms-2"></span>
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
document.addEventListener('DOMContentLoaded', () => {

    const table = document.getElementById('categoryTable');
    const searchInput = document.getElementById('masterSearchInput');
    let currentSort = { index: null, asc: true };

    // ---------------- MASTER SEARCH ----------------
    function filterTable(){
        const filter = searchInput?.value.toLowerCase().trim() || '';
        table.querySelectorAll('tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    }
    searchInput?.addEventListener('keyup', filterTable);

    // ---------------- SORT TABLE ----------------
    function sortTable(index){
        const tbody = table.tBodies[0];
        const rows = Array.from(tbody.rows);

        if(currentSort.index === index) currentSort.asc = !currentSort.asc;
        else { currentSort.index = index; currentSort.asc = true; }

        rows.sort((a,b)=>{
            let aText = a.cells[index].innerText.toLowerCase();
            let bText = b.cells[index].innerText.toLowerCase();
            if(!isNaN(aText) && !isNaN(bText)){ aText = parseFloat(aText); bText = parseFloat(bText); }
            return (aText < bText ? -1 : aText > bText ? 1 : 0) * (currentSort.asc ? 1 : -1);
        });

        rows.forEach(r => tbody.appendChild(r));
        filterTable();
    }
    window.sortTable = sortTable;

    // ---------------- MODAL ----------------
    const modalEl = document.getElementById('categoryModal');
    const modal   = new mdb.Modal(modalEl);
    const form    = document.getElementById('categoryForm');
    const nameInput = document.getElementById('catName');
    const statusInput = document.getElementById('catStatus');
    const methodInput = document.getElementById('formMethod');
    const saveBtn = document.getElementById('saveBtn');
    const nameError = document.getElementById('nameError');

    modalEl.addEventListener('shown.mdb.modal', () => {
        nameInput.focus();
        nameInput.select();
    });

    function resetSaveBtn(){
        saveBtn.disabled = false;
        saveBtn.querySelector('.btn-text').innerText = 'Save';
        saveBtn.querySelector('.spinner-border').classList.add('d-none');
        // Reset validation errors
        nameInput.classList.remove('is-invalid');
        nameError.innerText = '';
    }

    // ---------------- ADD ----------------
    window.openAdd = function(){
        document.getElementById('modalTitle').innerText = 'Add Category';
        form.action = "{{ route('categories.store') }}";
        methodInput.value = 'POST';
        form.category_id.value = '';
        nameInput.value = '';
        statusInput.checked = true;
        document.getElementById('formMode').classList.remove('d-none');
        document.getElementById('viewMode').classList.add('d-none');
        saveBtn.style.display = 'inline-block';
        resetSaveBtn();
        modal.show();
    }

    // ---------------- EDIT ----------------
    window.openEdit = function(id,name,status){
        document.getElementById('modalTitle').innerText = 'Edit Category';
        form.action = `/categories/update/${id}`;
        methodInput.value = 'PUT';
        form.category_id.value = id;
        nameInput.value = name;
        statusInput.checked = (status === 'Active');
        document.getElementById('formMode').classList.remove('d-none');
        document.getElementById('viewMode').classList.add('d-none');
        saveBtn.style.display = 'inline-block';
        resetSaveBtn();
        modal.show();
    }

    // ---------------- VIEW ----------------
    window.openView = function(name,status){
        document.getElementById('modalTitle').innerText = 'View Category';
        document.getElementById('formMode').classList.add('d-none');
        document.getElementById('viewMode').classList.remove('d-none');
        document.getElementById('viewName').innerText = name;
        document.getElementById('viewStatus').innerText = status;
        document.getElementById('viewStatus').className =
            'badge ' + (status === 'Active' ? 'bg-success' : 'bg-secondary');
        saveBtn.style.display = 'none';
        modal.show();
    }

    // ---------------- DELETE ----------------
    window.confirmDelete = function(id){
        Swal.fire({
            title: 'Are you sure?',
            text: 'This cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete'
        }).then(res=>{
            if(res.isConfirmed){
                Swal.fire({
                    title: 'Deleting...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: ()=> Swal.showLoading()
                });
                document.getElementById('del-'+id).submit();
            }
        });
    }

    // ---------------- SUBMIT ----------------
    form.addEventListener('submit', e=>{
        e.preventDefault();
        nameInput.value = nameInput.value.trim();
        saveBtn.disabled = true;
        saveBtn.querySelector('.btn-text').innerText = 'Saving...';
        saveBtn.querySelector('.spinner-border').classList.remove('d-none');

        Swal.fire({
            title: 'Saving...',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: ()=> { Swal.showLoading(); form.submit(); }
        });
    });

    // ---------------- SUCCESS MESSAGE ----------------
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 1800,
        timerProgressBar: true
    });
    @endif

    // ---------------- AUTO OPEN MODAL ON VALIDATION ERROR ----------------
    @if($errors->any())
        modal.show();
    @endif

});
</script>
@endpush
