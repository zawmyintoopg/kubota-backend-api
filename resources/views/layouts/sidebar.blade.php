<div class="list-group list-group-flush">

 <a href="/"
    class="list-group-item list-group-item-action {{ request()->is('/')?'active':'' }}">
  <i class="fas fa-home me-2"></i> Dashboard
 </a>

 <a href="{{ route('categories.index') }}"
    class="list-group-item list-group-item-action {{ request()->is('categories*')?'active':'' }}">
  <i class="fas fa-tags me-2"></i> Categories
 </a>

 <a href="#"
    class="list-group-item list-group-item-action">
  <i class="fas fa-box me-2"></i> Products
 </a>

 <a href="#"
    class="list-group-item list-group-item-action text-danger">
  <i class="fas fa-sign-out-alt me-2"></i> Logout
 </a>

</div>
