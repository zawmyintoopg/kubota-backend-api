<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Variant</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
.variant-header{
    background: linear-gradient(135deg,#3b71ca,#54b4d3);
    color: #fff;
    border-radius: .75rem .75rem 0 0;
}
.is-invalid{
    border-color: red !important;
}
</style>
</head>
<body class="bg-light">
<div class="container-fluid">

<!-- HEADER -->
<div class="card shadow-3 mb-3">
    <div class="card-body variant-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0"><i class="fas fa-layer-group me-2"></i> Variants</h5>
            <small>{{ $product->name }}</small>
        </div>
        <button type="button" class="btn btn-light btn-sm" onclick="openAdd()">
            <i class="fas fa-plus"></i> Add Variant
        </button>
    </div>
</div>

<!-- TABLE -->
<div class="card shadow-2">
<div class="table-responsive">
<table class="table table-sm table-bordered align-middle mb-0" id="variantTable">
<thead class="bg-primary text-white">
<tr>
    <th>#</th>
    <th>Variant Name</th>
    <th>Unit</th>
    <th class="text-end">Sell Price</th>
    <th class="text-end">Last Purchase Price</th>
    <th class="text-end">Last Purchase Date</th>
    <th>Status</th>
    <th class="text-center">Action</th>
</tr>
</thead>
<tbody>
@forelse($variants as $v)
<tr>
<td>{{ $loop->iteration }}</td>
<td>{{ $v->variant_name }}</td>
<td>{{ $v->unit_name }} (x {{ $v->to_base }})</td>
<td class="text-end">MMK {{ number_format($v->sell_price) }}</td>
<td class="text-end">MMK {{ number_format($v->last_purchase_price) }}</td>
<td class="text-end">{{ $v->last_purchase_date }}</td>
<td>
    <span class="badge {{ $v->status ? 'bg-success' : 'bg-danger' }}">
        {{ $v->status ? 'active' : 'Disable' }}
    </span>
</td>
<td class="text-center">
    <button type="button" class="btn btn-sm btn-light"
        onclick="openEdit(
            {{ $v->id }},
            {{ $v->unit_id }},
            {{ $v->sell_price }},
            {{ $v->status }},
            {{ $v->variant_name }},
            {{ $v->last_purchase_price ?? 'null' }},
            {{ $v->last_purchase_date }}
        )">
        <i class="fas fa-edit text-warning"></i>
    </button>
    <form class="d-inline" method="POST" action="{{ route('product.variant.destroy',$v->id) }}">
        @csrf @method('DELETE')
        <button type="button" class="btn btn-sm btn-light" onclick="confirmDelete(this)">
            <i class="fas fa-trash text-danger"></i>
        </button>
    </form>
</td>
</tr>
@empty
<tr><td colspan="8" class="text-center text-muted">No variants</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>

<!-- ================= ADD / EDIT MODAL ================= -->
<div class="modal fade" id="variantModal" tabindex="-1">
<div class="modal-dialog modal-md modal-dialog-centered">
<div class="modal-content">

<form id="variantForm" method="POST">
@csrf
<input type="hidden" name="_method" id="formMethod">

<div class="modal-header">
<h6 class="mb-0">Variant</h6>
<button type="button" class="btn-close" data-mdb-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="mb-3">
<label>Variant Name</label>
<input type="text" name="variant_name" id="variant_name" class="form-control" autofocus>
</div>
<div class="mb-3">
<label>Unit</label>
<select name="unit_id" id="unit_id" class="form-select">
<option value="">-- Select Unit --</option>
@foreach($units as $u)
<option value="{{ $u->id }}">{{ $u->unit_name }} ( {{ $u->remarks }} )</option>
@endforeach
</select>
</div>
<div class="mb-3">
<label>Sell Price</label>
<input type="number" name="sell_price" id="sellprice" class="form-control">
</div>
<div class="mb-3">
<label>Last Purchase Price</label>
<input type="number" name="last_purchase_price" id="last_purchase_price" class="form-control">
</div>
<div class="mb-3">
<label>Last Purchase Date</label>
<input type="date" name="last_purchase_date" id="last_purchase_date" class="form-control" value="{{ date('Y-m-d') }}">
</div>
<input type="hidden" name="status" id="status" value="1">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
const variantModal = new mdb.Modal(document.getElementById('variantModal'));
const variantForm  = document.getElementById('variantForm');
const unit_id      = document.getElementById('unit_id');
const sellprice    = document.getElementById('sellprice');
const variant_name = document.getElementById('variant_name');
const last_purchase_price = document.getElementById('last_purchase_price');
const last_purchase_date = document.getElementById('last_purchase_date');
const formMethod   = document.getElementById('formMethod');
const status       = document.getElementById('status');
const saveBtn      = document.getElementById('saveBtn');

