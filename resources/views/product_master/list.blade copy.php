<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Max POS Product List</title>

<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.2.0/mdb.min.css" rel="stylesheet">

<style>
body { font-family: Roboto, sans-serif; background:#f8f9fa; }
.container-fluid { padding:10px; }

/* KBZ Pay Style Product Row */
.kbz-product-row {
    display:flex;
    align-items:center;
    justify-content:space-between;
    background:#fff;
    border-radius:10px;
    padding:12px 15px;
    margin-bottom:10px;
    box-shadow:0 1px 4px rgba(0,0,0,0.1);
    cursor:pointer;
    transition:0.2s;
}
.kbz-product-row:hover { background:#f1f3f6; transform: translateY(-1px); }

.kbz-left { flex-shrink:0; }
.kbz-img, .kbz-color {
    width:50px; height:50px; border-radius:8px;
    object-fit:cover;
    display:flex; align-items:center; justify-content:center;
    font-weight:500; color:#fff; text-align:center;
}

.kbz-middle { flex:1; margin-left:12px; display:flex; flex-direction:column; }
.kbz-name { font-weight:600; font-size:0.95rem; color:#212529; }
.kbz-brand { font-size:0.85rem; color:#6c757d; }
.kbz-stock { font-size:0.8rem; color:#6c757d; }

.kbz-right { font-weight:700; font-size:1rem; color:#0d6efd; text-align:right; min-width:90px; }

/* Search autocomplete */
.autocomplete-suggestions { border:1px solid #ced4da; background:#fff; max-height:200px; overflow-y:auto; position:absolute; z-index:1000; width:100%; }
.autocomplete-suggestion { padding:8px 12px; cursor:pointer; }
.autocomplete-suggestion:hover { background:#f1f1f1; }

/* Floating create button */
.fab {
    position:fixed;
    bottom:25px;
    right:25px;
    width:60px; height:60px;
    border-radius:50%;
    background:#0d6efd;
    color:#fff;
    display:flex; justify-content:center; align-items:center;
    font-size:28px;
    box-shadow:0 4px 8px rgba(0,0,0,0.3);
    cursor:pointer;
    z-index:1000;
}
.fab:hover { background:#0b5ed7; }

@media(max-width:576px){
    .kbz-img, .kbz-color { width:40px; height:40px; }
    .kbz-right { min-width:70px; font-size:0.95rem; }
}
</style>
</head>
<body>

<div class="container-fluid">
    <h5 class="mb-3">Max POS Product List</h5>

    <!-- SEARCH BOX -->
    <div class="mb-3 position-relative">
        <input type="text" id="searchBox" class="form-control" placeholder="Search product...">
        <div id="autocompleteList" class="autocomplete-suggestions d-none"></div>
    </div>

    <!-- PRODUCT GRID -->
    <div id="productGrid">
        @include('product_master.partials.product_rows', ['products' => $products])
    </div>

    <!-- PAGINATION -->
    <div class="mt-3" id="paginationLinks">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Floating create button -->
<a href="{{ route('items.create') }}" class="fab"><i class="fas fa-plus"></i></a>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script>
$(document).ready(function(){
    const $search = $('#searchBox');
    const $list = $('#autocompleteList');
    const $grid = $('#productGrid');
    const $pagination = $('#paginationLinks');
    let typingTimer;
    const typingDelay = 300; // ms

    // Load grid (AJAX)
    function loadProducts(url="{{ route('items.list') }}", query=''){
        $.get(url, { q: query }, function(data){
            $grid.html(data.rows);
            $pagination.html(data.pagination);
        });
    }

    // Autocomplete fast
    $search.on('input', function(){
        clearTimeout(typingTimer);
        const query = $(this).val().trim();

        typingTimer = setTimeout(()=>{
            // Autocomplete suggestions
            if(query.length < 1) { 
                $list.addClass('d-none'); 
                return;
            }

            $.get("{{ route('items.search') }}", { q: query }, function(data){
                $list.html('');
                if(data.length){
                    data.forEach(item=>{
                        $list.append(`<div class="autocomplete-suggestion" data-id="${item.id}">${item.item_name}</div>`);
                    });
                    $list.removeClass('d-none');
                } else { $list.addClass('d-none'); }
            });

        }, typingDelay);
    });

    // Click autocomplete suggestion
    $list.on('click', '.autocomplete-suggestion', function(){
        const id = $(this).data('id');
        window.location.href = `/items/${id}/edit`;
    });

    // Hide autocomplete on click outside
    $(document).on('click', function(e){
        if(!$(e.target).closest('#searchBox, #autocompleteList').length){
            $list.addClass('d-none');
        }
    });

    // AJAX pagination
    $(document).on('click', '#paginationLinks a', function(e){
        e.preventDefault();
        const url = $(this).attr('href');
        const query = $search.val().trim();
        loadProducts(url, query);
    });
});
</script>

</body>
</html>
