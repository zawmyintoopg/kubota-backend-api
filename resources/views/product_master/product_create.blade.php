<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Product | MDB5</title>

<!-- MDB5 -->
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.2.0/mdb.min.css" rel="stylesheet">

<style>
body { background:#f5f6f8; }
.header {
  position: sticky;
  top: 0;
  z-index: 1000;
  background:#0d6efd;
  color:#fff;
  padding:12px 16px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  border-radius:8px;
}
.variant-card {
  border:1px solid #ddd;
  border-radius:10px;
  padding:15px;
  margin-bottom:15px;
}
.inventory-fields { display:none; }
.color-box {
  width:36px;
  height:36px;
  border-radius:50%;
  display:inline-block;
  margin-right:6px;
  border:1px solid #ccc;
}
</style>
</head>

<body>
<div class="container py-3">

<!-- HEADER -->
<div class="header mb-3">
  <button class="btn btn-link text-white" onclick="history.back()">
    <i class="fas fa-arrow-left"></i>
  </button>
  <strong>Create Product</strong>
  <button class="btn btn-light btn-sm">
    <i class="fas fa-save"></i> Save
  </button>
</div>

<!-- BASIC INFO -->
<div class="card mb-3">
<div class="card-body">
<h6 class="mb-3">Basic Info</h6>

<input class="form-control mb-3" placeholder="Product name">

<label class="form-label">Category</label>
<div class="d-flex gap-2 mb-3">
  <select id="category" class="form-select"></select>
  <button type="button" class="btn btn-outline-primary btn-sm"
    data-mdb-toggle="modal" data-mdb-target="#addCategoryModal">
    <i class="fas fa-plus"></i>
  </button>
</div>

<label class="form-label">Brand</label>
<div class="d-flex gap-2">
  <select id="brand" class="form-select"></select>
  <button type="button" class="btn btn-outline-primary btn-sm"
    data-mdb-toggle="modal" data-mdb-target="#addBrandModal">
    <i class="fas fa-plus"></i>
  </button>
</div>
</div>
</div>

<!-- VARIANTS -->
<div class="card mb-3">
<div class="card-body">
<h6 class="mb-3">Variants</h6>
<div id="variantBox"></div>

<button class="btn btn-outline-success w-100" onclick="addVariant()">
  <i class="fas fa-plus"></i> Add Variant
</button>
</div>
</div>

<!-- POS -->
<div class="card mb-3">
<div class="card-body">
<h6 class="mb-3">POS Representation</h6>

<div class="form-check">
  <input class="form-check-input" type="radio" name="posType" checked>
  <label class="form-check-label">Upload Image</label>
</div>
<input type="file" class="form-control mb-3">

<div class="form-check">
  <input class="form-check-input" type="radio" name="posType">
  <label class="form-check-label">Color Placeholder</label>
</div>

<div class="d-flex align-items-center mt-2">
  <div class="color-box" style="background:#ff5733"></div>
  <div class="color-box" style="background:#33c3ff"></div>
  <div class="color-box" style="background:#28a745"></div>
</div>

<select class="form-select mt-2">
  <option>Circle</option>
  <option>Rectangle</option>
</select>
</div>
</div>

</div>

<!-- MODALS -->
<div class="modal fade" id="addCategoryModal">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header">
<h6>Add Category</h6>
<button class="btn-close" data-mdb-dismiss="modal"></button>
</div>
<div class="modal-body">
<input id="newCategory" class="form-control" placeholder="Category name">
</div>
<div class="modal-footer">
<button class="btn btn-primary"
 onclick="addOption('category','newCategory','addCategoryModal')">
Save</button>
</div>
</div>
</div>
</div>

<div class="modal fade" id="addBrandModal">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header">
<h6>Add Brand</h6>
<button class="btn-close" data-mdb-dismiss="modal"></button>
</div>
<div class="modal-body">
<input id="newBrand" class="form-control" placeholder="Brand name">
</div>
<div class="modal-footer">
<button class="btn btn-primary"
 onclick="addOption('brand','newBrand','addBrandModal')">
Save</button>
</div>
</div>
</div>
</div>

<div class="modal fade" id="addUnitModal">
<div class="modal-dialog modal-sm">
<div class="modal-content">
<div class="modal-header">
<h6>Add Unit</h6>
<button class="btn-close" data-mdb-dismiss="modal"></button>
</div>
<div class="modal-body">
<input id="newUnit" class="form-control" placeholder="Unit name">
</div>
<div class="modal-footer">
<button class="btn btn-primary" onclick="addUnit()">Save</button>
</div>
</div>
</div>
</div>

<!-- MDB JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.2.0/mdb.min.js"></script>

<script>
let skuCounter = 1;
let variantCount = 0;

function generateSKU() {
  return String(skuCounter++).padStart(8,"0");
}

function addVariant() {
  variantCount++;
  const div = document.createElement("div");
  div.className = "variant-card";

  div.innerHTML = `
  <strong>Variant ${variantCount}</strong>

  <input class="form-control my-2" placeholder="Variant name">

  <input class="form-control mb-2" value="${generateSKU()}" placeholder="SKU">

  <input type="number" class="form-control mb-2" placeholder="Selling price">

  <label class="form-label">Unit</label>
  <div class="d-flex gap-2 mb-2">
    <select class="form-select unit-select">
      <option>pcs</option>
      <option>set</option>
    </select>
    <button type="button" class="btn btn-outline-primary btn-sm"
      data-mdb-toggle="modal" data-mdb-target="#addUnitModal">
      <i class="fas fa-plus"></i>
    </button>
  </div>

  <div class="form-check form-switch mb-2">
    <input class="form-check-input" type="checkbox"
      onchange="this.closest('.variant-card')
      .querySelector('.inventory-fields').style.display =
      this.checked ? 'block':'none'">
    <label class="form-check-label">Track Inventory</label>
  </div>

  <div class="inventory-fields">
    <input type="number" class="form-control mb-2" placeholder="Stock qty">
    <input type="number" class="form-control" placeholder="Cost price">
  </div>
  `;
  document.getElementById("variantBox").appendChild(div);
}

function addOption(selectId,inputId,modalId){
  const v=document.getElementById(inputId).value.trim();
  if(!v) return;
  document.getElementById(selectId)
    .add(new Option(v,v,true,true));
  document.getElementById(inputId).value="";
  mdb.Modal.getOrCreateInstance(
    document.getElementById(modalId)).hide();
}

function addUnit(){
  const v=document.getElementById("newUnit").value.trim();
  if(!v) return;
  document.querySelectorAll(".unit-select")
    .forEach(s=>s.add(new Option(v,v,true,true)));
  document.getElementById("newUnit").value="";
  mdb.Modal.getOrCreateInstance(
    document.getElementById("addUnitModal")).hide();
}

document.addEventListener("DOMContentLoaded",()=>addVariant());
</script>
</body>
</html>
