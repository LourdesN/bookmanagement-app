<!-- Book Id Field -->
<div class="col-sm-12">
    {!! Form::label('book_id', 'Book Id:') !!}
    <p>{{ $inventory->book_id }}</p>
</div>

<!-- Quantity Field -->
<div class="col-sm-12">
    {!! Form::label('quantity', 'Quantity:') !!}
    <p>{{ $inventory->quantity }}</p>
</div>

<!-- Location Field -->
<div class="col-sm-12">
    {!! Form::label('location', 'Location:') !!}
    <p>{{ $inventory->location }}</p>
</div>

<!-- Delivery Date Field -->
<div class="col-sm-12">
    {!! Form::label('delivery_date', 'Delivery Date:') !!}
    <p>{{ $inventory->delivery_date }}</p>
</div>

