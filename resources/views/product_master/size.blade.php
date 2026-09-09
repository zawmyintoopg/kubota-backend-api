@extends('layouts.master')
@section('title','Size')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0">
        <i class="fas fa-tags text-primary me-2"></i> Size
    </h5>

    <button class="btn btn-outline-primary btn-sm" onclick="openAdd()">
        <i class="fas fa-plus"></i> Create Size
    </button>
</div>

<!-- ================= TABLE ================= -->
<div class="card shadow-2">
    <div class="table-responsive">
        <table class="table align-middle mb-0" id="sizeTable">
            <thead class="bg-primary text-white">
                <tr>
                    <th width="80">#</th>
                    <th>Size Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>
            @foreach($sizes as $s)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $s->size_name }}</td>
                    <td>{{ $s->description }}</td>
                    <td>
                        <span class="badge {{ $s->status=='Active'?'bg-success':'bg-secondary' }}">
                            {{ $s->status }}
                        </span>
                    </td>
                    <td class="text-center">

                        <button class="btn btn-sm btn-light"
                            onclick="openView(
                                '{{ $s->size_name }}',
                                '{{ $s->description }}',
                                '{{ $s->status }}'
                            )">
                            <i class="fas fa-eye text-primary"></i>
                        </button>

                        <button class="btn btn-sm btn-light"
                            onclick="openEdit(
                                {{ $s->id }},
                                '{{ $s->size_name }}',
                                '{{ $s->description }}',
                                '{{ $s->status }}'
                            )">
                            <i class="fas fa-edit text-warning"></i>
                        </button>

                        <button class="btn btn-sm btn-light"
                            onclick="confirmDelete({{ $s->id }})">
                            <i class="fas fa-trash text-danger"></i>
                        </button>

                        <form id="del-{{ $s->id }}"
                              method="POST"
                              action="{{ route('size.destroy',$s->id) }}"
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
        {{ $sizes->links() }}
    </div>
</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="sizeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="sizeForm" method="POST">
                @csrf
                <input type="hidden" id="formMethod" name="_method">

                <div class="modal-header">
                    <h6 id="modalTitle">Add Size</h6>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- FORM MODE -->
                    <div id="formMode">
                        <div class="form-outline mb-3">
                            <input type="text" id="sizeName" name="addsizeName" class="form-control" required>
                            <label class="form-label">Size Name</label>
                        </div>

                        <div class="form-outline mb-3">
                            <textarea id="Description" name="addDescription" class="form-control"></textarea>
                            <label class="form-label">Description</label>
                        </div>

                        <input type="hidden" name="AddStatus" value="Inactive">

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox"
                                   id="Status" name="AddStatus"
                                   value="Active" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>

                    <!-- VIEW MODE -->
                    <div id="viewMode" class="d-none">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <th>Size Name</th>
                                <td id="viewSizeName"></td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td id="viewDescription"></td>
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
const modal = new mdb.Modal(document.getElementById('sizeModal'));
const form  = document.getElementById('sizeForm');

/* ================= MASTER SEARCH (FIXED) ================= */
document.addEventListener('input', function (e) {

    // only react to master search input
    if (
        e.target.tagName === 'INPUT' &&
        (e.target.type === 'search' || e.target.placeholder?.toLowerCase().includes('search'))
    ) {

        if (!document.getElementById('sizeTable')) return;

        const keyword = e.target.value.toLowerCase().trim();
        document.querySelectorAll('#sizeTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(keyword)
                ? ''
                : 'none';
        });
    }
});

/* ================= ADD ================= */
function openAdd(){
    modal.show();
    modalTitle.innerText = 'Add Size';
    form.action = "{{ route('size.store') }}";
    formMethod.value = 'POST';

    form.reset();
    Status.checked = true;

    formMode.classList.remove('d-none');
    viewMode.classList.add('d-none');
    saveBtn.classList.remove('d-none');

    setTimeout(()=>sizeName.focus(),300);
}

/* ================= EDIT ================= */
function openEdit(id,name,desc,status){
    modal.show();
    modalTitle.innerText = 'Edit Size';
    form.action = `/sizes/${id}`;
    formMethod.value = 'PUT';

    sizeName.value = name;
    Description.value = desc;
    Status.checked = (status === 'Active');

    formMode.classList.remove('d-none');
    viewMode.classList.add('d-none');
    saveBtn.classList.remove('d-none');
}

/* ================= VIEW ================= */
function openView(name,desc,status){
    modal.show();

    formMode.classList.add('d-none');
    viewMode.classList.remove('d-none');
    saveBtn.classList.add('d-none');

    viewSizeName.innerText = name;
    viewDescription.innerText = desc;
    viewStatus.innerText = status;
    viewStatus.className = 'badge ' + (status==='Active'?'bg-success':'bg-secondary');
}

/* ================= DELETE ================= */
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
            setTimeout(()=>{
                document.getElementById('del-'+id).submit();
            },300);
        }
    });
}

/* ================= SAVE LOADER ================= */
form.addEventListener('submit',()=>{
    Swal.fire({
        title:'Saving...',
        allowOutsideClick:false,
        didOpen:()=>Swal.showLoading()
    });
});

/* ================= RESET ================= */
document.getElementById('sizeModal')
.addEventListener('hidden.mdb.modal',()=>{
    form.reset();
    formMethod.value='POST';
});
</script>
@endpush
