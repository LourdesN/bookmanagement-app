<!-- Book Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('book_id', 'Book Title:') !!}
    {!! Form::select('book_id', $books, null, ['class' => 'form-control', 'placeholder' => 'Select Book', 'required']) !!}
</div>

<!-- Customer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customer_id', 'Customer Name:') !!}
    {!! Form::select('customer_id', $customers, null, ['class' => 'form-control', 'placeholder' => 'Select Customer', 'required']) !!}
</div>

<!-- Quantity Field -->
<div class="form-group col-sm-6">
    {!! Form::label('quantity', 'Quantity:') !!}
    {!! Form::number('quantity', null, ['class' => 'form-control', 'required', 'id' => 'quantity']) !!}
</div>

<!-- Unit Price Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unit_price', 'Unit Price:') !!}
    {!! Form::number('unit_price', null, ['class' => 'form-control', 'required', 'id' => 'unit_price']) !!}
</div>

<!-- Total Field -->
<div class="form-group col-sm-6">
    {!! Form::label('total', 'Total:') !!}
    {!! Form::number('total', null, ['class' => 'form-control', 'readonly' => true, 'id' => 'total']) !!}
</div>


<!-- Payment Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('payment_status', 'Payment Status:') !!}
    {!! Form::text('payment_status', null, ['class' => 'form-control', 'required', 'maxlength' => 65535, 'maxlength' => 65535]) !!}
</div>

<script>
    function calculateTotal() {
        let quantity = parseFloat(document.getElementById('quantity').value) || 0;
        let unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
        document.getElementById('total').value = (quantity * unitPrice).toFixed(2);
    }

    document.getElementById('quantity').addEventListener('input', calculateTotal);
    document.getElementById('unit_price').addEventListener('input', calculateTotal);
</script>
