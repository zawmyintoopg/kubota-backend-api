<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>POS Product List</title>

<!-- MDB5 CSS & Icons -->
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.2.0/mdb.min.css" rel="stylesheet">

<style>
body { font-family: Roboto, sans-serif; background:#f8f9fa; }
.container-fluid { padding:10px; }
.product-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    background:#fff;
    padding:10px 12px;
    border-radius:8px;
    margin-bottom:8px;
    box-shadow:0 1px 3px rgba(0,0,0,0.1);
    cursor:pointer;
    transition: transform 0.1s;
}
.product-row:hover { transform: translateY(-2px); }
.product-left {
    display:flex;
    align-items:center;
    gap:12px;
}
.product-left img, .product-left .color-block {
    width:50px;
    height:50px;
    border-radius:8px;
    object-fit:cover;
    flex-shrink:0;
}
.product-middle {
    display:flex;
    flex-direction:column;
}
.product-middle h6 { margin:0; font-size:0.95rem; font-weight:500; }
.product-middle small { color:#6c757d; font-size:0.85rem; }
.product-right {
    font-weight:600;
    color:#0d6efd;
    min-width:70px;
    text-align:right;
}
.autocomplete-suggestions {
  border:1px solid #ced4da; background:#fff; max-height:200px; overflow-y:auto; position:absolute; z-index:1000; width:100%;
}
.autocomplete-suggestion { padding:8px 12px; cursor:pointer; }
.autocomplete-suggestion:hover { background:#f1f1f1; }
@media(max-width:576px){
    .product-right { min-width:50px; font-size:0.9rem; }
    .product-left img, .product-left .color-block { width:40px; height:40px; }
}
</style>
</head>
<body>

<div class="container-fluid">
  <h5 class="mb-3">POS Product List</h5>

  <!-- SEARCH BOX -->
  <div class="mb-3 position-relative">
    <input type="text" id="searchBox" class="form-control" placeholder="Search product...">
    <div id="autocompleteList" class="autocomplete-suggestions d-none"></div>
  </div>

  <!-- PRODUCT GRID -->
  <div id="productGrid">
    @include('items.partials.product_rows', ['products' => $products])
  </div>

  <!-- PAGINATION -->
  <div class="mt-3" id="paginationLinks">
    {{ $products->links('pagination::bootstrap-5') }}
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.2.0/mdb.min.js"></script>

<script>
// ------------------------ AUTOCOMPLETE ------------------------
$(document).ready(function(){
    const $search = $('#searchBox');
    const $list = $('#autocompleteList');
    const $grid = $('#productGrid');
    const $pagination = $('#paginationLinks');

    function loadProducts(url = "{{ route('items.index') }}", query='') {
        $.get(url, { q: query }, function(data){
            $grid.html(data.rows);
            $pagination.html(data.pagination);
        });
    }

    $search.on('input', function(){
        const query = $(this).val();

        // Autocomplete suggestions
        if(query.length < 1) { $list.addClass('d-none'); } else {
            $.get("{{ route('items.search') }}", { q: query }, function(data){
                $list.html('');
                if(data.length){
                    data.forEach(item=>{
                        $list.append(`<div class="autocomplete-suggestion" data-id="${item.id}">${item.item_name}</div>`);
                    });
                    $list.removeClass('d-none');
                } else { $list.addClass('d-none'); }
            });
        }

        // Live filter grid
        loadProducts("{{ route('items.index') }}", query);
    });

    // Click suggestion
    $list.on('click', '.autocomplete-suggestion', function(){
        const id = $(this).data('id');
        window.location.href = `/items/${id}/edit`;
    });

    // Click outside
    $(document).on('click', function(e){
        if(!$(e.target).closest('#searchBox, #autocompleteList').length){
            $list.addClass('d-none');
        }
    });

    // Pagination links click
    $(document).on('click', '#paginationLinks a', function(e){
        e.preventDefault();
        const url = $(this).attr('href');
        const query = $search.val();
        loadProducts(url, query);
    });
});
</script>

</body>
</html>
