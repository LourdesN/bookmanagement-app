@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h1 class="fw-bold text-primary">Book Details</h1>
                </div>
                <div class="col-sm-6 text-sm-end ">
                    <a class="btn btn-outline-secondary"
                       href="{{ route('books.index') }}">
                        ← Back
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card shadow border-0 rounded-3">
            <div class="card-body py-4 px-5">
                <div class="row">
                    @include('books.show_fields')
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
    background-color: #fff;
    border-radius: 0.75rem;
}

h1 {
    font-size: 1.5rem;
    margin-bottom: 0;
}
.btn{
    margin-left:405px;
}
</style>
@endsection
