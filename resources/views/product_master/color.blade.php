@extends('layouts.master')
@section('title','Color')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">
        <i class="fas fa-tags text-primary me-2"></i> Color
    </h5>

    <button class="btn btn-outline-primary btn-sm" onclick="openAdd()">
        <i class="fas fa-plus"></i> Create Color
    </button>
</div>

<!-- ================= TABLE ================= -->
<div class="card shadow-2">
    <div class="table-responsive">
        <table class="table align-middle mb-0" id="colorTable">
            <thead class="bg-primary text-white">
                <tr>
                    <th style="width:80px">#</th>
                    <th>Color Name</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>
            @foreach($colors as $c)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $c->color_name }}</td>
                    <td>
                        <span class="badge {{ $c->status=='Active'?'bg-success':'bg-secondary' }}">
                            {{ $c->status }}
                        </span>
                    </td>

                    <td class="text-center">
                        <button class="btn btn-sm btn-light"
                            onclick="openView('{{ $c->color_name }}','{{ $c->status }}')">
                            <i class="fas fa-eye text-primary"></i>
                        </button>

                        <button class="btn btn-sm btn-light"
                            onclick="openEdit({{ $c->id }},'{{ $c->color_name }}','{{ $c->status }}')">
                            <i class="fas fa-edit text-warning"></i>
                        </button>

                        <button class="btn btn-sm btn-light"
                            onclick="confirmDelete({{ $c->id }})">
                            <i class="fas fa-trash text-danger"></i>
                        </button>

                        <form id="del-{{ $c->id }}"
                              method="POST"
                              action="{{ route('color.destroy',$c->id) }}"
                              class="d-none">
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
        {{ $colors->links() }}
    </div>
</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="colorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="colorForm" method="POST">
                @csrf
                <input type="hidden" id="formMethod" name="_method">

                <div class="modal-header">
                    <h6 id="modalTitle">Add Color</h6>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- FORM MODE -->
                    <div id="formMode">
                        <div class="form-outline mb-3">
                            <input type="text"
                                   name="addcolorName"
                                   id="colorName"
                                   class="form-control"
                                   value="{{ old('addcolorName') }}"
                                   required>
                            <label class="form-label">Color Name</label>
                        </div>

                        <input type="hidden" name="AddStatus" value="Inactive">

                        <div class="form-check form-switch">
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="Status"
                                   name="AddStatus"
                                   value="Active"
                                   {{ old('AddStatus','Active')==='Active' ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <!-- VIEW MODE -->
                    <div id="viewMode" class="d-none">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th>Color Name</th>
                                <td id="viewColorName"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><span id="viewStatus" class="badge"></span></td>
                            </tr>
                        </table>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-mdb-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="saveBtn">Save</button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const modal = new mdb.Modal(document.getElementById('colorModal'));
const form  = document.getElementById('colorForm');

/* ========= ADD ========= */
function openAdd(){
    modal.show();
    modalTitle.innerText = 'Add Color';
    form.action = "{{ route('color.store') }}";
    formMethod.value = 'POST';
    colorName.value = '';
    Status.checked = true;
    formMode.classList.remove('d-none');
    viewMode.classList.add('d-none');
    saveBtn.classList.remove('d-none');
}

/* ========= EDIT ========= */
function openEdit(id,name,status){
    modal.show();
    modalTitle.innerText = 'Edit Color';
    form.action = `/colors/${id}`;
    formMethod.value = 'PUT';
    colorName.value = name;
    Status.checked = (status === 'Active');
    formMode.classList.remove('d-none');
    viewMode.classList.add('d-none');
    saveBtn.classList.remove('d-none');
}

/* ========= VIEW ========= */
function openView(name,status){
    modal.show();
    formMode.classList.add('d-none');
    viewMode.classList.remove('d-none');
    saveBtn.classList.add('d-none');
    viewColorName.innerText = name;
    viewStatus.innerText = status;
    viewStatus.className = 'badge ' + (status==='Active'?'bg-success':'bg-secondary');
}

/* ========= DELETE ========= */
function confirmDelete(id){
    Swal.fire({
        title:'Delete?',
        text:'This cannot be undone',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33'
    }).then(r=>{
        if(r.isConfirmed){
            Swal.fire({ title:'Deleting...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
            setTimeout(()=>document.getElementById('del-'+id).submit(),300);
        }
    });
}

/* ========= SAVE SPINNER ========= */
form.addEventListener('submit',()=>{
    Swal.fire({ title:'Saving...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
});

/* ========= RESET ========= */
document.getElementById('colorModal')
    .addEventListener('hidden.mdb.modal',()=>{
        form.reset();
        formMethod.value='POST';
    });

/* ========= AUTO FOCUS ========= */
document.getElementById('colorModal')
    .addEventListener('shown.mdb.modal',()=>{
        colorName.focus();
        colorName.select();
    });

/* ========= MASTER SEARCH (AUTO FILTER) ========= */
document.addEventListener('DOMContentLoaded',()=>{
    const masterSearch = document.getElementById('globalSearch');
    if(!masterSearch) return;

    masterSearch.addEventListener('keyup',function(){
        const key = this.value.toLowerCase();
        document.querySelectorAll('#colorTable tbody tr').forEach(row=>{
            row.style.display = row.innerText.toLowerCase().includes(key) ? '' : 'none';
        });
    });
});
</script>

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded',()=>{
    Swal.fire({
        icon:'error',
        title:'Duplicate Color',
        html:`{!! implode('<br>', $errors->all()) !!}`
    }).then(()=>modal.show());
});
</script>
@endif
@endpush
