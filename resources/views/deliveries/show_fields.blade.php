<!-- Book Title Field -->
<div class="col-sm-12">
    {!! Form::label('book_id', 'Book Title:') !!}
    <p>{{ $delivery->book->title ?? 'N/A' }}</p>
</div>

<!-- Supplier Name Field -->
<div class="col-sm-12">
    {!! Form::label('supplier_id', 'Supplier Name:') !!}
    <p>{{ $delivery->supplier->first_name . ' ' . $delivery->supplier->last_name ?? 'N/A' }}</p>
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

<!-- Location Field -->
<div class="col-sm-12">
    {!! Form::label('location', 'Location:') !!}
    <p>{{ $delivery->location }}</p>
</div>
