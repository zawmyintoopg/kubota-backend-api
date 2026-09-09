@extends('layouts.master')

@section('title','POS - Stock Movements')

@section('content')
<div class="container-fluid mt-3">
    <h3>📦 Stock Movements</h3>

    <div class="d-flex mb-2">
        <button class="btn btn-success me-2" onclick="openAddModal()">➕ Add Multiple</button>
        <button class="btn btn-primary me-2" onclick="bulkEdit()">✏️ Edit Selected</button>
        <button class="btn btn-danger" onclick="bulkDelete()">🗑 Delete Selected</button>
    </div>

    <table class="table table-bordered table-sm align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th><input type="checkbox" id="selectAll" onchange="toggleAll(this)"></th>
                <th>#</th>
                <th>Product</th>
                <th>Unit</th>
                <th>Type</th>
                <th>Qty</th>
                <th>Balance</th>
                <th>Reference</th>
                <th>Date</th>
                <th>Note</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $i => $m)
            <tr class="{{ $m->variant?->product && $m->balance_after < 5 ? 'low-stock' : '' }}">
                <td><input type="checkbox" class="selectMovement" value="{{ $m->id }}"></td>
                <td>{{ $i+1 }}</td>
                <td>{{ $m->variant?->product?->name ?? 'N/A' }}</td>
                <td>{{ $m->variant?->unit?->name ?? 'N/A' }}</td>
                <td>{{ $m->movement_type }}</td>
                <td>{{ $m->qty }}</td>
                <td>{{ $m->balance_after }}</td>
                <td>{{ $m->reference_id }}</td>
                <td>{{ $m->movement_date }}</td>
                <td>{{ $m->note }}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-primary" onclick="editMovement({{ $m->id }})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $m->id }})">
                        <i class="fas fa-trash"></i>
                    </button>
                    <form id="deleteForm{{ $m->id }}" method="POST" action="{{ route('stock-movements.destroy', $m->id) }}" style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- ADD / EDIT MODAL -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('stock-movements.store') }}" id="addForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add / Edit Stock Movements</h5>
                    <button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="reference_id" value="1">
                    <table class="table table-bordered table-sm">
                        <thead class="table-secondary">
                            <tr>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>Note</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="addBody"></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="addRow()">➕ Add Row</button>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-mdb-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const movements = @json($movements);
const variants = @json($variants);

// Open Add Modal
function openAddModal(){
    const addModal = new mdb.Modal(document.getElementById('addModal'));
    document.getElementById('addBody').innerHTML = '';
    addRow();
    addModal.show();
}

// Add new row inside modal
function addRow(data=null){
    const tbody = document.getElementById('addBody');
    const tr = document.createElement('tr');
    let variantOptions = variants.map(v => {
        const selected = data && data.variant_id==v.id ? 'selected' : '';
        return `<option value="${v.id}" ${selected}>${v.product_name} - ${v.unit_name}</option>`;
    }).join('');
    let movementType = data ? data.movement_type : '';
    tr.innerHTML = `
        <td>
            <select name="variant_id[]" class="form-select">${variantOptions}</select>
        </td>
        <td>
            <select name="movement_type[]" class="form-select">
                <option value="IN" ${movementType=='IN'?'selected':''}>IN</option>
                <option value="OUT" ${movementType=='OUT'?'selected':''}>OUT</option>
                <option value="ADJUST" ${movementType=='ADJUST'?'selected':''}>ADJUST</option>
            </select>
        </td>
        <td><input type="number" name="qty[]" class="form-control" value="${data ? data.qty : 0}" min="0"></td>
        <td><input type="text" name="note[]" class="form-control" value="${data ? data.note : ''}"></td>
        <td><i class="fas fa-trash text-danger pointer" onclick="this.closest('tr').remove()"></i></td>
    `;
    tbody.appendChild(tr);
}

// Edit single movement
function editMovement(id){
    const m = movements.find(x=>x.id===id);
    if(!m) return;
    const addModalEl = document.getElementById('addModal');
    const addModal = new mdb.Modal(addModalEl);
    document.getElementById('addBody').innerHTML = '';
    addRow(m);
    addModal.show();
}

// Confirm delete
function confirmDelete(id){
    Swal.fire({
        title:'Delete?',
        text:'You cannot undo this!',
        icon:'warning',
        showCancelButton:true,
        confirmButtonText:'Yes, delete!',
        cancelButtonText:'Cancel'
    }).then(result=>{
        if(result.isConfirmed){
            document.getElementById('deleteForm'+id).submit();
        }
    });
}

// Bulk actions placeholders
function bulkEdit(){
    const selectedIds = Array.from(document.querySelectorAll('.selectMovement:checked')).map(c=>c.value);
    if(selectedIds.length === 0){
        Swal.fire('No selection','Please select at least one movement','info');
        return;
    }

    const addModalEl = document.getElementById('addModal');
    const addModal = new mdb.Modal(addModalEl);
    const tbody = document.getElementById('addBody');
    tbody.innerHTML = '';

    selectedIds.forEach(id => {
        const m = movements.find(x=>x.id==id);
        if(m) addRow(m);
    });

    addModal.show();
}

function bulkDelete(){
    const selected = Array.from(document.querySelectorAll('.selectMovement:checked')).map(c=>c.value);
    if(selected.length === 0){
        Swal.fire('No selection','Please select at least one movement','info');
        return;
    }

    Swal.fire({
        title:'Delete selected movements?',
        text:'This cannot be undone!',
        icon:'warning',
        showCancelButton:true,
        confirmButtonText:'Yes, delete all!',
        cancelButtonText:'Cancel'
    }).then(result=>{
        if(result.isConfirmed){
            // create a form dynamically
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('stock-movements.bulkDelete') }}"; // new route for bulk delete
            form.style.display = 'none';
            // CSRF
            const csrf = document.createElement('input');
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            form.appendChild(csrf);
            // method DELETE
            const method = document.createElement('input');
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            // selected ids
            selected.forEach(id=>{
                const input = document.createElement('input');
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Select all checkboxes
function toggleAll(el){
    document.querySelectorAll('.selectMovement').forEach(c=>c.checked=el.checked);
}
</script>
@endpush
