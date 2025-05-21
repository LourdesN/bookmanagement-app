<!-- Book Id Field -->
<div class="col-sm-12">
    {!! Form::label('book_id', 'Book Title:') !!}
    <p>{{ $sale->book->title }}</p>
</div>

<!-- Customer Id Field -->
<div class="col-sm-12">
    {!! Form::label('customer_id', 'Customer Name:') !!}
    <p>{{ $sale->customer->first_name }}</p>
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

