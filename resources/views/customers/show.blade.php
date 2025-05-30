@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
Customer Details
                    </h1>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-default float-right"
                       href="{{ route('customers.index') }}">
                                                    Back
                                            </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @include('customers.show_fields')
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
