<!-- Book Title Field -->
<div class="form-group col-sm-6">
    {!! Form::label('book_id', 'Book Title:') !!}
    {!! Form::select('book_id', $books, null, ['class' => 'form-control', 'placeholder' => 'Select a Book', 'required']) !!}
</div>

<!-- Supplier Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplier_id', 'Supplier Name:') !!}
    {!! Form::select('supplier_id', $suppliers, null, ['class' => 'form-control', 'placeholder' => 'Select a Supplier', 'required']) !!}
</div>

<!-- Quantity Field -->
<div class="form-group col-sm-6">
    {!! Form::label('quantity', 'Quantity:') !!}
    {!! Form::number('quantity', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Delivery Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('delivery_date', 'Delivery Date:') !!}
    {!! Form::date('delivery_date', null, ['class' => 'form-control','id'=>'delivery_date',  'max' => \Carbon\Carbon::now()->format('Y-m-d')]) !!}
</div>
<!--location details-->
<div class="form-group">
    <label for="location">Location</label>
    <input type="text" name="location" class="form-control" value="{{ old('location', $delivery->location ?? '') }}" required>
</div>


@push('page_scripts')
    <script type="text/javascript">
        $('#delivery_date').datepicker()
    </script>
@endpush