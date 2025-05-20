<!-- Book Id Field -->
<div class="col-sm-12">
    {!! Form::label('book_id', 'Book Id:') !!}
    <p>{{ $delivery->book_id }}</p>
</div>

<!-- Supplier Id Field -->
<div class="col-sm-12">
    {!! Form::label('supplier_id', 'Supplier Id:') !!}
    <p>{{ $delivery->supplier_id }}</p>
</div>

<!-- Quantity Field -->
<div class="col-sm-12">
    {!! Form::label('quantity', 'Quantity:') !!}
    <p>{{ $delivery->quantity }}</p>
</div>

<!-- Delivery Date Field -->
<div class="col-sm-12">
    {!! Form::label('delivery_date', 'Delivery Date:') !!}
    <p>{{ $delivery->delivery_date }}</p>
</div>

