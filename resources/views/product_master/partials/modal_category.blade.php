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