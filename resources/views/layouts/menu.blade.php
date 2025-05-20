<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Home</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('books.index') }}" class="nav-link {{ Request::is('books*') ? 'active' : '' }}">
     <i class="fas fa-book"></i>
        <p>Books</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('customers.index') }}" class="nav-link {{ Request::is('customers*') ? 'active' : '' }}">
     <i class="fas fa-users"></i>
        <p>Customers</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('deliveries.index') }}" class="nav-link {{ Request::is('deliveries*') ? 'active' : '' }}">
     <i class="fas fa-truck-loading"></i>
        <p>Deliveries</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('inventories.index') }}" class="nav-link {{ Request::is('inventories*') ? 'active' : '' }}">
    <i class="fas fa-boxes"></i>
        <p>Inventories</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('sales.index') }}" class="nav-link {{ Request::is('sales*') ? 'active' : '' }}">
    <i class="fas fa-money-bill-wave"></i>
        <p>Sales</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('suppliers.index') }}" class="nav-link {{ Request::is('suppliers*') ? 'active' : '' }}">
    <i class="fas fa-people-carry"></i>
        <p>Suppliers</p>
    </a>
</li>
