<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Vault</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .top-right {
            position: absolute;
            right: 1.5rem;
            top: 1.5rem;
        }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

    <div class="container py-5 text-center">
        {{-- Login / Register / Dashboard --}}
        @if (Route::has('login'))
            <div class="top-right text-end">
                @auth
                    <a href="{{ url('/home') }}" class="btn btn-outline-primary btn-sm me-2">
                        <i class="fa fa-user"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm me-2">
                        <i class="fa fa-sign-in-alt"></i> Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-success btn-sm">
                            <i class="fa fa-user-plus"></i> Register
                        </a>
                    @endif
                @endauth
            </div>
        @endif

        {{-- Title --}}
        <h1 class="display-4 fw-bold text-primary">📚 Book Vault</h1>
        <p class="lead text-muted mb-5">Easily manage your book inventory, suppliers, and storage — all in one centralized system.</p>

        {{-- Feature Cards --}}
        <div class="row justify-content-center g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-book fa-3x text-info mb-3"></i>
                        <h5 class="card-title">Book Inventory</h5>
                        <p class="card-text">Track all your books, including titles, authors, quantities, and more. Keep your records organized and updated.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-truck fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Suppliers</h5>
                        <p class="card-text">Maintain supplier profiles with contact details and supply history to streamline your procurement process.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fa fa-chart-line fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Reports & Insights</h5>
                        <p class="card-text">Generate detailed reports to monitor book movement, stock levels, and performance metrics over time.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-5 text-muted">
            <p>Crafted with ❤️ for seamless book management.</p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
