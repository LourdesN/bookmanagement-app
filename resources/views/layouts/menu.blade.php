<!-- need to remove -->
<li class="nav-item">
    <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
      <i class="fas fa-chart-line text-info"></i>
        <p>Home</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('books.index') }}" class="nav-link {{ Request::is('books*') ? 'active' : '' }}">
        <i class="fas fa-book text-info"></i>
        <p>Books</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('suppliers.index') }}" class="nav-link {{ Request::is('suppliers*') ? 'active' : '' }}">
        <i class="fas fa-people-carry text-info"></i>
        <p>Suppliers</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('deliveries.index') }}" class="nav-link {{ Request::is('deliveries*') ? 'active' : '' }}">
        <i class="fas fa-truck-loading text-info"></i>
        <p>Deliveries</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('inventories.index') }}" class="nav-link {{ Request::is('inventories*') ? 'active' : '' }}">
        <i class="fas fa-boxes text-info"></i>
        <p>Inventories</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('customers.index') }}" class="nav-link {{ Request::is('customers*') ? 'active' : '' }}">
        <i class="fas fa-users text-info"></i>
        <p>Customers</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('sales.index') }}" class="nav-link {{ Request::is('sales*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave text-info"></i>
        <p>Sales</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('payments.index') }}" class="nav-link {{ Request::is('payments*') ? 'active' : '' }}">
        <i class="fas fa-credit-card text-info"></i>
        <p>Payments</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('users.index') }}" class="nav-link {{ Request::is('users*') ? 'active' : '' }}">
        <i class="fas fa-user-circle text-info"></i>
        <p>Users</p>
    </a>
</li>