const usedUnits = @json($variants->pluck('unit_id')->toArray());
let isSubmitting = false;

// When unit selected, focus on sell price
unit_id.addEventListener('change', () => { if(unit_id.value) sellprice.focus(); });

// ================= ADD =================
function openAdd() {
    variantForm.reset();
    formMethod.value = 'POST';
    variantForm.action = "{{ route('product.variant.store',$product->id) }}";
    unit_id.disabled = false;
    isSubmitting = false;
    variantModal.show();

    // Autofocus
    variantModal._element.addEventListener('shown.mdb.modal', function handler() {
        if(variant_name) variant_name.focus();
        variantModal._element.removeEventListener('shown.mdb.modal', handler);
    });
}

// ================= EDIT =================
function openEdit(id, unit, sell, statusVal, variant, lastPrice, lastDate){
    alert('one');
    variantForm.reset();
    formMethod.value = 'PUT';
    variantForm.action = "{{ url('product/variant') }}/" + id;

    // Fill data
    variant_name.value = variant ?? '';
    unit_id.value = unit;
    unit_id.disabled = true;
    sellprice.value = sell ?? '';
    last_purchase_price.value = lastPrice ?? '';
    last_purchase_date.value = lastDate ?? '';
    status.value = statusVal;

    isSubmitting = false;
    variantModal.show();

    // Autofocus
    variantModal._element.addEventListener('shown.mdb.modal', function handler() {
        if(variant_name) variant_name.focus();
        variantModal._element.removeEventListener('shown.mdb.modal', handler);
    });
}

// ================= SUBMIT =================
variantForm.addEventListener('submit', e=>{
    e.preventDefault();
    if(isSubmitting) return;

    const unitVal = unit_id.value;
    const variantVal = variant_name.value.trim().toLowerCase();

    const existingVariants = @json($variants->map(function($v){
        return [
            'unit_id' => $v->unit_id,
            'variant_name' => strtolower($v->variant_name)
        ];
    }));

    let duplicate = false;
    existingVariants.forEach(v => {
        if(v.unit_id == unitVal && v.variant_name == variantVal){
            duplicate = true;
        }
    });

    if(duplicate){
        Swal.fire({icon:'error', title:'Duplicate!', text:'This variant name with selected unit already exists.'});
        variant_name.classList.add('is-invalid');
        variant_name.focus();
        return;
    } else {
        variant_name.classList.remove('is-invalid');
    }

    isSubmitting = true;
    saveBtn.disabled = true;

    Swal.fire({
        title:'Saving...',
        allowOutsideClick:false,
        showConfirmButton:false,
        didOpen:()=>Swal.showLoading()
    });

    setTimeout(()=>variantForm.submit(), 100);
});

// ================= RESET MODAL =================
document.getElementById('variantModal').addEventListener('hidden.mdb.modal', ()=>{
    isSubmitting=false;
    saveBtn.disabled=false;
    Swal.close();
});

// ================= DELETE =================
function confirmDelete(btn){
    Swal.fire({
        title:'Delete?',
        icon:'warning',
        showCancelButton:true
    }).then(r=>{
        if(r.isConfirmed){
            btn.closest('form').submit();
        }
    });
}

// ================= SUCCESS ALERT =================
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
</body>
</html>
