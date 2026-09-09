<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product | POS</title>

<!-- MDB5 & Icons -->
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.2.0/mdb.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body { font-family: Roboto, sans-serif; background:#f8f9fa; }
.header { position:sticky; top:0; z-index:1000; background:#0d6efd; color:#fff;
  padding:12px 16px; display:flex; justify-content:space-between; align-items:center; flex-wrap: wrap; border-radius:8px; margin-bottom:20px;}
.header h5 { margin:0; font-weight:500; }
.pos-preview { width:100%; max-width:180px; height:120px; border-radius:12px; display:flex; align-items:center; justify-content:center;
    font-weight:500; color:#fff; background:#adb5bd; margin-top:10px; }
.pos-preview img { max-width:100%; max-height:100%; border-radius:10px; }

.delete-btn {
  background:#dc3545;
  color:#fff;
  border:none;
  padding:10px 16px;
  border-radius:6px;
  width:100%;
  font-weight:500;
  cursor:pointer;
}
.delete-btn:hover { background:#c82333; }

@media (max-width:768px){
  .header { flex-direction: column; align-items: flex-start; gap:10px;}
}
</style>
</head>
<body>

<div class="container-fluid p-2">

<!-- HEADER -->
<div class="header w-100">
  <a href="{{ route('items.list') }}" class="btn btn-light btn-sm">
    <i class="fas fa-arrow-left"></i> Back
  </a>
  <h5>Edit Product</h5>
  <button type="button" id="updateBtn" class="btn btn-warning btn-sm">
    <span id="btnText"><i class="fas fa-save"></i> Update</span>
    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none"></span>
  </button>
</div>

<form id="editForm" method="POST" action="{{ route('items.update',$product->id) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="card shadow-sm p-3">
<div class="row g-3">

<!-- ITEM NAME -->
<div class="col-md-6">
  <label>Item Name *</label>
  <input type="text" name="item_name" class="form-control" value="{{ old('item_name',$product->item_name) }}" required>
</div>

<!-- CATEGORY -->
<div class="col-md-6">
  <label>Category *</label>
  <select name="category_id" class="form-select" required>
    @foreach($categories as $cat)
      <option value="{{ $cat->id }}" {{ $product->category_id==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
    @endforeach
  </select>
</div>

<!-- BRAND -->
<div class="col-md-6">
  <label>Brand</label>
  <select name="brand_id" class="form-select">
    <option value="">Select Brand</option>
    @foreach($brands as $brand)
      <option value="{{ $brand->id }}" {{ $product->brand_id==$brand->id?'selected':'' }}>{{ $brand->brand_name }}</option>
    @endforeach
  </select>
</div>

<!-- COST -->
<div class="col-md-6">
  <label>Cost *</label>
  <input type="number" name="cost" class="form-control" value="{{ $product->cost }}" min="0" required>
</div>

<!-- UNIT -->
<div class="col-md-6">
  <label>Unit *</label>
  <select name="unit_id" class="form-select" required>
    @foreach($units as $unit)
      <option value="{{ $unit->id }}" {{ $product->unit_id==$unit->id?'selected':'' }}>{{ $unit->unit_name }}</option>
    @endforeach
  </select>
</div>

<!-- UNIT QTY -->
<div class="col-md-6">
  <label>Unit Qty *</label>
  <input type="number" name="unit_qty" class="form-control" value="{{ $product->unit_qty }}" min="1" required>
</div>

<!-- ON HAND -->
<div class="col-md-6">
  <label>On Hand *</label>
  <input type="number" name="on_hand_qty" class="form-control" value="{{ $product->on_hand_qty }}" min="0" required>
</div>

<!-- PRICE -->
<div class="col-md-6">
  <label>Unit Price *</label>
  <input type="number" name="unit_price" class="form-control" value="{{ $product->unit_price }}" min="0" required>
</div>

<!-- POS TYPE -->
<div class="col-12">
  <label>POS Type *</label><br>
  <label class="me-3">
    <input type="radio" name="pos_type" value="image" {{ $product->pos_type=='image'?'checked':'' }}> Image
  </label>
  <label>
    <input type="radio" name="pos_type" value="color" {{ $product->pos_type=='color'?'checked':'' }}> Color
  </label>
</div>

<!-- IMAGE -->
<div class="col-12" id="imageBox" style="{{ $product->pos_type=='image'?'':'display:none' }}">
  <input type="file" name="image" class="form-control" accept="image/*">
  @if($product->image)
    <img src="{{ asset('storage/'.$product->image) }}" class="mt-2" width="120">
  @endif
</div>

<!-- COLOR -->
<div class="col-12" id="colorBox" style="{{ $product->pos_type=='color'?'':'display:none' }}">
  <input type="color" name="pos_color" class="form-control form-control-color" value="{{ $product->pos_color }}">
</div>

<!-- PREVIEW -->
<div class="col-12">
  <h6>POS Preview</h6>
  <div class="pos-preview" id="posPreview">ITEM</div>
</div>

</div>
</div>
</form>

<!-- DELETE BUTTON BELOW FORM -->
<div class="mt-3">
  <form method="POST" action="{{ route('items.deactivate', $product->id) }}" id="statusForm">
    @csrf
    <button type="button" class="delete-btn" onclick="confirmDelete()">
      <i class="fas fa-trash"></i> Delete Product
    </button>
  </form>
</div>

</div>

<script>
const form = document.getElementById('editForm');
const updateBtn = document.getElementById('updateBtn');
const spinner = document.getElementById('btnSpinner');
const btnText = document.getElementById('btnText');

const imageBox = document.getElementById('imageBox');
const colorBox = document.getElementById('colorBox');
const imageInput = document.querySelector('input[name="image"]');
const colorInput = document.querySelector('input[name="pos_color"]');
const posPreview = document.getElementById('posPreview');

function updatePreview() {
  const checked = document.querySelector('input[name="pos_type"]:checked');
  if (!checked) return;

  if (checked.value === 'color') {
    posPreview.style.background = colorInput.value;
    posPreview.innerHTML = 'ITEM';
  }

  if (checked.value === 'image' && imageInput.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      posPreview.innerHTML = `<img src="${e.target.result}">`;
      posPreview.style.background = '#fff';
    };
    reader.readAsDataURL(imageInput.files[0]);
  }
}

document.querySelectorAll('input[name="pos_type"]').forEach(radio => {
  radio.addEventListener('change', () => {
    imageBox.style.display = radio.value === 'image' ? 'block' : 'none';
    colorBox.style.display = radio.value === 'color' ? 'block' : 'none';
    updatePreview();
  });
});

imageInput?.addEventListener('change', updatePreview);
colorInput?.addEventListener('input', updatePreview);

updateBtn.addEventListener('click', () => {
  const checked = document.querySelector('input[name="pos_type"]:checked');

  if (!checked) {
    Swal.fire('Error', 'Please select POS type', 'error');
    return;
  }

  if (checked.value === 'image' && !imageInput.files.length && !'{{ $product->image }}') {
    Swal.fire('Error', 'Please select POS image', 'error');
    return;
  }

  btnText.classList.add('d-none');
  spinner.classList.remove('d-none');

  Swal.fire({
    title: 'Updating product...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  form.submit();
});

// CONFIRM DELETE / SOFT DELETE
function confirmDelete(){
  Swal.fire({
    title:'Are you sure?',
    text: "This will disable the product (soft delete).",
    icon:'warning',
    showCancelButton:true,
    confirmButtonText:'Yes, Disable'
  }).then(r=>{
    if(r.isConfirmed){
      document.getElementById('statusForm').submit();
    }
  });
}

// INIT PREVIEW
@if($product->pos_type=='color')
  posPreview.style.background = '{{ $product->pos_color }}';
@endif

@if($product->pos_type=='image' && $product->image)
  posPreview.innerHTML = `<img src="{{ asset('storage/'.$product->image) }}">`;
  posPreview.style.background = '#fff';
@endif
</script>

</body>
</html>
