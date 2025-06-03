@extends('layouts.app')

@section('content')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

    <section class="content-header bg-light py-3 mb-4 border-bottom">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0 text-primary">👥 Customer List</h2>
                </div>
                <div class="col-md-6 text-md-right mt-3 mt-md-0">
                    <a href="{{ route('customers.create') }}" class="btn btn-success">
                        ➕ Add New Customer
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('sweetalert::alert')

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📋 All Customers</h5>
            </div>

            <div class="card-body table-responsive">
                @include('customers.table')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- jQuery & DataTables --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    {{-- DataTables Buttons --}}
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    {{-- Initialize DataTable --}}
    <script>
        $(document).ready(function () {
            $('#customers-table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                responsive: true,
                pageLength: 10
            });
        });
    </script>
@endsection
