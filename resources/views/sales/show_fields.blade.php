<!-- Book Id Field -->
<div class="col-sm-12">
    {!! Form::label('book_id', 'Book Id:') !!}
    <p>{{ $sale->book_id }}</p>
</div>

<!-- Customer Id Field -->
<div class="col-sm-12">
    {!! Form::label('customer_id', 'Customer Id:') !!}
    <p>{{ $sale->customer_id }}</p>
</div>

<!-- Quantity Field -->
<div class="col-sm-12">
    {!! Form::label('quantity', 'Quantity:') !!}
    <p>{{ $sale->quantity }}</p>
</div>

<!-- Unit Price Field -->
<div class="col-sm-12">
    {!! Form::label('unit_price', 'Unit Price:') !!}
    <p>{{ $sale->unit_price }}</p>
</div>

<!-- Total Field -->
<div class="col-sm-12">
    {!! Form::label('total', 'Total:') !!}
    <p>{{ $sale->total }}</p>
</div>

<!-- Payment Status Field -->
<div class="col-sm-12">
    {!! Form::label('payment_status', 'Payment Status:') !!}
    <p>{{ $sale->payment_status }}</p>
</div>

