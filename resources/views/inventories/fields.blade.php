<!-- Book Title Field -->
<div class="form-group col-sm-6">
    {!! Form::label('book_id', 'Book Title:') !!}
    {!! Form::select('book_id', $books, null, ['class' => 'form-control', 'placeholder' => 'Select a Book', 'required']) !!}
</div>

<!-- Quantity Field -->
<div class="form-group col-sm-6">
    {!! Form::label('quantity', 'Quantity:') !!}
    {!! Form::number('quantity', null, ['class' => 'form-control', 'required']) !!}
</div>

<!-- Location Field -->
<div class="form-group col-sm-6">
    {!! Form::label('location', 'Location:') !!}
    {!! Form::text('location', null, ['class' => 'form-control', 'required', 'maxlength' => 255, 'maxlength' => 255]) !!}
</div>

<!-- Delivery Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('delivery_date', 'Delivery Date:') !!}
    {!! Form::date('delivery_date', null, ['class' => 'form-control','id'=>'delivery_date', 'max' => \Carbon\Carbon::now()->format('Y-m-d')]) !!}
</div>

@push('page_scripts')
    <script type="text/javascript">
        $('#delivery_date').datepicker()
    </script>
@endpush