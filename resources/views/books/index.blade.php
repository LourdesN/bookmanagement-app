@extends('layouts.app')

@section('content')
    <section class="content-header mb-4">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">📚 Book Management</h1>
                <a class="btn btn-success shadow-sm px-4 py-2" href="{{ route('books.create') }}">
                    ➕ Add New Book
                </a>
            </div>
        </div>
    </section>

    <div class="content px-3">

        @include('sweetalert::alert')

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📄 All Books</h5>
            </div>

            <div class="card-body p-4">
                @include('books.table')
            </div>
        </div>
    </div>
@endsection
