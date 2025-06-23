@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-10">
            <div>
                <div class=" text-black text-center mb-6">
                    <h3 class="mb-0">Welcome to BookVault, {{ Auth::user()->name }}!</h3>
                </div>
            </div>
        </div>
    </div>
<!-- dashboard cards -->
<div class="row g-4 ml-2">
    <!-- Total Users -->
    <div class="col-md-3">
        <div class="card shadow-lg bg-primary text-white hover-card card-users">
        <div class="icon-container">
        <i class="fas fa-book"></i>
        </div>
            <div class="card-body text-center">
                <h2 class="display-4">{{ $totalbooks }}</h2>
                <h4 class="lead">Books</h4>
            </div>
            <div class="border-top">
                <a href="{{ route('books.index') }}" class="text-decoration-none text-white p-3 d-block text-center">
                    Manage Books <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

      <!-- Total Sales -->
      <div class="col-md-3">
        <div class="card shadow-lg bg-warning text-white hover-card card-users">
        <div class="icon-container">
        <i class="fas fa-money-bill-wave"></i>
        </div>
            <div class="card-body text-center">
                <h2 class="display-4">{{ $formattedTotal }}</h2>
                <h4 class="lead">Sales</h4>
            </div>
            <div class="border-top">
                <a href="{{ route('sales.index') }}" class="text-decoration-none text-white p-3 d-block text-center">
                    Manage Sales <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

     <!-- Total Inventories -->
     <div class="col-md-3">
        <div class="card shadow-lg bg-danger text-white hover-card card-users">
        <div class="icon-container">
        <i class="fas fa-boxes"></i>
        </div>
            <div class="card-body text-center">
                <h2 class="display-4">{{ $totalinventory }}</h2>
                <h4 class="lead">Inventory</h4>
            </div>
            <div class="border-top">
                <a href="{{ route('sales.index') }}" class="text-decoration-none text-white p-3 d-block text-center">
                    Manage Inventory <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

     <!-- Total Users -->
     <div class="col-md-3">
        <div class="card shadow-lg bg-info text-white hover-card card-users">
        <div class="icon-container">
        <i class="fas fa-users"></i>
        </div>
            <div class="card-body text-center">
                <h2 class="display-4">{{ $totalusers }}</h2>
                <h4 class="lead">Users</h4>
            </div>
            <div class="border-top">
                <a href="{{ route('sales.index') }}" class="text-decoration-none text-white p-3 d-block text-center">
                    Manage users <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!--end of card-->
</div>
</div>
<style>

.list-group-item {
    border: none;
    border-bottom: 1px solid #ddd;
    padding: 10px;
}

.card {
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease-in-out;
    position: relative; /* For the background image */
    overflow: hidden; /* Clip background images */
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.card-body {
    padding: 1rem;
    background-color: rgba(0, 0, 0, 0.05);
}

.display-4 {
    font-size: 4rem;
    font-weight: bold;
    letter-spacing: 2px;
    font-family:Tangerine;
}

.card .border-top a {
    background-color: rgba(0, 0, 0, 0.1);
    transition: background-color 0.3s ease;
}

.card .border-top a:hover {
    background-color: rgba(0, 0, 0, 0.2);
}
.icon-container {
    position: absolute;
    top: 2px;
    right: 2px;
    font-size: 5rem;
    color: rgba(0, 0, 0, 0.1); /* Blended effect */
    opacity: 10;
}

/* Card Content Foreground */
.card-body {
    z-index: 2; /* Ensure card content is above the background */
}

.lead {
    font-size: 1.25rem;
    font-weight: bold;
    font-family: Verdana;
}

.bg-primary {
    background: linear-gradient(135deg, #0061ff, #4e94ff); /* Gradient blue */
}

.bg-success {
    background: linear-gradient(135deg, #28a745, #6dbf78); /* Gradient green */
}

.bg-warning {
    background: linear-gradient(135deg, #ffca28, #ff8f00); /* Gradient yellow */
}

.bg-info {
    background: linear-gradient(135deg, #17a2b8, #3ab7bb); /* Gradient cyan */
}

.text-white {
    color: white !important;
}
h3 {
    font-family: georgia;
    font-size: 2rem;
   
}

</style>
@endsection
