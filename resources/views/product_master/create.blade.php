<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Product | POS</title>

<!-- MDB5 CSS & Icons -->
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.2.0/mdb.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
body { font-family: Roboto, sans-serif; background:#f8f9fa; }
.header { position:sticky; top:0; z-index:1000; background:#0d6efd; color:#fff;
  padding:12px 16px; display:flex; justify-content:space-between; align-items:center; flex-wrap: wrap; border-radius:8px; margin-bottom:20px;}
.header h5 { margin:0; font-weight:500; }
.is-invalid { border-color:#dc3545 !important; }
.invalid-feedback { display:none; font-size:0.875rem; color:#dc3545; margin-top:3px; }
.color-circle { width:38px; height:38px; border-radius:50%; cursor:pointer; border:2px solid #ced4da; position:relative; }
.color-circle.selected { border-color:#0d6efd; }
.color-circle.selected::after { content:"\f00c"; font-family:"Font Awesome 6 Free"; font-weight:900; color:white;
    position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); }
.pos-preview { width:100%; max-width:180px; height:120px; border-radius:12px; display:flex; align-items:center; justify-content:center;
    font-weight:500; color:#fff; background:#adb5bd; margin-top:10px; }
.pos-preview img { max-width:100%; max-height:100%; border-radius:10px; }
@media (max-width:768px){
  .header { flex-direction: column; align-items: flex-start; gap:10px;}
  .pos-preview { max-width:100%; }
}
</style>
</head>
<body>
<div class="container-fluid p-2">

<!-- HEADER -->
<div class="header w-100">
  <h5>Create Product</h5>
  <button id="saveBtn" class="btn btn-light btn-sm" onclick="saveProduct()">
    <span id="saveBtnText"><i class="fas fa-save"></i> Save</span>
    <span id="saveBtnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
  </button>
</div>

<div class="card shadow-sm p-3">
<div class="row g-3">

  <!-- ITEM NAME -->
  <div class="col-12 col-md-6">
    <label for="itemName">Item Name *</label>
    <input type="text" id="itemName" class="form-control" placeholder="e.g. T-Shirt XL / Red" autofocus>
    <div class="invalid-feedback">Item name is required</div>
  </div>

  <!-- CATEGORY -->
  <div class="col-12 col-md-6">
    <label for="category">Category *</label>
    <div class="d-flex gap-2">
      <select id="category" class="form-select">
          <option value="">Select Category (e.g. Clothing)</option>
          @foreach($categories as $category)
              <option value="{{ $category->id }}">{{ $category->name }}</option>
          @endforeach
      </select>
      <button type="button" class="btn btn-outline-primary btn-sm" data-mdb-toggle="modal" data-mdb-target="#addCategoryModal">
        <i class="fas fa-plus"></i>
      </button>
    </div>
    <div class="invalid-feedback">Category is required</div>
  </div>

  <!-- BRAND -->
  <div class="col-12 col-md-6">
    <label for="brand">Brand</label>
    <div class="d-flex gap-2">
      <select id="brand" class="form-select">
          <option value="">Select Brand (e.g. Nike)</option>
          @foreach($brands as $brand)
              <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
          @endforeach
      </select>
      <button type="button" class="btn btn-outline-primary btn-sm" data-mdb-toggle="modal" data-mdb-target="#addBrandModal">
        <i class="fas fa-plus"></i>
      </button>
    </div>
  </div>

  <!-- COST -->
  <div class="col-12 col-md-6">
    <label for="cost">Cost *</label>
    <input type="number" id="cost" class="form-control" placeholder="e.g. 5000 MMK">
    <div class="invalid-feedback">Cost is required</div>
  </div>

  <!-- UNIT -->
  <div class="col-12 col-md-6">
    <label for="unitId">Unit *</label>
    <div class="d-flex gap-2">
      <select id="unitId" class="form-select unit-select">
          @foreach($units as $unit)
              <option value="{{ $unit->id }}">{{ $unit->unit_name }} x {{ $unit->to_base }} (e.g. Box x 12 pcs)</option>
          @endforeach
      </select>
      <button type="button" class="btn btn-outline-primary btn-sm" data-mdb-toggle="modal" data-mdb-target="#addUnitModal">
        <i class="fas fa-plus"></i>
      </button>
    </div>
    <div class="invalid-feedback">Unit is required</div>
  </div>

  <!-- UNIT QTY -->
  <div class="col-12 col-md-6">
    <label for="unitQty">Unit Qty *</label>
    <input type="number" id="unitQty" class="form-control" placeholder="e.g. 1 Box = 12 pcs" value="1" min="1">
    <div class="invalid-feedback">Unit quantity is required</div>
  </div>

  <!-- ON HAND STOCK -->
  <div class="col-12 col-md-6">
    <label for="onHandQty">On-hand Stock *</label>
    <input type="number" id="onHandQty" class="form-control" placeholder="e.g. 120 pcs" value="0" min="0">
    <div class="invalid-feedback">Stock is required</div>
  </div>

  <!-- UNIT PRICE -->
  <div class="col-12 col-md-6">
    <label for="unitPrice">Unit Price *</label>
    <input type="number" id="unitPrice" class="form-control" placeholder="e.g. 6000 MMK">
    <div class="invalid-feedback">Unit price is required</div>
  </div>

  <!-- POS Representation -->
  <div class="col-12 mt-2">
    <label>POS Representation *</label><br>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="posType" value="image" checked onchange="togglePos(this)">
      <label class="form-check-label">Image</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="posType" value="color" onchange="togglePos(this)">
      <label class="form-check-label">Color</label>
    </div>
  </div>

  <!-- IMAGE INPUT -->
  <div class="col-12 pos-image mt-2">
    <input type="file" class="form-control" accept="image/*" onchange="previewImage(this)">
  </div>

  <!-- COLOR PICKER -->
  <div class="col-12 pos-color mt-2" style="display:none;">
    <input type="hidden" id="posColorValue">
    <div class="d-flex gap-2 flex-wrap mb-2">
      <div class="color-circle bg-danger" data-color="#dc3545" onclick="selectColor(this)"></div>
      <div class="color-circle bg-primary" data-color="#0d6efd" onclick="selectColor(this)"></div>
      <div class="color-circle bg-success" data-color="#198754" onclick="selectColor(this)"></div>
      <div class="color-circle bg-warning" data-color="#ffc107" onclick="selectColor(this)"></div>
      <div class="color-circle bg-info" data-color="#0dcaf0" onclick="selectColor(this)"></div>
      <div class="color-circle bg-dark" data-color="#212529" onclick="selectColor(this)"></div>
    </div>
    <input type="color" class="form-control form-control-color" onchange="pickCustomColor(this)">
  </div>

  <!-- POS Preview -->
  <div class="col-12">
    <h6 class="mt-3">POS Preview</h6>
    <div class="pos-preview" id="posPreview">ITEM</div>
  </div>

</div>
</div>
</div>
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Add Category</h6>
        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="newCategory">Category Name *</label>
        <input type="text" id="newCategory" class="form-control" placeholder="Enter category name" autofocus>
        <div class="invalid-feedback" id="newCategoryError">Category name is required</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="addOption('category','newCategory','addCategoryModal')">
          Save
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Add Brand -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Add Brand</h6>
        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="newBrand">Brand Name *</label>
        <input type="text" id="newBrand" class="form-control" placeholder="Enter brand name" autofocus>
        <div class="invalid-feedback" id="newBrandError">Brand name is required</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="addOption('brand','newBrand','addBrandModal')">
          Save
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Add Unit -->
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Add Unit</h6>
        <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <label for="newUnit">Unit Name *</label>
        <input type="text" id="newUnit" class="form-control" placeholder="Enter unit name" autofocus>
        <div class="invalid-feedback" id="newUnitError">Unit name is required</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" onclick="addUnit()">
          Save
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.2.0/mdb.min.js"></script>
<script>
// ------------------------ POS JS ------------------------
let lastItemCode = 10000000;

function nextItemCode(){ lastItemCode++; return String(lastItemCode).padStart(8,'0'); }

function togglePos(el){ 
  document.querySelector('.pos-image').style.display = el.value==='image'?'block':'none';
  document.querySelector('.pos-color').style.display = el.value==='color'?'block':'none';
  resetPreview();
}

function previewImage(input){
  const preview=document.getElementById('posPreview');
  const reader=new FileReader();
  reader.onload=e=>{preview.innerHTML=`<img src="${e.target.result}">`; preview.style.background='#fff';};
  if(input.files[0]) reader.readAsDataURL(input.files[0]);
}

function selectColor(el){
  document.querySelectorAll('.color-circle').forEach(c=>c.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('posColorValue').value=el.dataset.color;
  updateColorPreview(el.dataset.color);
}

function pickCustomColor(input){
  document.querySelectorAll('.color-circle').forEach(c=>c.classList.remove('selected'));
  document.getElementById('posColorValue').value=input.value;
  updateColorPreview(input.value);
}

function updateColorPreview(color){
  const preview=document.getElementById('posPreview');
  preview.innerHTML='ITEM';
  preview.style.background=color;
}

function resetPreview(){
  const preview=document.getElementById('posPreview');
  preview.innerHTML='ITEM';
  preview.style.background='#adb5bd';
}

document.querySelectorAll('input, select').forEach(el=>{
  el.addEventListener('input',()=>{ el.classList.remove('is-invalid'); el.nextElementSibling.style.display='none'; });
});

// ------------------------ SAVE ------------------------
function saveProduct(){
    let valid = true;
    const requiredFields = ['itemName','category','unitId','unitQty','onHandQty','unitPrice','cost'];
    requiredFields.forEach(id=>{
        const el = document.getElementById(id);
        if(!el.value || (el.type==='number' && parseFloat(el.value) <=0)){
            el.classList.add('is-invalid');
            if(el.nextElementSibling) el.nextElementSibling.style.display='block';
            valid = false;
        }
    });
    if(!valid) return;

    // SweetAlert loading
    Swal.fire({
        title: 'Saving product...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = "{{ route('items.store') }}";
    form.enctype = 'multipart/form-data';
    form.appendChild(createInput('_token','{{ csrf_token() }}'));
    form.appendChild(createInput('item_name',document.getElementById('itemName').value));
    form.appendChild(createInput('category_id',document.getElementById('category').value));
    form.appendChild(createInput('brand_id',document.getElementById('brand').value));
    form.appendChild(createInput('unit_id',document.getElementById('unitId').value));
    form.appendChild(createInput('unit_qty',document.getElementById('unitQty').value));
    form.appendChild(createInput('on_hand_qty',document.getElementById('onHandQty').value));
    form.appendChild(createInput('unit_price',document.getElementById('unitPrice').value));
    form.appendChild(createInput('cost',document.getElementById('cost').value));
    form.appendChild(createInput('item_code',nextItemCode()));

    const posType = document.querySelector('input[name="posType"]:checked').value;
    form.appendChild(createInput('pos_type', posType));
    if(posType==='color') form.appendChild(createInput('pos_color', document.getElementById('posColorValue').value));
    if(posType==='image' && document.querySelector('.pos-image input').files[0]){
        const fileInput = document.querySelector('.pos-image input');
        const file = fileInput.files[0];
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;
        form.appendChild(fileInput);
    }

    document.body.appendChild(form);
    form.submit();
}

function createInput(name,value){
    const input = document.createElement('input');
    input.type='hidden';
    input.name=name;
    input.value=value;
    return input;
}

// ------------------------ SHORTCUT MODALS ------------------------
function addOption(selectId,inputId,modalId){
    const v = document.getElementById(inputId).value.trim();
    if(!v) return;
    const sel = document.getElementById(selectId);
    sel.add(new Option(v,v,true,true));
    document.getElementById(inputId).value="";
    mdb.Modal.getOrCreateInstance(document.getElementById(modalId)).hide();
}

function addUnit(){
    const v = document.getElementById("newUnit").value.trim();
    if(!v) return;
    document.querySelectorAll(".unit-select").forEach(s=>s.add(new Option(v,v,true,true)));
    document.getElementById("newUnit").value="";
    mdb.Modal.getOrCreateInstance(document.getElementById("addUnitModal")).hide();
}
</script>
</body>
</html>
